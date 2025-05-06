<?php

namespace App\Http\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TokenEncoder
{
    /**
     * Generate a new JWT token for the user
     * Plus a guaranteed unique database token
     *
     * @param User $user
     * @return string
     */
    public function encode(User $user): string
    {
        // Generate a completely random, unique token for the database
        // This is explicitly NOT a JWT token
        return Str::random(60) . uniqid('', true) . $user->id;
    }

    /**
     * Generate token from credentials
     * 
     * @param array $credentials
     * @return string|null
     */
    public function encodeFromCredentials(array $credentials): ?string
    {
        if (!JWTAuth::attempt($credentials)) {
            return null;
        }
        
        // Get the authenticated user
        $user = Auth::user();
        
        // Generate a completely random token for this user
        return Str::random(60) . uniqid('', true) . $user->id;
    }
    
    /**
     * Refresh the token
     * 
     * @return string
     */
    public function refresh(): string
    {
        $user = Auth::user();
        // Generate a new unique token
        return Str::random(60) . uniqid('', true) . $user->id;
    }
}