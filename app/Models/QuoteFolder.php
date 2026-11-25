<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteFolder extends Model
{
    protected $fillable = ['name', 'parent_id', 'position'];

    public function parent()
    {
        return $this->belongsTo(QuoteFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(QuoteFolder::class, 'parent_id')->orderBy('position');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'folder_id')->orderBy('position');
    }
}
