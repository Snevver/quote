<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quote' => 'sometimes|string',
            'author' => 'sometimes|string',
            'tags_en' => 'nullable|array',
            'tags_nl' => 'nullable|array',
            'note_en' => 'nullable|string',
            'note_nl' => 'nullable|string',
            'folder_id' => 'nullable|integer',
            'position' => 'nullable|integer',
        ];
    }
}
