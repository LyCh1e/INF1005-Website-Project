// Tabbed Menu for home page
function openMenu(evt, menuName) {
  var i, x, tablinks;
  x = document.getElementsByClassName("menu");
  for (i = 0; i < x.length; i++) {
    x[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < x.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" w3-dark-grey", "");
  }
  document.getElementById(menuName).style.display = "block";
  evt.currentTarget.firstElementChild.className += " w3-dark-grey";
}

// Wait for DOM to be fully loaded before attaching event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Initialize menu if element exists
  if (document.getElementById("myLink")) {
    document.getElementById("myLink").click();
  }

  // Initialize booking tabs if they exist
  if (document.querySelector('#bookingTabs')) {
    var bookingTabs = new bootstrap.Tab(document.querySelector('#bookingTabs'));
  }

  // Login form validation
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('pwd');
    const loginError = document.getElementById('loginError');

    // Simulate valid login credentials
    const validEmail = "user@example.com";
    const validPassword = "Password123!";

    loginForm.addEventListener('submit', function(event) {
      event.preventDefault(); // Prevent default form submission

      // Reset error message
      loginError.style.display = 'none';
      emailField.classList.remove('is-invalid');
      passwordField.classList.remove('is-invalid');

      // Get form values
      const email = emailField.value;
      const password = passwordField.value;

      // Simulate login validation
      if (email === validEmail && password === validPassword) {
        // Successful login
        alert("Login successful! Redirecting to the home page...");
        window.location.href = "index.php"; // Redirect to home page
      } else {
        // Failed login
        loginError.style.display = 'block';
        emailField.classList.add('is-invalid');
        passwordField.classList.add('is-invalid');
      }
    });
  }

  // Registration form validation (existing code)
  const registrationForm = document.getElementById('registrationForm');
  if (registrationForm) {
    // Get password related elements
    const passwordField = document.getElementById('pwd');
    const confirmPasswordField = document.getElementById('pwd_confirm');
    const passwordError = document.getElementById('passwordError');
    const passwordLengthError = document.getElementById('passwordLengthError');
    const passwordUppercaseError = document.getElementById('passwordUppercaseError');
    const passwordLowercaseError = document.getElementById('passwordLowercaseError');
    const passwordSpecialCharError = document.getElementById('passwordSpecialCharError');

    // Function to validate password
    const validatePassword = function(password) {
      let isValid = true;

      // Check password length
      if (password.length < 8) {
        passwordLengthError.style.display = 'block';
        isValid = false;
      } else {
        passwordLengthError.style.display = 'none';
      }

      // Check for at least one uppercase letter
      if (!/[A-Z]/.test(password)) {
        passwordUppercaseError.style.display = 'block';
        isValid = false;
      } else {
        passwordUppercaseError.style.display = 'none';
      }

      // Check for at least one lowercase letter
      if (!/[a-z]/.test(password)) {
        passwordLowercaseError.style.display = 'block';
        isValid = false;
      } else {
        passwordLowercaseError.style.display = 'none';
      }

      // Check for at least one special character
      if (!/[!@#$%^&*]/.test(password)) {
        passwordSpecialCharError.style.display = 'block';
        isValid = false;
      } else {
        passwordSpecialCharError.style.display = 'none';
      }

      return isValid;
    };

    registrationForm.addEventListener('submit', function(event) {
      const password = passwordField.value;
      const confirmPassword = confirmPasswordField.value;

      let isValid = true;

      // Reset error messages
      passwordError.style.display = 'none';
      passwordLengthError.style.display = 'none';
      passwordUppercaseError.style.display = 'none';
      passwordLowercaseError.style.display = 'none';
      passwordSpecialCharError.style.display = 'none';

      // Validate password
      if (!validatePassword(password)) {
        event.preventDefault(); // Prevent form submission
        passwordField.classList.add('is-invalid');
        isValid = false;
      } else {
        passwordField.classList.remove('is-invalid');
      }

      // Check if passwords match
      if (password !== confirmPassword) {
        event.preventDefault(); // Prevent form submission
        passwordError.style.display = 'block';
        confirmPasswordField.classList.add('is-invalid');
        isValid = false;
      } else {
        confirmPasswordField.classList.remove('is-invalid');
      }

      return isValid;
    });

    // Real-time password validation - only for password matching, not length
    if (confirmPasswordField && passwordField) {
      // Function to check password match
      const checkPasswordMatch = function() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        if (confirmPassword !== '' && password !== confirmPassword) {
          passwordError.style.display = 'block';
          confirmPasswordField.classList.add('is-invalid');
        } else {
          passwordError.style.display = 'none';
          confirmPasswordField.classList.remove('is-invalid');
        }
      };

      // Add input listeners only to confirm password field
      confirmPasswordField.addEventListener('input', checkPasswordMatch);
      passwordField.addEventListener('input', checkPasswordMatch);
    }
  }
});

function togglePwd(){
  var pwd = document.getElementById("pwd");
  var eyeIcon = document.getElementById("eyeIcon");
  if (pwd.type === "password"){
    pwd.type = "text";
    eyeIcon.classList.remove("fa-eye");
    eyeIcon.classList.add("fa-eye-slash");
  } else{
    pwd.type = "password";
    eyeIcon.classList.remove("fa-eye-slash");
    eyeIcon.classList.add("fa-eye");
    
  }
}

function toggleConfirmPwd(){
  var pwd = document.getElementById("pwd_confirm");
  var eyeIcon = document.getElementById("CeyeIcon");
  if (pwd.type === "password"){
    pwd.type = "text";
    eyeIcon.classList.remove("fa-eye");
    eyeIcon.classList.add("fa-eye-slash");
  } else{
    pwd.type = "password";
    eyeIcon.classList.remove("fa-eye-slash");
    eyeIcon.classList.add("fa-eye");
    
  }
}