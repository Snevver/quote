<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quote' => $this->quote,
            'author' => $this->author,
            'tags_en' => $this->tags_en ?? [],
            'tags_nl' => $this->tags_nl ?? [],
            'note_en' => $this->note_en,
            'note_nl' => $this->note_nl,
            'folder_id' => $this->folder_id,
            'position' => $this->position,
        ];
    }
}
