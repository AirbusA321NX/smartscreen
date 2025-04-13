// Authentication JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
  // Check which page we're on
  const isLoginPage = window.location.pathname.includes('login.html');
  const isSignupPage = window.location.pathname.includes('signup.html');
  
  // Get form elements based on current page
  if (isLoginPage) {
    const loginBtn = document.getElementById('login-btn');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    
    // Add event listener to login button
    loginBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Reset error messages
      emailError.style.display = 'none';
      passwordError.style.display = 'none';
      
      // Validate email
      if (!emailInput.value) {
        emailError.textContent = 'Email is required';
        emailError.style.display = 'block';
        return;
      } else if (!isValidEmail(emailInput.value)) {
        emailError.textContent = 'Please enter a valid email';
        emailError.style.display = 'block';
        return;
      }
      
      // Validate password
      if (!passwordInput.value) {
        passwordError.textContent = 'Password is required';
        passwordError.style.display = 'block';
        return;
      } else if (passwordInput.value.length < 6) {
        passwordError.textContent = 'Password must be at least 6 characters';
        passwordError.style.display = 'block';
        return;
      }
      
      // For demo purposes, simulate successful login
      // In a real application, you would make an API call to verify credentials
      simulateLogin(emailInput.value);
    });
  }
  
  if (isSignupPage) {
    const signupBtn = document.getElementById('signup-btn');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const termsCheckbox = document.getElementById('terms');
    
    const nameError = document.getElementById('name-error');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    const confirmPasswordError = document.getElementById('confirm-password-error');
    
    // Add event listener to signup button
    signupBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Reset error messages
      nameError.style.display = 'none';
      emailError.style.display = 'none';
      passwordError.style.display = 'none';
      confirmPasswordError.style.display = 'none';
      
      let isValid = true;
      
      // Validate name
      if (!nameInput.value) {
        nameError.textContent = 'Name is required';
        nameError.style.display = 'block';
        isValid = false;
      }
      
      // Validate email
      if (!emailInput.value) {
        emailError.textContent = 'Email is required';
        emailError.style.display = 'block';
        isValid = false;
      } else if (!isValidEmail(emailInput.value)) {
        emailError.textContent = 'Please enter a valid email';
        emailError.style.display = 'block';
        isValid = false;
      }
      
      // Validate password
      if (!passwordInput.value) {
        passwordError.textContent = 'Password is required';
        passwordError.style.display = 'block';
        isValid = false;
      } else if (passwordInput.value.length < 6) {
        passwordError.textContent = 'Password must be at least 6 characters';
        passwordError.style.display = 'block';
        isValid = false;
      }
      
      // Validate confirm password
      if (passwordInput.value !== confirmPasswordInput.value) {
        confirmPasswordError.textContent = 'Passwords do not match';
        confirmPasswordError.style.display = 'block';
        isValid = false;
      }
      
      // Validate terms
      if (!termsCheckbox.checked) {
        alert('Please agree to the Terms of Service and Privacy Policy');
        isValid = false;
      }
      
      // If all validations pass
      if (isValid) {
        // For demo purposes, simulate successful signup
        // In a real application, you would make an API call to create an account
        simulateSignup(nameInput.value, emailInput.value);
      }
    });
  }
  
  // Helper functions
  function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
  
  function simulateLogin(email) {
    // Store user info in localStorage for demo purposes
    // In a real application, you would use secure authentication methods
    const username = email.split('@')[0];
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('username', username);
    
    // Redirect to dashboard
    window.location.href = 'dashboard.html';
  }
  
  function simulateSignup(name, email) {
    // Store user info in localStorage for demo purposes
    // In a real application, you would use secure authentication methods
    localStorage.setItem('isLoggedIn', 'true');
    localStorage.setItem('username', name);
    
    // Redirect to dashboard
    window.location.href = 'dashboard.html';
  }
});
