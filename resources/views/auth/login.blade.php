@extends('app')

@section('title', 'Login')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="auth-container">
  <div class="auth-card">
    <h1 class="auth-title">Login</h1>
    
    <form id="loginForm" class="auth-form">
      @csrf
      <div class="input-group">
        <label for="email" class="input-label">Email</label>
        <input 
          id="email" 
          class="base-input @error('email') is-invalid @enderror" 
          type="email" 
          placeholder="your@email.com" 
          autocomplete="email"
          required
        >
        <span id="emailError" class="error-message">
          @error('email')
            {{ $message }}
          @enderror
        </span>
      </div>
      
      <div class="input-group">
        <label for="password" class="input-label">Password</label>
        <input 
          id="password" 
          class="base-input @error('password') is-invalid @enderror" 
          type="password" 
          placeholder="••••••••" 
          autocomplete="current-password"
          required
        >
        <span id="passwordError" class="error-message">
          @error('password')
            {{ $message }}
          @enderror
        </span>
      </div>
      
      <div class="form-actions">
        <button id="loginButton" type="submit" class="base-button">
          <span id="loginButtonText">Sign In</span>
          <div id="loginButtonLoader" class="loader hidden"></div>
        </button>
      </div>
      
    </form>
    
    <div class="auth-footer">
      <p>Don't have an account? <a href="{{ route('auth.register') }}" class="auth-link">Register</a></p>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/login.js') }}"></script>
@endsection