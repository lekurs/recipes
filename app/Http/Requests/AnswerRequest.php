<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => ['required'],
            'comment' => ['required'],
            'recipe_id' => ['required', 'exists:recipes'],
            'user_id' => ['required', 'exists:users'],
        ];
    }

    public function authorize()
    {
        return true;
    }
}
