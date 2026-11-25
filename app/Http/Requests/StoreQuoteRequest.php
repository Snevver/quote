<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quote' => 'required|string',
            'author' => 'required|string',
            'tags_en' => 'nullable|array',
            'tags_nl' => 'nullable|array',
            'note_en' => 'nullable|string',
            'note_nl' => 'nullable|string',
            'folder_id' => 'nullable|exists:quote_folders,id',
            'position' => 'nullable|integer',
        ];
    }
}
