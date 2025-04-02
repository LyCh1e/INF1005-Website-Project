<?php
session_start();
$email = $token = $pwd = $pwd_confirm = "";
$errorMsg = "";
$success = true;

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email and token
    if (empty($_POST["email"]) || empty($_POST["token"])) {
        $errorMsg .= "Missing email or token.<br>";
        $success = false;
    } else {
        $email = sanitize_input($_POST["email"]);
        $token = sanitize_input($_POST["token"]);
    }

    // Validate password
    if (empty($_POST["pwd"])) {
        $errorMsg .= "Password is required.<br>";
        $success = false;
    } else {
        $pwd = sanitize_input($_POST["pwd"]);
        // Check password length
        if (strlen($pwd) < 8) {
            $errorMsg .= "Password must be at least 8 characters long.<br>";
            $success = false;
        }
        // Check for uppercase letter
        if (!preg_match('/[A-Z]/', $pwd)) {
            $errorMsg .= "Password must contain at least one uppercase letter.<br>";
            $success = false;
        }
        // Check for lowercase letter
        if (!preg_match('/[a-z]/', $pwd)) {
            $errorMsg .= "Password must contain at least one lowercase letter.<br>";
            $success = false;
        }
        // Check for special character
        if (!preg_match('/[!@#$%^&*]/', $pwd)) {
            $errorMsg .= "Password must contain at least one special character (e.g., !@#$%^&*).<br>";
            $success = false;
        }
    }

    // Validate password confirmation
    if (empty($_POST["pwd_confirm"])) {
        $errorMsg .= "Password confirmation is required.<br>";
        $success = false;
    } else {
        $pwd_confirm = sanitize_input($_POST["pwd_confirm"]);
        // Check if passwords match
        if ($pwd !== $pwd_confirm) {
            $errorMsg .= "Passwords do not match.<br>";
            $success = false;
        }
    }
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function updatePassword()
{
    global $email, $token, $pwd, $errorMsg, $success;
    
    // Create database connection
    $config = parse_ini_file('/var/www/private/db-config.ini');
    if (!$config) {
        $errorMsg = "Failed to read database config file.";
        $success = false;
        return;
    }
    
    $conn = new mysqli(
        $config['servername'],
        $config['username'],
        $config['password'],
        $config['dbname']
    );
    
    // Check connection
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
        return;
    }
    
    // Verify token is valid and not expired
    $stmt = $conn->prepare("SELECT * FROM password_reset_tokens WHERE email = ? AND token = ? AND expiry > NOW()");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $errorMsg = "Invalid or expired reset token.";
        $success = false;
        $stmt->close();
        $conn->close();
        return;
    }
    
    // Password hash
    $pwd_hashed = password_hash($pwd, PASSWORD_DEFAULT);
    
    // Update password in the database
    $updateStmt = $conn->prepare("UPDATE world_of_pets_members SET password = ? WHERE email = ?");
    $updateStmt->bind_param("ss", $pwd_hashed, $email);
    $updateStmt->execute();
    
    // Check if password was updated
    if ($updateStmt->affected_rows == 0) {
        $errorMsg = "Failed to update password.";
        $success = false;
        $updateStmt->close();
        $stmt->close();
        $conn->close();
        return;
    }
    
    // Delete used token
    $deleteStmt = $conn->prepare("DELETE FROM password_reset_tokens WHERE email = ?");
    $deleteStmt->bind_param("s", $email);
    $deleteStmt->execute();
    
    $deleteStmt->close();
    $updateStmt->close();
    $stmt->close();
    $conn->close();
    
    // Clear session variables
    unset($_SESSION['reset_email']);
    unset($_SESSION['reset_token']);
}

if ($success && $_SERVER["REQUEST_METHOD"] == "POST") {
    updatePassword();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "inc/head.inc.php"; ?>
    <title>Password Reset - Gastronome's Guide</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        h1 {
            color: rgb(0, 146, 131);
            margin-bottom: 10px;
        }
        h3 {
            color: #555;
            margin-bottom: 20px;
        }
        .action-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .success-button {
            background-color: rgb(0, 146, 131);
        }
        .error-button {
            background-color: red;
        }
    </style>
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <div style="text-align: center;">
            <?php if ($success && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <h1>Password Reset Successful</h1>
                <h3>Your password has been updated.</h3>
                <p>
                    <a href="login.php">
                        <button class="action-button success-button">Back to Login</button>
                    </a>
                </p>
            <?php elseif (!$success && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <h1>Password Reset Failed</h1>
                <h3>The following errors were detected:</h3>
                <p><?= $errorMsg ?></p>
                <p>
                    <a href="forgot_password.php">
                        <button class="action-button error-button">Request New Reset Link</button>
                    </a>
                </p>
            <?php else: ?>
                <h1>Password Reset Error</h1>
                <h3>Invalid request.</h3>
                <p>
                    <a href="forgot_password.php">
                        <button class="action-button error-button">Request Password Reset</button>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>

</html>