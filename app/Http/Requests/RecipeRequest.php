<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'type' => ['required'],
            'title' => ['required'],
            'description' => ['required'],
            'project_id' => ['required', 'exists:projects'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
