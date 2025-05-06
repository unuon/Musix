<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;

class MusicApiController extends Controller
{
    public function endpoints()
    {
        return response()->json([
            'endpoints' => [
                'infos' => 'https://deezerdevs-deezer.p.rapidapi.com/infos',
                'radio' => 'https://deezerdevs-deezer.p.rapidapi.com/radio',
                'genre' => 'https://deezerdevs-deezer.p.rapidapi.com/genre',
                'search' => 'https://deezerdevs-deezer.p.rapidapi.com/search',
                'playlist' => 'https://deezerdevs-deezer.p.rapidapi.com/playlist',
                'artist' => 'https://deezerdevs-deezer.p.rapidapi.com/artist',
                'editorial' => 'https://deezerdevs-deezer.p.rapidapi.com/editorial',
                'track' => 'https://deezerdevs-deezer.p.rapidapi.com/track',
                'comment' => 'https://deezerdevs-deezer.p.rapidapi.com/comment',
                'album' => 'https://deezerdevs-deezer.p.rapidapi.com/album'
            ],
            'host' => config('music-api.api_header')
        ]);
    }

    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            $limit = $request->get('limit', 10);
            
            // Check if API key and header are set
            if (!config('music-api.api_key') || !config('music-api.api_header')) {
                // Return sample data if API credentials are missing
                return response()->json($this->getSampleSearchData($query));
            }
            
            $client = new \GuzzleHttp\Client();
            
            $response = $client->request('GET', 'https://deezerdevs-deezer.p.rapidapi.com/search', [
                'headers' => [
                    'x-rapidapi-host' => config('music-api.api_header'),
                    'x-rapidapi-key' => config('music-api.api_key')
                ],
                'query' => [
                    'q' => $query,
                    'limit' => $limit
                ]
            ]);
            
            return response($response->getBody()->getContents())
                ->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            \Log::error('Music Search API Error: ' . $e->getMessage());
            // Return sample data on error
            return response()->json($this->getSampleSearchData($query));
        }
    }
    
    public function getTrending()
    {
        try {
            // Check if API key and header are set
            if (!config('music-api.api_key') || !config('music-api.api_header')) {
                // Log missing credentials
                \Log::warning('Music API credentials missing. Using sample data.');
                // Return sample data if API credentials are missing
                return response()->json($this->getSampleTrendingData());
            }
            
            $client = new \GuzzleHttp\Client();
            
            $response = $client->request('GET', 'https://deezerdevs-deezer.p.rapidapi.com/search', [
                'headers' => [
                    'x-rapidapi-host' => config('music-api.api_header'),
                    'x-rapidapi-key' => config('music-api.api_key')
                ],
                'query' => [
                    'q' => 'top',
                    'limit' => 8
                ]
            ]);
            
            $content = $response->getBody()->getContents();
            $data = json_decode($content);
            
            return response()->json([
                'tracks' => $data
            ]);
        } catch (\Exception $e) {
            \Log::error('Music Trending API Error: ' . $e->getMessage());
            // Return sample data on error
            return response()->json($this->getSampleTrendingData());
        }
    }
    
    /**
     * Get sample trending data for fallback
     */
    private function getSampleTrendingData()
    {
        return [
            'tracks' => [
                'data' => [
                    [
                        'id' => 1, 
                        'title' => "Shape of You", 
                        'duration' => 240,
                        'preview' => "https://cdns-preview-1.dzcdn.net/stream/c-13039fed16a173733f227b0bec631034-10.mp3",
                        'artist' => ['name' => "Ed Sheeran"],
                        'album' => [
                            'title' => "÷ (Divide)",
                            'cover_small' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/56x56-000000-80-0-0.jpg",
                            'cover_medium' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/250x250-000000-80-0-0.jpg"
                        ]
                    ],
                    [
                        'id' => 2, 
                        'title' => "Blinding Lights", 
                        'duration' => 203,
                        'preview' => "https://cdns-preview-1.dzcdn.net/stream/c-13039fed16a173733f227b0bec631034-10.mp3",
                        'artist' => ['name' => "The Weeknd"],
                        'album' => [
                            'title' => "After Hours",
                            'cover_small' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/56x56-000000-80-0-0.jpg",
                            'cover_medium' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/250x250-000000-80-0-0.jpg"
                        ]
                    ],
                    [
                        'id' => 3, 
                        'title' => "Dance Monkey", 
                        'duration' => 210,
                        'preview' => "https://cdns-preview-1.dzcdn.net/stream/c-13039fed16a173733f227b0bec631034-10.mp3",
                        'artist' => ['name' => "Tones and I"],
                        'album' => [
                            'title' => "The Kids Are Coming",
                            'cover_small' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/56x56-000000-80-0-0.jpg",
                            'cover_medium' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/250x250-000000-80-0-0.jpg"
                        ]
                    ],
                    [
                        'id' => 4, 
                        'title' => "Uptown Funk", 
                        'duration' => 270,
                        'preview' => "https://cdns-preview-1.dzcdn.net/stream/c-13039fed16a173733f227b0bec631034-10.mp3",
                        'artist' => ['name' => "Mark Ronson ft. Bruno Mars"],
                        'album' => [
                            'title' => "Uptown Special",
                            'cover_small' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/56x56-000000-80-0-0.jpg",
                            'cover_medium' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/250x250-000000-80-0-0.jpg"
                        ]
                    ]
                ]
            ]
        ];
    }
    
    /**
     * Get sample search data for fallback
     */
    private function getSampleSearchData($query)
    {
        return [
            'data' => [
                [
                    'id' => 5, 
                    'title' => "Search result for: " . $query, 
                    'duration' => 240,
                    'preview' => "https://cdns-preview-1.dzcdn.net/stream/c-13039fed16a173733f227b0bec631034-10.mp3",
                    'artist' => ['name' => "Sample Artist"],
                    'album' => [
                        'title' => "Sample Album",
                        'cover_small' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/56x56-000000-80-0-0.jpg",
                        'cover_medium' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/250x250-000000-80-0-0.jpg"
                    ]
                ],
                [
                    'id' => 6, 
                    'title' => "Another result for: " . $query, 
                    'duration' => 180,
                    'preview' => "https://cdns-preview-1.dzcdn.net/stream/c-13039fed16a173733f227b0bec631034-10.mp3",
                    'artist' => ['name' => "Another Artist"],
                    'album' => [
                        'title' => "Another Album",
                        'cover_small' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/56x56-000000-80-0-0.jpg",
                        'cover_medium' => "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/250x250-000000-80-0-0.jpg"
                    ]
                ]
            ]
        ];
    }
}