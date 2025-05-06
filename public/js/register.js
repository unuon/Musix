document.addEventListener('DOMContentLoaded', function() {
  // IMMEDIATELY hide any authentication messages that might be hardcoded
  const authMessages = document.querySelectorAll('.auth-error, .auth-message, [id^="authError"]');
  authMessages.forEach(el => {
    if (el) {
      el.style.display = 'none';
      el.innerHTML = '';
      if (el.parentNode) {
        el.parentNode.removeChild(el);
      }
    }
  });

  // Regular form handling code
  const registerForm = document.getElementById('registerForm');
  const nameInput = document.getElementById('name');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const passwordConfirmationInput = document.getElementById('password_confirmation');
  const nameError = document.getElementById('nameError');
  const emailError = document.getElementById('emailError');
  const passwordError = document.getElementById('passwordError');
  const passwordConfirmationError = document.getElementById('passwordConfirmationError');
  const registerButton = document.getElementById('registerButton');
  const registerButtonText = document.getElementById('registerButtonText');
  const registerButtonLoader = document.getElementById('registerButtonLoader');
  
  // CSRF token
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  
  // Helper to show loader
  function showLoader() {
    if (registerButton && registerButtonText && registerButtonLoader) {
      registerButton.disabled = true;
      registerButtonText.style.opacity = '0';
      registerButtonLoader.classList.remove('hidden');
    }
  }
  
  // Helper to hide loader
  function hideLoader() {
    if (registerButton && registerButtonText && registerButtonLoader) {
      registerButton.disabled = false;
      registerButtonText.style.opacity = '1';
      registerButtonLoader.classList.add('hidden');
    }
  }
  
  // Helper to clear errors
  function clearErrors() {
    if (nameError) nameError.textContent = '';
    if (emailError) emailError.textContent = '';
    if (passwordError) passwordError.textContent = '';
    if (passwordConfirmationError) passwordConfirmationError.textContent = '';
    
    // Find and remove any auth message elements again
    const authMessages = document.querySelectorAll('.auth-error, .auth-message, [id^="authError"]');
    authMessages.forEach(el => {
      if (el) {
        el.style.display = 'none';
        el.innerHTML = '';
      }
    });
  }
  
  // Handle form submission
  if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
      e.preventDefault();
      clearErrors();
      
      // Basic validation
      let isValid = true;
      
      if (!nameInput.value) {
        if (nameError) nameError.textContent = 'Name is required';
        isValid = false;
      }
      
      if (!emailInput.value) {
        if (emailError) emailError.textContent = 'Email is required';
        isValid = false;
      } else if (!isValidEmail(emailInput.value)) {
        if (emailError) emailError.textContent = 'Please enter a valid email address';
        isValid = false;
      }
      
      if (!passwordInput.value) {
        if (passwordError) passwordError.textContent = 'Password is required';
        isValid = false;
      } else if (passwordInput.value.length < 8) {
        if (passwordError) passwordError.textContent = 'Password must be at least 8 characters';
        isValid = false;
      }
      
      if (!passwordConfirmationInput.value) {
        if (passwordConfirmationError) passwordConfirmationError.textContent = 'Please confirm your password';
        isValid = false;
      } else if (passwordInput.value !== passwordConfirmationInput.value) {
        if (passwordConfirmationError) passwordConfirmationError.textContent = 'Passwords do not match';
        isValid = false;
      }
      
      if (!isValid) return;
      
      showLoader();
      
      // API call to register
      fetch('/api/v1/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          name: nameInput.value,
          email: emailInput.value,
          password: passwordInput.value,
          password_confirmation: passwordConfirmationInput.value
        })
      })
      .then(response => response.json())
      .then(data => {
        console.log('Registration response:', data); // Debug the response
        
        if (data.success) {
          // Use the proper notification system
          if (window.notifications) {
            window.notifications.success('Registration Successful', 'Your account has been created!');
          }
          
          // Double check no auth messages are showing
          clearErrors();
          
          // Redirect to login page after a delay
          setTimeout(() => {
            window.location.href = '/login';
          }, 1500);
        } else {
          // Show errors
          hideLoader();
          
          if (data.errors) {
            if (data.errors.name && nameError) {
              nameError.textContent = data.errors.name[0];
            }
            if (data.errors.email && emailError) {
              emailError.textContent = data.errors.email[0];
            }
            if (data.errors.password && passwordError) {
              passwordError.textContent = data.errors.password[0];
            }
          }
          
          if (data.message && window.notifications) {
            window.notifications.error('Registration Failed', data.message);
          }
        }
      })
      .catch(error => {
        console.error('Registration error:', error);
        hideLoader();
        
        if (window.notifications) {
          window.notifications.error('Registration Failed', 'An error occurred. Please try again.');
        }
      });
    });
  }
  
  // Helper functions
  function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
});