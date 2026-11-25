<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $fillable = ['quote', 'author', 'tags_en', 'tags_nl', 'note_en', 'note_nl', 'folder_id', 'position'];

    protected $casts = [
        'tags_en' => 'array',
        'tags_nl' => 'array',
    ];

    public function folder()
    {
        return $this->belongsTo(QuoteFolder::class, 'folder_id');
    }
}
