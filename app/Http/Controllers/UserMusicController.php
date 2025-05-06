<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Utilities\ApiResponse;
use App\Models\FavoriteTrack;
use App\Models\FavoriteAlbum;
use App\Models\FavoriteArtist;
use App\Models\Playlists;
use App\Models\PlaylistTrack;
use App\Models\ListeningHistory;
use App\Services\MusicApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserMusicController extends Controller
{
    protected $musicService;
    
    public function __construct(MusicApiService $musicService)
    {
        $this->musicService = $musicService;
    }
    
    /**
     * Get user's favorite tracks
     */
    public function favoriteTracks()
    {
        $tracks = Auth::user()->favoriteTracks()->orderBy('created_at', 'desc')->get();
        return ApiResponse::success($tracks);
    }
    
    /**
     * Add a track to user's favorites
     */
    public function addFavoriteTrack(Request $request, $trackId)
    {
        // Check if already favorited
        $existing = Auth::user()->favoriteTracks()->where('track_id', $trackId)->first();
        if ($existing) {
            return ApiResponse::success($existing, 'Track already in favorites');
        }
        
        // Get track data from API
        $trackData = $this->musicService->getTrack($trackId);
        if (empty($trackData) || isset($trackData['error'])) {
            return ApiResponse::notFound('Track not found');
        }
        
        // Create favorite
        $favorite = Auth::user()->favoriteTracks()->create([
            'track_id' => $trackId,
            'title' => $trackData['title'] ?? '',
            'artist' => $trackData['artist']['name'] ?? null,
            'album' => $trackData['album']['title'] ?? null,
            'duration' => $trackData['duration'] ?? null,
            'cover_image' => $trackData['album']['cover_medium'] ?? null,
            'preview_url' => $trackData['preview'] ?? null,
        ]);
        
        return ApiResponse::success($favorite, 'Track added to favorites');
    }
    
    /**
     * Remove a track from user's favorites
     */
    public function removeFavoriteTrack($trackId)
    {
        $deleted = Auth::user()->favoriteTracks()->where('track_id', $trackId)->delete();
        
        if ($deleted) {
            return ApiResponse::success(null, 'Track removed from favorites');
        }
        
        return ApiResponse::notFound('Track not found in favorites');
    }
    
    /**
     * Get user's playlists
     */
    public function playlists()
    {
        $playlists = Auth::user()->playlists()->with('tracks')->get();
        return ApiResponse::success($playlists);
    }
    
    /**
     * Create a new playlist
     */
    public function createPlaylist(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);
        
        $playlist = Auth::user()->playlists()->create([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->is_public ?? false,
        ]);
        
        return ApiResponse::success($playlist, 'Playlist created');
    }
    
    /**
     * Update a playlist
     */
    public function updatePlaylist(Request $request, $id)
    {
        $playlist = Auth::user()->playlists()->findOrFail($id);
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);
        
        $playlist->update($request->only(['name', 'description', 'is_public']));
        
        return ApiResponse::success($playlist, 'Playlist updated');
    }
    
    /**
     * Delete a playlist
     */
    public function deletePlaylist($id)
    {
        $playlist = Auth::user()->playlists()->findOrFail($id);
        $playlist->delete();
        
        return ApiResponse::success(null, 'Playlist deleted');
    }
    
    /**
     * Add a track to a playlist
     */
    public function addTrackToPlaylist(Request $request, $playlistId, $trackId)
    {
        $playlist = Auth::user()->playlists()->findOrFail($playlistId);
        
        // Check if track already in playlist
        $existing = $playlist->tracks()->where('track_id', $trackId)->first();
        if ($existing) {
            return ApiResponse::success($existing, 'Track already in playlist');
        }
        
        // Get track data from API
        $trackData = $this->musicService->getTrack($trackId);
        if (empty($trackData) || isset($trackData['error'])) {
            return ApiResponse::notFound('Track not found');
        }
        
        // Get highest position
        $maxPosition = $playlist->tracks()->max('position') ?? 0;
        
        // Add track to playlist
        $playlistTrack = $playlist->tracks()->create([
            'track_id' => $trackId,
            'title' => $trackData['title'] ?? '',
            'artist' => $trackData['artist']['name'] ?? null,
            'album' => $trackData['album']['title'] ?? null,
            'duration' => $trackData['duration'] ?? null,
            'cover_image' => $trackData['album']['cover_medium'] ?? null,
            'preview_url' => $trackData['preview'] ?? null,
            'position' => $maxPosition + 1,
        ]);
        
        return ApiResponse::success($playlistTrack, 'Track added to playlist');
    }
    
    /**
     * Remove a track from a playlist
     */
    public function removeTrackFromPlaylist($playlistId, $trackId)
    {
        $playlist = Auth::user()->playlists()->findOrFail($playlistId);
        
        $deleted = $playlist->tracks()->where('track_id', $trackId)->delete();
        
        if ($deleted) {
            // Reorder positions
            $tracks = $playlist->tracks()->orderBy('position')->get();
            foreach ($tracks as $index => $track) {
                $track->update(['position' => $index + 1]);
            }
            
            return ApiResponse::success(null, 'Track removed from playlist');
        }
        
        return ApiResponse::notFound('Track not found in playlist');
    }
    
    /**
     * Get user's listening history
     */
    public function listeningHistory(Request $request)
    {
        $limit = $request->query('limit', 50);
        $history = Auth::user()->listeningHistory()->limit($limit)->get();
        
        return ApiResponse::success($history);
    }
    
    /**
     * Add track to listening history
     */
    public function addToHistory(Request $request, $trackId)
    {
        // Get track data from API
        $trackData = $this->musicService->getTrack($trackId);
        if (empty($trackData) || isset($trackData['error'])) {
            return ApiResponse::notFound('Track not found');
        }
        
        // Add to history
        $historyEntry = Auth::user()->listeningHistory()->create([
            'track_id' => $trackId,
            'title' => $trackData['title'] ?? '',
            'artist' => $trackData['artist']['name'] ?? null,
            'album' => $trackData['album']['title'] ?? null,
            'duration' => $trackData['duration'] ?? null,
            'cover_image' => $trackData['album']['cover_medium'] ?? null,
            'played_at' => now(),
        ]);
        
        return ApiResponse::success($historyEntry, 'Added to history');
    }
}