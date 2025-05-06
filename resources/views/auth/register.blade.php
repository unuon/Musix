@extends('app')

@section('title', 'Register')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="auth-container">
  <div class="auth-card">
    <h1 class="auth-title">Register</h1>
    
    <form id="registerForm" class="auth-form">
      @csrf
      <div class="input-group">
        <label for="name" class="input-label">Name</label>
        <input 
          id="name" 
          class="base-input @error('name') is-invalid @enderror" 
          type="text" 
          placeholder="Your Name" 
          autocomplete="name"
          required
        >
        <span id="nameError" class="error-message">
          @error('name')
            {{ $message }}
          @enderror
        </span>
      </div>
      
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
          autocomplete="new-password"
          required
        >
        <span id="passwordError" class="error-message">
          @error('password')
            {{ $message }}
          @enderror
        </span>
      </div>
      
      <div class="input-group">
        <label for="password_confirmation" class="input-label">Confirm Password</label>
        <input 
          id="password_confirmation" 
          class="base-input" 
          type="password" 
          placeholder="••••••••" 
          autocomplete="new-password"
          required
        >
        <span id="passwordConfirmationError" class="error-message"></span>
      </div>
      
      <div class="form-actions">
        <button id="registerButton" type="submit" class="base-button">
          <span id="registerButtonText">Create Account</span>
          <div id="registerButtonLoader" class="loader hidden"></div>
        </button>
      </div>
    </form>
    
    <div class="auth-footer">
      <p>Already have an account? <a href="{{ route('auth.login') }}" class="auth-link">Login</a></p>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/register.js') }}"></script>
@endsection