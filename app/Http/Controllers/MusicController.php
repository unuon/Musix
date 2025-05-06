<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Utilities\ApiResponse;
use App\Services\MusicApiService;
use Illuminate\Http\Request;

class MusicController extends Controller
{
    protected $musicService;
    
    public function __construct(MusicApiService $musicService)
    {
        $this->musicService = $musicService;
        $this->middleware('jwt.auth')->except(['search', 'genres', 'track', 'album', 'artist']);
    }
    
    public function search(Request $request)
    {
        $request->validate([
            'q' => 'required|string',
            'limit' => 'sometimes|integer|min:1|max:50',
            'index' => 'sometimes|integer|min:0'
        ]);
        
        $results = $this->musicService->search(
            $request->q,
            $request->limit ?? 25,
            $request->index ?? 0
        );
        
        return ApiResponse::success($results);
    }
    
    public function track($id)
    {
        $track = $this->musicService->getTrack($id);
        
        if (empty($track) || isset($track['error'])) {
            return ApiResponse::notFound('Track not found');
        }
        
        return ApiResponse::success($track);
    }
    
    public function album($id)
    {
        $album = $this->musicService->getAlbum($id);
        
        if (empty($album) || isset($album['error'])) {
            return ApiResponse::notFound('Album not found');
        }
        
        return ApiResponse::success($album);
    }
    
    public function artist($id)
    {
        $artist = $this->musicService->getArtist($id);
        
        if (empty($artist) || isset($artist['error'])) {
            return ApiResponse::notFound('Artist not found');
        }
        
        return ApiResponse::success($artist);
    }
    
    public function artistTopTracks($id)
    {
        $tracks = $this->musicService->getArtistTopTracks($id);
        
        if (empty($tracks) || isset($tracks['error'])) {
            return ApiResponse::notFound('Artist or tracks not found');
        }
        
        return ApiResponse::success($tracks);
    }
    
    public function playlist($id)
    {
        $playlist = $this->musicService->getPlaylist($id);
        
        if (empty($playlist) || isset($playlist['error'])) {
            return ApiResponse::notFound('Playlist not found');
        }
        
        return ApiResponse::success($playlist);
    }
    
    public function genres()
    {
        $genres = $this->musicService->getGenres();
        return ApiResponse::success($genres);
    }
    
    public function genreDetails($id)
    {
        $genre = $this->musicService->getGenreDetails($id);
        
        if (empty($genre) || isset($genre['error'])) {
            return ApiResponse::notFound('Genre not found');
        }
        
        return ApiResponse::success($genre);
    }
    
    public function radio()
    {
        $stations = $this->musicService->getRadioStations();
        return ApiResponse::success($stations);
    }
    
    public function radioTracks($id)
    {
        $tracks = $this->musicService->getRadioTracks($id);
        
        if (empty($tracks) || isset($tracks['error'])) {
            return ApiResponse::notFound('Radio station not found');
        }
        
        return ApiResponse::success($tracks);
    }
    
    public function editorial()
    {
        $editorial = $this->musicService->getEditorial();
        return ApiResponse::success($editorial);
    }
    
    public function editorialDetails($id)
    {
        $details = $this->musicService->getEditorialDetails($id);
        
        if (empty($details) || isset($details['error'])) {
            return ApiResponse::notFound('Editorial content not found');
        }
        
        return ApiResponse::success($details);
    }
}