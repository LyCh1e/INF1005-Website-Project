<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Member Registration</title>
    <style>
        .error-message {
            color: red;
            font-size: 0.9rem;
            margin-top: 5px;
            display: none;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
        }
        .password-length-error,
        .password-uppercase-error,
        .password-lowercase-error,
        .password-special-char-error {
            color: red;
            font-size: 0.9rem;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <?php include "inc/nav.inc.php"; ?>
    
    <main class="container mt-5">
        <h1>Member Registration</h1>
        <?php if (!isset($_SESSION['fname'])): ?>
        <p>For existing members, please go to the <a href="login.php" style='color: rgb(0, 78, 74)'>Sign In page</a>.</p>
        <form id="registrationForm" action="process_register.php" method="post">
            <div class="mb-3">
                <label for="fname" class="form-label">First Name:</label>
                <input required maxlength="45" type="text" id="fname" name="fname" class="form-control" placeholder="Enter first name">
            </div>
            <div class="mb-3">
                <label for="lname" class="form-label">Last Name:</label>
                <input required maxlength="45" type="text" id="lname" name="lname" class="form-control" placeholder="Enter last name">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input required maxlength="45" type="email" id="email" name="email" class="form-control" placeholder="Enter email">
            </div>
            <div class="mb-3">
                <label for="ph" class="form-label">Phone Number:</label>
                <input required type="tel" id="ph" name="ph" class="form-control" placeholder="Enter phone number">
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Password:</label>
                <input required maxlength="45" type="password" id="pwd" name="pwd" class="form-control" placeholder="Enter password">
                <div id="passwordLengthError" class="password-length-error">
                    Password must be at least 8 characters long
                </div>
                <div id="passwordUppercaseError" class="password-uppercase-error">
                    Password must contain at least one uppercase letter
                </div>
                <div id="passwordLowercaseError" class="password-lowercase-error">
                    Password must contain at least one lowercase letter
                </div>
                <div id="passwordSpecialCharError" class="password-special-char-error">
                    Password must contain at least one special character (e.g., !@#$%^&*)
                </div>
            </div>
            <div class="mb-3">
                <label for="pwd_confirm" class="form-label">Confirm Password:</label>
                <input required maxlength="45" type="password" id="pwd_confirm" name="pwd_confirm" class="form-control" placeholder="Confirm password">
                <div id="passwordError" class="error-message">Passwords do not match</div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="agree" id="agree" class="form-check-input" required>
                <label class="form-check-label" for="agree">Agree to terms and conditions.</label>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn" style='background-color: rgb(0, 78, 74); color: white'>Submit</button>
            </div>
        </form>
        <?php else: ?>
            <p><a href="index.php" style='color: rgb(0, 78, 74)'>You are already logged in!</a></p>
        <?php endif; ?>
    </main>
    
    <?php include "inc/footer.inc.php"; ?>
    <script src="js/main.js"></script>
</body>
</html>