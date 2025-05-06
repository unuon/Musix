<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MusicApiService
{
    private $apiKey;
    private $apiHost;
    
    public function __construct()
    {
        $this->apiKey = env('RAPID_API_KEY', '0bf7a8a67emshe5eea2d713adc1cp1d133ajsn0ebfbf4bebe1');
        $this->apiHost = 'deezerdevs-deezer.p.rapidapi.com';
    }
    
    private function getHeaders()
    {
        return [
            'x-rapidapi-host' => $this->apiHost,
            'x-rapidapi-key' => $this->apiKey
        ];
    }
    
    /**
     * Get music service information
     */
    public function getInfos()
    {
        return Cache::remember('music_infos', 3600, function () {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_INFOS'))
                ->json();
        });
    }
    
    /**
     * Search for tracks, albums, artists, etc.
     */
    public function search($query, $limit = 25, $index = 0)
    {
        return Http::withHeaders($this->getHeaders())
            ->get(env('MUSIC_API_SEARCH'), [
                'q' => $query,
                'limit' => $limit,
                'index' => $index
            ])
            ->json();
    }
    
    /**
     * Get track details by ID
     */
    public function getTrack($trackId)
    {
        $cacheKey = "track_{$trackId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($trackId) {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_TRACK') . '/' . $trackId)
                ->json();
        });
    }
    
    /**
     * Get album details by ID
     */
    public function getAlbum($albumId)
    {
        $cacheKey = "album_{$albumId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($albumId) {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_APP_ALBUM') . '/' . $albumId)
                ->json();
        });
    }
    
    /**
     * Get artist details by ID
     */
    public function getArtist($artistId)
    {
        $cacheKey = "artist_{$artistId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($artistId) {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_ARTIST') . '/' . $artistId)
                ->json();
        });
    }
    
    /**
     * Get artist top tracks
     */
    public function getArtistTopTracks($artistId, $limit = 10)
    {
        $cacheKey = "artist_{$artistId}_top_tracks";
        
        return Cache::remember($cacheKey, 3600, function () use ($artistId) {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_ARTIST') . '/' . $artistId . '/top')
                ->json();
        });
    }
    
    /**
     * Get playlist details by ID
     */
    public function getPlaylist($playlistId)
    {
        $cacheKey = "playlist_{$playlistId}";
        
        return Cache::remember($cacheKey, 1800, function () use ($playlistId) {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_PLAYLIST') . '/' . $playlistId)
                ->json();
        });
    }
    
    /**
     * Get all music genres
     */
    public function getGenres()
    {
        return Cache::remember('music_genres', 86400, function () {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_GENRE'))
                ->json();
        });
    }
    
    /**
     * Get genre details by ID
     */
    public function getGenreDetails($genreId)
    {
        $cacheKey = "genre_{$genreId}";
        
        return Cache::remember($cacheKey, 86400, function () use ($genreId) {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_GENRE') . '/' . $genreId)
                ->json();
        });
    }
    
    /**
     * Get radio stations
     */
    public function getRadioStations()
    {
        return Cache::remember('radio_stations', 43200, function () {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_RADIO'))
                ->json();
        });
    }
    
    /**
     * Get radio tracks by ID
     */
    public function getRadioTracks($radioId)
    {
        return Http::withHeaders($this->getHeaders())
            ->get(env('MUSIC_API_RADIO') . '/' . $radioId . '/tracks')
            ->json();
    }
    
    /**
     * Get editorial content
     */
    public function getEditorial()
    {
        return Cache::remember('editorial', 43200, function () {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_EDITORIAL'))
                ->json();
        });
    }
    
    /**
     * Get editorial details by ID
     */
    public function getEditorialDetails($editorialId)
    {
        $cacheKey = "editorial_{$editorialId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($editorialId) {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_EDITORIAL') . '/' . $editorialId)
                ->json();
        });
    }
    
    /**
     * Get chart data
     */
    public function getChart()
    {
        return Cache::remember('chart', 3600, function () {
            return Http::withHeaders($this->getHeaders())
                ->get(env('MUSIC_API_CHART'))
                ->json();
        });
    }
    
    /**
     * Get comments for a track
     */
    public function getTrackComments($trackId)
    {
        return Http::withHeaders($this->getHeaders())
            ->get(env('MUSIC_APP_COMMENT') . '/track/' . $trackId)
            ->json();
    }
    
    /**
     * Get comments for an album
     */
    public function getAlbumComments($albumId)
    {
        return Http::withHeaders($this->getHeaders())
            ->get(env('MUSIC_APP_COMMENT') . '/album/' . $albumId)
            ->json();
    }
    
    /**
     * Search with advanced parameters
     */
    public function advancedSearch($options = [])
    {
        $params = array_merge([
            'q' => '',
            'limit' => 25,
            'index' => 0
        ], $options);
        
        return Http::withHeaders($this->getHeaders())
            ->get(env('MUSIC_API_SEARCH'), $params)
            ->json();
    }
}