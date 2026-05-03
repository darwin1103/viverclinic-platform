<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'content',
        'youtube_url',
    ];

    public function getYoutubeIdAttribute()
    {
        if (!$this->youtube_url) return null;
        preg_match('/(youtube\.com\/watch\?v=|youtu\.be\/)([^&]+)/', $this->youtube_url, $matches);
        return $matches[2] ?? null;
    }
}
