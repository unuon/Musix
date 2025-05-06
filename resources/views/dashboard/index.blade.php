@extends('app')

@section('title', 'Music Dashboard')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, {{ $user->name }}</h1>
        <div class="header-actions">
            <p class="last-login">Last login: {{ now()->format('M d, Y H:i') }}</p>
            <button id="logout-button" class="logout-button">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Logout
            </button>
        </div>
    </div>

    <div class="dashboard-layout">
        <div class="main-content">
            <div class="dashboard-card search-card">
                <h2>Search Music</h2>
                <div class="search-container">
                    <input type="text" id="music-search" placeholder="Search for songs, artists, or albums..." class="search-input">
                    <button id="search-button" class="search-button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </div>
                <div id="search-results" class="search-results-container">
                    <!-- Search results will appear here -->
                </div>
            </div>

            <div class="dashboard-card">
                <h2>Trending Now</h2>
                <div id="trending-tracks" class="music-grid">
                    <div class="loading-state">
                        <div class="spinner"></div>
                        <p>Loading trending tracks...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="player-sidebar">
            <div class="dashboard-card player-card">
                <div id="music-player" class="music-player">
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polygon points="10 8 16 12 10 16 10 8"></polygon>
                        </svg>
                        <p>Select a track to play</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-card">
                <h2>Recently Played</h2>
                <div id="recent-tracks" class="recent-tracks">
                    <div class="empty-state">
                        <p>Your recently played tracks will appear here</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/music-api.js') }}" defer></script>
<script src="{{ asset('js/dashboard.js') }}" defer></script>
@endsection