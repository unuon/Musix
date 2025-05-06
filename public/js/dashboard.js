document.addEventListener('DOMContentLoaded', function() {
    // Check if user is authenticated
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = '/login';
        return;
    }
    
    // Setup logout button
    setupLogout();
    
    // Get user data from localStorage and update display
    const userData = localStorage.getItem('user');
    if (userData) {
        const user = JSON.parse(userData);
        
        // Update username in header
        const welcomeHeader = document.querySelector('.dashboard-header h1');
        if (welcomeHeader && user.name) {
            welcomeHeader.textContent = `Welcome, ${user.name}`;
        }
    }
    
    // Recently played tracks storage
    let recentlyPlayed = [];
    // Try to get recently played from localStorage
    const storedRecent = localStorage.getItem('recentlyPlayed');
    if (storedRecent) {
        try {
            recentlyPlayed = JSON.parse(storedRecent);
        } catch (e) {
            console.error('Error parsing recently played tracks', e);
        }
    }
    
    // Initialize dashboard components
    initMusicFeatures();
    
    // Function to format duration in mm:ss
    function formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
    }
    
    // Function to create a track element
    function createTrackElement(track) {
        const trackItem = document.createElement('div');
        trackItem.className = 'track-item';
        trackItem.dataset.trackId = track.id;
        
        trackItem.innerHTML = `
            <img src="${track.album.cover_small || 'https://via.placeholder.com/50'}" alt="${track.title}" class="track-img">
            <div class="track-details">
                <div class="track-title">${track.title}</div>
                <div class="track-artist">${track.artist.name}</div>
            </div>
            <div class="track-duration">${formatDuration(track.duration)}</div>
            <div class="track-action">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
            </div>
        `;
        
        // Add click event to play the track
        trackItem.addEventListener('click', function() {
            playTrack(track);
        });
        
        return trackItem;
    }
    
    // Function to add a track to recently played
    function addToRecentlyPlayed(track) {
        // Check if track is already in recently played
        const index = recentlyPlayed.findIndex(t => t.id === track.id);
        if (index !== -1) {
            // Remove it from current position
            recentlyPlayed.splice(index, 1);
        }
        
        // Add track to the beginning
        recentlyPlayed.unshift(track);
        
        // Limit to 5 tracks
        if (recentlyPlayed.length > 5) {
            recentlyPlayed = recentlyPlayed.slice(0, 5);
        }
        
        // Save to localStorage
        localStorage.setItem('recentlyPlayed', JSON.stringify(recentlyPlayed));
        
        // Update the UI
        updateRecentlyPlayed();
    }
    
    // Function to update recently played UI
    function updateRecentlyPlayed() {
        const recentContainer = document.getElementById('recent-tracks');
        
        if (recentlyPlayed.length === 0) {
            recentContainer.innerHTML = `
                <div class="empty-state">
                    <p>Your recently played tracks will appear here</p>
                </div>
            `;
            return;
        }
        
        recentContainer.innerHTML = '';
        recentlyPlayed.forEach(track => {
            recentContainer.appendChild(createTrackElement(track));
        });
    }
    
    // Function to play a track
    function playTrack(track) {
        // Add to recently played first
        addToRecentlyPlayed(track);
        
        const player = document.getElementById('music-player');
        
        // Format duration
        const minutes = Math.floor(track.duration / 60);
        const seconds = Math.floor(track.duration % 60);
        const durationFormatted = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        
        // Update player with track info
        player.innerHTML = `
            <div class="now-playing">NOW PLAYING</div>
            <h3 class="player-title">${track.title}</h3>
            <div class="player-artist">${track.artist.name}</div>
            
            <div class="player-album-art">
                <img src="${track.album.cover_medium || 'https://via.placeholder.com/200'}" alt="${track.title}" style="width: 120px; height: 120px; border-radius: 8px;">
            </div>
            
            <div class="player-duration">
                <span class="current-time">0:00</span>
                <div class="progress-bar">
                    <div class="progress-filled"></div>
                </div>
                <span class="total-time">${durationFormatted}</span>
            </div>
            
            <div class="preview-badge">30-second preview</div>
            
            <div class="player-controls">
                <button class="player-control-btn previous">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="19 20 9 12 19 4 19 20"></polygon>
                        <line x1="5" y1="19" x2="5" y2="5"></line>
                    </svg>
                </button>
                <button class="player-control-btn play-pause" id="play-pause-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="play-icon">
                        <polygon points="5 3 19 12 5 21 5 3"></polygon>
                    </svg>
                </button>
                <button class="player-control-btn next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="5 4 15 12 5 20 5 4"></polygon>
                        <line x1="19" y1="5" x2="19" y2="19"></line>
                    </svg>
                </button>
            </div>
        `;
        
        // Create and add audio element separately
        const audioElement = document.createElement('audio');
        audioElement.id = 'audio-player';
        audioElement.src = track.preview;
        audioElement.preload = 'auto';
        player.appendChild(audioElement);
        
        // Handle audio controls
        const audio = document.getElementById('audio-player');
        const playPauseBtn = document.getElementById('play-pause-btn');
        const progressFilled = document.querySelector('.progress-filled');
        const currentTimeEl = document.querySelector('.current-time');
        
        // Define play and pause icons
        const playIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="play-icon">
                <polygon points="5 3 19 12 5 21 5 3"></polygon>
            </svg>
        `;
        
        const pauseIcon = `
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pause-icon">
                <rect x="6" y="4" width="4" height="16"></rect>
                <rect x="14" y="4" width="4" height="16"></rect>
            </svg>
        `;
        
        // Function to update play/pause button
        function updatePlayPauseButton(isPlaying) {
            playPauseBtn.innerHTML = isPlaying ? pauseIcon : playIcon;
        }
        
        // Add click event for play/pause
        playPauseBtn.addEventListener('click', function() {
            if (audio.paused) {
                audio.play().catch(e => {
                    console.error('Error playing audio:', e);
                    if (window.notifications) {
                        window.notifications.error('Playback Error', 'Could not play this track preview.');
                    }
                });
                updatePlayPauseButton(true);
            } else {
                audio.pause();
                updatePlayPauseButton(false);
            }
        });
        
        // Update progress bar
        audio.addEventListener('timeupdate', () => {
            const percent = (audio.currentTime / audio.duration) * 100;
            progressFilled.style.width = `${percent}%`;
            
            // Update current time
            const currentMinutes = Math.floor(audio.currentTime / 60);
            const currentSeconds = Math.floor(audio.currentTime % 60);
            currentTimeEl.textContent = `${currentMinutes}:${currentSeconds < 10 ? '0' : ''}${currentSeconds}`;
        });
        
        // Make progress bar clickable
        const progressBar = document.querySelector('.progress-bar');
        progressBar.addEventListener('click', (e) => {
            const percent = e.offsetX / progressBar.offsetWidth;
            audio.currentTime = percent * audio.duration;
        });
        
        // Listen for play event to update button
        audio.addEventListener('play', () => {
            updatePlayPauseButton(true);
        });
        
        // Listen for pause event to update button
        audio.addEventListener('pause', () => {
            updatePlayPauseButton(false);
        });
        
        // Handle end of preview
        audio.addEventListener('ended', () => {
            updatePlayPauseButton(false);
        });
        
        // Auto play (but with a short delay to avoid issues)
        setTimeout(() => {
            audio.play().then(() => {
                updatePlayPauseButton(true);
            }).catch(e => {
                console.error('Error auto-playing audio:', e);
                updatePlayPauseButton(false);
            });
        }, 100);
    }
    
    // Function to initialize music features
    function initMusicFeatures() {
        if (!window.musicAPI) {
            console.error('Music API not available!');
            if (window.notifications) {
                window.notifications.error('API Error', 'Music API not available');
            }
            return;
        }
        
        // Load trending tracks
        loadTrendingTracks();
        
        // Set up search functionality
        setupSearch();
        
        // Initialize recently played
        updateRecentlyPlayed();
    }
    
    // Function to load trending tracks
    async function loadTrendingTracks() {
        const trendingContainer = document.getElementById('trending-tracks');
        
        try {
            const data = await window.musicAPI.getTrendingTracks();
            
            if (data.error) {
                trendingContainer.innerHTML = `
                    <div class="empty-state">
                        <p>Unable to load trending tracks: ${data.message}</p>
                    </div>
                `;
                return;
            }
            
            if (data.tracks && data.tracks.data && data.tracks.data.length > 0) {
                trendingContainer.innerHTML = '';
                
                // Display the first 8 tracks
                const tracks = data.tracks.data.slice(0, 8);
                tracks.forEach(track => {
                    trendingContainer.appendChild(createTrackElement(track));
                });
            } else {
                trendingContainer.innerHTML = `
                    <div class="empty-state">
                        <p>No trending tracks available right now.</p>
                        <button class="discover-button">Refresh</button>
                    </div>
                `;
            }
        } catch (error) {
            console.error('Error loading trending tracks:', error);
            trendingContainer.innerHTML = `
                <div class="empty-state">
                    <p>Failed to load trending tracks.</p>
                    <button class="discover-button">Try Again</button>
                </div>
            `;
        }
    }
    
    // Function to set up search functionality
    function setupSearch() {
        const searchInput = document.getElementById('music-search');
        const searchButton = document.getElementById('search-button');
        const resultsContainer = document.getElementById('search-results');
        
        searchButton.addEventListener('click', async function() {
            const query = searchInput.value.trim();
            if (query.length < 2) {
                if (window.notifications) {
                    window.notifications.warning('Search Error', 'Please enter at least 2 characters');
                }
                return;
            }
            
            // Show loading state
            resultsContainer.innerHTML = `
                <div class="loading-state">
                    <div class="spinner"></div>
                    <p>Searching for "${query}"...</p>
                </div>
            `;
            
            try {
                const results = await window.musicAPI.searchMusic(query);
                
                if (results.error) {
                    resultsContainer.innerHTML = `
                        <div class="empty-state">
                            <p>Error searching: ${results.message}</p>
                        </div>
                    `;
                    return;
                }
                
                if (results.data && results.data.length > 0) {
                    resultsContainer.innerHTML = '';
                    results.data.forEach(track => {
                        resultsContainer.appendChild(createTrackElement(track));
                    });
                } else {
                    resultsContainer.innerHTML = `
                        <div class="empty-state">
                            <p>No results found for "${query}"</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Search error:', error);
                resultsContainer.innerHTML = `
                    <div class="empty-state">
                        <p>Failed to perform search. Please try again.</p>
                    </div>
                `;
            }
        });
        
        // Allow search on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });
    }
    
    // Function to set up logout button
    function setupLogout() {
        const logoutButton = document.getElementById('logout-button');
        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                // Remove authentication data from localStorage
                localStorage.removeItem('auth_token');
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                
                // Optionally, also remove recently played tracks
                localStorage.removeItem('recentlyPlayed');
                
                // Show notification if available
                if (window.notifications) {
                    window.notifications.success('Logged Out', 'You have been successfully logged out.');
                }
                
                // Small delay to show the notification before redirecting
                setTimeout(() => {
                    // Redirect to login page
                    window.location.href = '/login';
                }, 1000);
            });
        }
    }
});