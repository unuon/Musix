class MusicAPI {
    constructor() {
        this.initialized = false;
        this.endpoints = {};
        this.apiHost = '';
        this.cache = {};
        this.requestQueue = [];
        this.isProcessingQueue = false;
        
        // These fallback methods allow the app to work while API issues are resolved
        this.fallbackData = {
            trending: {
                tracks: {
                    data: [
                        {
                            id: 1, 
                            title: "Sample Track", 
                            duration: 180,
                            preview: "https://cdns-preview-1.dzcdn.net/stream/c-13039fed16a173733f227b0bec631034-10.mp3",
                            artist: { name: "Sample Artist" },
                            album: { 
                                title: "Sample Album",
                                cover_small: "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/56x56-000000-80-0-0.jpg",
                                cover_medium: "https://e-cdns-images.dzcdn.net/images/cover/a794d5965f2a8a86e7f2f894f7c9c690/250x250-000000-80-0-0.jpg"
                            }
                        }
                    ]
                }
            }
        };
        
        // Load endpoints from server
        this.loadEndpoints();
    }
    
    async loadEndpoints() {
        try {
            // Updated URL to match the route prefix
            const response = await fetch('/api/v1/music-api/endpoints');
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }
            const data = await response.json();
            this.endpoints = data.endpoints;
            this.apiHost = data.host;
            this.initialized = true;
            console.log('Music API initialized successfully');
        } catch (error) {
            console.error('Failed to load music API endpoints:', error);
            // Use fallback data when API fails
            this.useFallbackMode();
        }
    }
    
    useFallbackMode() {
        if (window.notifications) {
            window.notifications.warning('API Connection Issue', 'Using demo data while connecting to music service');
        }
        // Set a flag to indicate we're in fallback mode
        this.fallbackMode = true;
    }

    async queueRequest(url, options) {
        // Check cache first
        if (this.cache[url]) {
            return this.cache[url];
        }
        
        // Queue the request
        return new Promise((resolve, reject) => {
            this.requestQueue.push({
                url,
                options,
                resolve,
                reject
            });
            
            if (!this.isProcessingQueue) {
                this.processQueue();
            }
        });
    }
    
    async processQueue() {
        if (this.requestQueue.length === 0) {
            this.isProcessingQueue = false;
            return;
        }
        
        this.isProcessingQueue = true;
        const request = this.requestQueue.shift();
        
        try {
            // Wait 1 second between requests to avoid rate limiting
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            const response = await fetch(request.url, request.options);
            const data = await response.json();
            
            // Cache the result
            this.cache[request.url] = data;
            
            request.resolve(data);
        } catch (error) {
            request.reject(error);
        }
        
        // Process next request
        this.processQueue();
    }

    getRequestOptions() {
        return {
            method: 'GET',
            headers: {
                'x-rapidapi-host': this.apiHost,
                'x-rapidapi-key': this.apiKey
            }
        };
    }

    async searchMusic(query, limit = 5) {
        if (!query || query.trim() === '') {
            return { data: [] };
        }
        
        // Return fallback data if in fallback mode
        if (this.fallbackMode) {
            return { 
                data: this.fallbackData.trending.tracks.data,
                error: false
            };
        }
        
        try {
            // Updated URL to match the route prefix
            const response = await fetch(`/api/v1/music-api/search?q=${encodeURIComponent(query)}&limit=${limit}`);
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error searching music:', error);
            return { 
                data: [],
                error: true, 
                message: "Search unavailable. Please try again later." 
            };
        }
    }

    async getGenres() {
        // Return hardcoded genres
        return {
            data: [
                { id: 1, name: "Pop" },
                { id: 2, name: "Rock" },
                { id: 3, name: "Hip Hop" },
                { id: 4, name: "Electronic" },
                { id: 5, name: "Classical" },
                { id: 6, name: "Jazz" },
                { id: 7, name: "R&B" },
                { id: 8, name: "Country" }
            ]
        };
    }

    async getPlaylist(id) {
        try {
            const url = `${this.endpoints.playlist}/${id}`;
            return await this.queueRequest(url, this.getRequestOptions());
        } catch (error) {
            console.error('Error fetching playlist:', error);
            return { error: true, message: error.message };
        }
    }

    async getArtist(id) {
        try {
            const url = `${this.endpoints.artist}/${id}`;
            return await this.queueRequest(url, this.getRequestOptions());
        } catch (error) {
            console.error('Error fetching artist:', error);
            return { error: true, message: error.message };
        }
    }

    async getTrendingTracks() {
        // Return fallback data if in fallback mode
        if (this.fallbackMode) {
            return this.fallbackData.trending;
        }
        
        try {
            // Updated URL to match the route prefix
            const response = await fetch('/api/v1/music-api/trending');
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}`);
            }
            return await response.json();
        } catch (error) {
            console.error('Error fetching trending tracks:', error);
            // Return fallback data when API fails
            return this.fallbackData.trending;
        }
    }
}

// Make it globally available
window.musicAPI = new MusicAPI();