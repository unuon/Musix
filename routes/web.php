<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\User;

// Auth routes
Route::get('/login', function () {return view('auth.login');})->name('auth.login');
Route::get('/register', function () {return view('auth.register');})->name('auth.register');

// Home routes
Route::get('/home', function () {return view('home');})->name('home');

// Redirect root to login
Route::get('/', function () {return redirect()->route('auth.login');});

// Dashboard Route
Route::get('/dashboard', function () {
    // Create a mock user for now or fetch the current user 
    // When you implement proper auth, you'll get the user from auth()->user()
    $user = auth()->user();
    
    // If no authenticated user, try to get from session
    if (!$user) {
        $userId = session('user');
        if ($userId) {
            $user = User::find($userId);
        }
    }
    
    // If still no user, create a temporary one
    if (!$user) {
        $user = new User();
        $user->name = 'Guest User';
    }
    
    return view('dashboard.index', ['user' => $user]);
})->name('dashboard');
