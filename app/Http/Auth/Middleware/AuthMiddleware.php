<?php

namespace App\Http\Auth\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Auth\TokenDecoder;
use App\Http\Utilities\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthMiddleware
{
    protected $tokenDecoder;

    public function __construct(TokenDecoder $tokenDecoder)
    {
        $this->tokenDecoder = $tokenDecoder;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $token = JWTAuth::parseToken();
            
            if (!$token->authenticate()) {
                return ApiResponse::unauthorized('User not found');
            }
        } catch (TokenExpiredException $e) {
            return ApiResponse::unauthorized('Token expired');
        } catch (TokenInvalidException $e) {
            return ApiResponse::unauthorized('Token invalid');
        } catch (JWTException $e) {
            return ApiResponse::unauthorized('Token not provided');
        }

        return $next($request);
    }
}
