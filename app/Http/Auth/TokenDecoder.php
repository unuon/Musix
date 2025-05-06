<?php

namespace App\Http\Auth;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class TokenDecoder
{
    /**
     * Decode a JWT token and get the user
     *
     * @param string|null $token
     * @return User|null
     */
    public function decode(?string $token = null): ?User
    {
        try {
            if ($token) {
                JWTAuth::setToken($token);
            }
            
            if (!$user = JWTAuth::authenticate()) {
                return null;
            }

            return $user;
        } catch (TokenExpiredException $e) {
            return null;
        } catch (TokenInvalidException $e) {
            return null;
        } catch (JWTException $e) {
            return null;
        }
    }

    /**
     * Check if token is valid
     * 
     * @param string|null $token
     * @return bool
     */
    public function isValid(?string $token = null): bool
    {
        try {
            if ($token) {
                JWTAuth::setToken($token);
            }
            
            return JWTAuth::check();
        } catch (JWTException $e) {
            return false;
        }
    }

    /**
     * Get the payload data from token
     * 
     * @param string|null $token
     * @return array|null
     */
    public function getPayload(?string $token = null): ?array
    {
        try {
            if ($token) {
                JWTAuth::setToken($token);
            }
            
            return JWTAuth::getPayload()->toArray();
        } catch (JWTException $e) {
            return null;
        }
    }
}
