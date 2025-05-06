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
        // Playlists
        Schema::create('playlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_public')->default(false);
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
        
        // Playlist Tracks (Many-to-Many relationship)
        Schema::create('playlist_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained()->onDelete('cascade');
            $table->string('track_id');
            $table->string('title');
            $table->string('artist')->nullable();
            $table->string('album')->nullable();
            $table->string('duration')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('preview_url')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            
            // Ensure a track can't be added to the same playlist twice
            $table->unique(['playlist_id', 'track_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_tracks');
        Schema::dropIfExists('playlists');
    }
};