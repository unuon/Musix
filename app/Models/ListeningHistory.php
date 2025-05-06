<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListeningHistory extends Model
{
    protected $table = 'listening_history';
    
    protected $fillable = [
        'user_id', 'track_id', 'title', 'artist', 'album',
        'duration', 'cover_image', 'played_at'
    ];
    
    protected $casts = [
        'played_at' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}