document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('emailError');
    const passwordError = document.getElementById('passwordError');
    const authError = document.getElementById('authError');
    const loginButton = document.getElementById('loginButton');
    const loginButtonText = document.getElementById('loginButtonText');
    const loginButtonLoader = document.getElementById('loginButtonLoader');
    
    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Helper to show loader
    function showLoader() {
      loginButton.disabled = true;
      loginButtonText.style.opacity = '0';
      loginButtonLoader.classList.remove('hidden');
    }
    
    // Helper to hide loader
    function hideLoader() {
      loginButton.disabled = false;
      loginButtonText.style.opacity = '1';
      loginButtonLoader.classList.add('hidden');
    }
    
    // Helper to show error message
    function showError(message) {
      // Just use notification system for auth errors, not inline
      if (window.notifications) {
        window.notifications.error('Login Failed', message);
      }
    }
    
    // Helper to clear errors
    function clearErrors() {
      // Hide the authError element if it exists
      if (authError) authError.classList.add('hidden');
      
      // Clear individual field errors
      if (emailError) emailError.textContent = '';
      if (passwordError) passwordError.textContent = '';
      
      // Also remove any possible hardcoded auth messages
      const authMessages = document.querySelectorAll('.auth-message');
      authMessages.forEach(el => {
        if (el) el.style.display = 'none';
      });
    }
    
    // Handle form submission
    if (loginForm) {
      loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();
        
        // Basic validation
        let isValid = true;
        
        if (!emailInput.value) {
          emailError.textContent = 'Email is required';
          isValid = false;
        } else if (!isValidEmail(emailInput.value)) {
          emailError.textContent = 'Please enter a valid email address';
          isValid = false;
        }
        
        if (!passwordInput.value) {
          passwordError.textContent = 'Password is required';
          isValid = false;
        }
        
        if (!isValid) return;
        
        showLoader();
        
        // API call to authenticate
        fetch('/api/v1/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            email: emailInput.value,
            password: passwordInput.value
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Store token in localStorage
            localStorage.setItem('auth_token', data.data.access_token);
            localStorage.setItem('user', JSON.stringify(data.data.user));
            
            // Show success notification
            if (window.notifications) {
              window.notifications.success('Login Successful', data.message || 'Welcome back!');
            }
            
            // Make extra sure no auth messages are showing
            clearErrors();
            
            // Redirect with delay to allow notification to be seen
            setTimeout(() => {
              window.location.href = data.data.redirect || '/dashboard';
            }, 1500);
          } else {
            // Show errors
            hideLoader();
            
            if (data.errors) {
              if (data.errors.email) {
                emailError.textContent = data.errors.email[0];
              }
              if (data.errors.password) {
                passwordError.textContent = data.errors.password[0];
              }
            }
            
            if (data.message) {
              showError(data.message);
            }
          }
        })
        .catch(error => {
          hideLoader();
          showError('An error occurred. Please try again.');
          console.error('Login error:', error);
        });
      });
    }
    
    // Helper functions
    function isValidEmail(email) {
      const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      return re.test(String(email).toLowerCase());
    }
});