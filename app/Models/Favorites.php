<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteTrack extends Model
{
    protected $fillable = [
        'user_id', 'track_id', 'title', 'artist', 'album', 
        'duration', 'cover_image', 'preview_url'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}