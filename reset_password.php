<?php
session_start();
$errorMsg = "";
$validToken = false;

// Check if email and token are provided
if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Verify token validity (in a real application)
    // For this demonstration, we'll just check against the session
    if (
        isset($_SESSION['reset_email']) && isset($_SESSION['reset_token']) &&
        $_SESSION['reset_email'] === $email && $_SESSION['reset_token'] === $token
    ) {
        $validToken = true;
    } else {
        // In a real application, check the database
        $config = parse_ini_file('/var/www/private/db-config.ini');
        if ($config) {
            $conn = new mysqli(
                $config['servername'],
                $config['username'],
                $config['password'],
                $config['dbname']
            );

            if (!$conn->connect_error) {
                $stmt = $conn->prepare("SELECT * FROM password_reset_tokens WHERE email = ? AND token = ? AND expiry > NOW()");
                $stmt->bind_param("ss", $email, $token);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $validToken = true;
                    $_SESSION['reset_email'] = $email;
                    $_SESSION['reset_token'] = $token;
                } else {
                    $errorMsg = "Invalid or expired reset link.";
                }

                $stmt->close();
                $conn->close();
            } else {
                $errorMsg = "Database connection error.";
            }
        } else {
            $errorMsg = "Failed to read database config file.";
        }
    }
} else {
    $errorMsg = "Missing email or token.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Reset Password - Gastronome's Guide</title>
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
        <h1>Reset Your Password</h1>

        <?php if ($validToken): ?>
            <p>Your identity has been verified. Please enter your new password below.</p>
            <form id="resetPasswordForm" action="process_reset_password.php" method="post">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div class="mb-3">
                    <label for="pwd" class="form-label">New Password:</label>
                    <div class="mb-3" style="display: flex;">
                        <input required maxlength="45" type="password" id="pwd" name="pwd" class="form-control"
                            placeholder="Enter password" required>
                        <button type="button" onclick="togglePwd()"
                            style="background: none; border: none; padding-left: 10px"><i class="fas fa-eye"
                                id="eyeIcon"></i></button>
                    </div>
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
                    <label for="pwd_confirm" class="form-label">Confirm New Password:</label>
                    <div class="mb-3" style="display: flex;">
                        <input required maxlength="45" type="password" id="pwd_confirm" name="pwd_confirm"
                            class="form-control" placeholder="Confirm password" required>
                        <button type="button" onclick="toggleConfirmPwd()"
                            style="background: none; border: none; padding-left: 10px"><i class="fas fa-eye"
                                id="CeyeIcon"></i></button>
                    </div>
                    <div id="passwordError" class="error-message">Passwords do not match</div>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn" style='background-color: rgb(0, 146, 131); color: white'>Update
                        Password</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">
                <p><?= $errorMsg ?></p>
                <p>Please request a new password reset link <a href="forgot_password.php"
                        style='color: rgb(0, 146, 131)'>here</a>.</p>
            </div>
        <?php endif; ?>
    </main>
    <?php include "inc/footer.inc.php"; ?>
    <script src="js/main.js"></script>
    <script>
        // Add reset password form validation
        document.addEventListener('DOMContentLoaded', function() {
            const resetPasswordForm = document.getElementById('resetPasswordForm');
            if (resetPasswordForm) {
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

                resetPasswordForm.addEventListener('submit', function(event) {
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

                // Real-time password validation
                if (passwordField) {
                    passwordField.addEventListener('input', function() {
                        validatePassword(passwordField.value);
                    });
                }

                // Real-time password match validation
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

                    // Add input listeners
                    confirmPasswordField.addEventListener('input', checkPasswordMatch);
                    passwordField.addEventListener('input', checkPasswordMatch);
                }
            }
        });
    </script>
</body>

</html>