<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistTrack extends Model
{
    protected $fillable = [
        'playlist_id', 'track_id', 'title', 'artist', 'album',
        'duration', 'cover_image', 'preview_url', 'position'
    ];
    
    public function playlist()
    {
        return $this->belongsTo(Playlists::class, 'playlist_id');
    }
}