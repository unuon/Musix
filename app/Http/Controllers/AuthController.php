<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Utilities\ApiResponse;
use App\Models\User;
use App\Http\Auth\TokenEncoder;
use App\Http\Auth\TokenDecoder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $tokenEncoder;
    protected $tokenDecoder;

    /**
     * Create a new AuthController instance.
     */
    public function __construct(TokenEncoder $tokenEncoder, TokenDecoder $tokenDecoder)
    {
        $this->tokenEncoder = $tokenEncoder;
        $this->tokenDecoder = $tokenDecoder;
    }

    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: email already exists',
                'errors' => $validator->errors(),
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate JWT for API auth
        $jwtToken = JWTAuth::fromUser($user);

        // Generate a separate, guaranteed unique database token
        $uniqueDbToken = hash('sha256', $user->id . Str::random(40) . microtime(true));
        
        $user->access_token = $uniqueDbToken;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Registration successful',
            'data' => [
                'access_token' => $jwtToken, // Use for API auth
                'user' => $user,
            ]
        ]);
    }

    /**
     * Login user and return a token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: invalid input',
                'errors' => $validator->errors(),
            ]);
        }

        $credentials = $request->only('email', 'password');
        $token = $this->tokenEncoder->encodeFromCredentials($credentials);

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed: invalid credentials',
                'errors' => [
                    'auth' => ['Invalid email or password.']
                ],
            ]);
        }

        // Save the token to the user's record
        $user = Auth::user();
        $user->access_token = $token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'access_token' => $token,
                'user' => $user,
            ]
        ]);
    }

    /**
     * Log the user out (invalidate the token).
     */
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return ApiResponse::success(null, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     */
    public function refresh()
    {
        try {
            $token = $this->tokenEncoder->refresh();
            return $this->respondWithToken($token, Auth::user());
        } catch (\Exception $e) {
            return ApiResponse::error('Error refreshing token', 401);
        }
    }

    /**
     * Get the authenticated user.
     */
    public function user()
    {
        return ApiResponse::success(Auth::user());
    }
    
    /**
     * Get the token array structure.
     */
    protected function respondWithToken($token, $user)
    {
        // Get token payload to extract expiration time
        $payload = $this->tokenDecoder->getPayload($token);
        
        return ApiResponse::success([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $payload ? ($payload['exp'] - time()) : config('jwt.ttl', 60) * 60,
            'user' => $user
        ], 'Authentication successful');
    }
}