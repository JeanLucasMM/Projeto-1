<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Character;
use App\Services\Campaigns\CampaignLiveStateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CampaignMasterController extends Controller
{
    public function __construct(
        private CampaignLiveStateService $liveState
    ) {
    }

    public function live(
        Request $request,
        Campaign $campaign
    ): JsonResponse {
        abort_unless(
            $campaign->isOwner($request->user()),
            403
        );

        return response()->json([
            'success' => true,
            ...$this->liveState->masterShield($campaign),
        ]);
    }

    public function characterImage(
        Request $request,
        Campaign $campaign,
        Character $character
    ): BinaryFileResponse {
        abort_unless(
            $campaign->isOwner(
                $request->user()
            ),
            403
        );

        $isShared =
            $campaign
                ->characters()
                ->whereKey(
                    $character->id
                )
                ->exists();

        abort_unless(
            $isShared,
            404
        );

        abort_unless(
            is_string(
                $character->image_path
            )
            &&
            trim(
                $character->image_path
            )
            !==
            '',
            404
        );

        $relativePath =
            ltrim(
                str_replace(
                    '\\',
                    '/',
                    trim(
                        $character->image_path
                    )
                ),
                '/'
            );

        abort_if(
            str_contains(
                $relativePath,
                '../'
            )
            ||
            str_contains(
                $relativePath,
                '..\\'
            ),
            404
        );

        $fullPath =
            storage_path(
                'app/public/'
                .
                $relativePath
            );

        abort_unless(
            is_file(
                $fullPath
            ),
            404
        );

        return response()->file(
            $fullPath,
            [
                'Cache-Control' =>
                    'private, no-cache, no-store, must-revalidate',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',

                'X-Content-Type-Options' =>
                    'nosniff',
            ]
        );
    }

}