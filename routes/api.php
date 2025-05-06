<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MusicController;
use App\Http\Auth\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserMusicController;
use App\Http\Controllers\MusicApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API v1 Routes
Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        
        // Protected auth routes
        Route::middleware(AuthMiddleware::class)->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::get('user', [AuthController::class, 'user']);
        });
    });

    // Music API Routes
    Route::prefix('music')->group(function () {
        // Public routes (no authentication required)
        Route::get('search', [MusicController::class, 'search']);
        Route::get('track/{id}', [MusicController::class, 'track']);
        Route::get('album/{id}', [MusicController::class, 'album']);
        Route::get('artist/{id}', [MusicController::class, 'artist']);
        Route::get('artist/{id}/top', [MusicController::class, 'artistTopTracks']);
        Route::get('playlist/{id}', [MusicController::class, 'playlist']);
        Route::get('genres', [MusicController::class, 'genres']);
        Route::get('genre/{id}', [MusicController::class, 'genreDetails']);
        Route::get('radio', [MusicController::class, 'radio']);
        Route::get('radio/{id}/tracks', [MusicController::class, 'radioTracks']);
        Route::get('editorial', [MusicController::class, 'editorial']);
        Route::get('editorial/{id}', [MusicController::class, 'editorialDetails']);
        
        // Protected routes (authentication required)
        Route::middleware(AuthMiddleware::class)->group(function () {
            // User-specific music features will go here
            // Example: Route::get('favorites', [UserMusicController::class, 'favorites']);
        });
    });

    // User music features (protected routes)
    Route::middleware(AuthMiddleware::class)->prefix('user')->group(function () {
        // Favorites
        Route::get('favorites/tracks', [UserMusicController::class, 'favoriteTracks']);
        Route::post('favorites/tracks/{trackId}', [UserMusicController::class, 'addFavoriteTrack']);
        Route::delete('favorites/tracks/{trackId}', [UserMusicController::class, 'removeFavoriteTrack']);
        
        // Playlists
        Route::get('playlists', [UserMusicController::class, 'playlists']);
        Route::post('playlists', [UserMusicController::class, 'createPlaylist']);
        Route::put('playlists/{id}', [UserMusicController::class, 'updatePlaylist']);
        Route::delete('playlists/{id}', [UserMusicController::class, 'deletePlaylist']);
        
        // Playlist tracks
        Route::post('playlists/{playlistId}/tracks/{trackId}', [UserMusicController::class, 'addTrackToPlaylist']);
        Route::delete('playlists/{playlistId}/tracks/{trackId}', [UserMusicController::class, 'removeTrackFromPlaylist']);
        
        // History
        Route::get('history', [UserMusicController::class, 'listeningHistory']);
        Route::post('history/{trackId}', [UserMusicController::class, 'addToHistory']);
    });

    // New routes for Music API
    Route::prefix('music-api')->group(function () {
        Route::get('/endpoints', [MusicApiController::class, 'endpoints']);
        Route::get('/search', [MusicApiController::class, 'search']);
        Route::get('/trending', [MusicApiController::class, 'getTrending']);
    });
});