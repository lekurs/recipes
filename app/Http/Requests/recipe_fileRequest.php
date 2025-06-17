<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class recipe_fileRequest extends FormRequest
{
    public function rules()
    {
        return [
            'filename' => ['required'],
            'original_name' => ['required'],
            'size' => ['required'],
            'mime_type' => ['required'],
            'recipe_id' => ['required', 'exists:recipes'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
