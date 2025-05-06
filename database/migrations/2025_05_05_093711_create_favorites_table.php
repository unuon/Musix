<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Favorite Tracks
        Schema::create('favorite_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('track_id');
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->string('duration')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('preview_url')->nullable();
            $table->timestamps();
            
            // Ensure a user can't favorite the same track twice
            $table->unique(['user_id', 'track_id']);
        });
        
        // Favorite Albums
        Schema::create('favorite_albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('album_id');
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('release_date')->nullable();
            $table->string('cover_image')->nullable();
            $table->integer('tracks_count')->nullable();
            $table->timestamps();
            
            // Ensure a user can't favorite the same album twice
            $table->unique(['user_id', 'album_id']);
        });
        
        // Favorite Artists
        Schema::create('favorite_artists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('artist_id');
            $table->string('name');
            $table->string('image')->nullable();
            $table->integer('albums_count')->nullable();
            $table->integer('fans')->nullable();
            $table->timestamps();
            
            // Ensure a user can't favorite the same artist twice
            $table->unique(['user_id', 'artist_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_tracks');
        Schema::dropIfExists('favorite_albums');
        Schema::dropIfExists('favorite_artists');
    }
};