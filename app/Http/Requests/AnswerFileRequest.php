<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnswerFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'filename' => ['required'],
            'original_name' => ['required'],
            'size' => ['required', 'integer'],
            'answer_id' => ['required', 'exists:answers'],
            'mime_type' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
