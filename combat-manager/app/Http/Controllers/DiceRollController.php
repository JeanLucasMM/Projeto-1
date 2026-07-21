<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InvalidArgumentException;
use App\Services\Dice\DiceRoller; // Caminho corrigido
use App\Services\Dice\DiceFormatter;

class DiceRollController extends Controller
{
    public function roll(Request $request, DiceRoller $dice, DiceFormatter $formatter)
    {
        $request->validate([
            'expression' => ['required', 'string']
        ]);

        try {
            $result = $dice->roll($request->expression);

            return response()->json([
                'success' => true,
                'data' => $result, // Retorna o seu DTO (o Laravel converte para JSON)
                'formatted' => $formatter->format($result) // Adiciona a string amigável!
            ]);
            
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}