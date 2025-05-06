<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteAlbum extends Model
{
    protected $fillable = [
        'user_id', 'album_id', 'title', 'artist', 
        'release_date', 'cover_image', 'tracks_count'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}