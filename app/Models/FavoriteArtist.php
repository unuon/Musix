<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteArtist extends Model
{
    protected $fillable = [
        'user_id', 'artist_id', 'name', 'image', 
        'albums_count', 'fans'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}