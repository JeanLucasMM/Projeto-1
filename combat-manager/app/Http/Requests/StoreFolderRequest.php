<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:80',
            ],

            'subtitle' => [
                'nullable',
                'string',
                'max:120',
            ],

            'color' => [
                'required',
                'string',
                'max:20',
            ],
        ];
    }
}