<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlists extends Model
{
    protected $fillable = [
        'user_id', 'name', 'description', 'is_public', 'cover_image'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function tracks()
    {
        return $this->hasMany(PlaylistTrack::class, 'playlist_id')->orderBy('position');
    }
}