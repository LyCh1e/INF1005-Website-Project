<?php
session_start();
$email = $fname = $ph = "";
$errorMsg = "";
$success = true;

// Check if email is submitted
if (empty($_POST["email"])) {
    $errorMsg .= "Email is required.<br>";
    $success = false;
} else {
    $email = sanitize_input($_POST["email"]);
    // Additional check to make sure e-mail address is well-formed
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg .= "Invalid email format.<br>";
        $success = false;
    }
}

// Check if first name is submitted
if (empty($_POST["fname"])) {
    $errorMsg .= "First name is required.<br>";
    $success = false;
} else {
    $fname = sanitize_input($_POST["fname"]);
}

// Check if phone number is submitted
if (empty($_POST["ph"])) {
    $errorMsg .= "Phone number is required.<br>";
    $success = false;
} else {
    $ph = sanitize_input($_POST["ph"]);
    // Phone number validation matching the registration requirement
    if (!preg_match("/^\d{8}$/", $ph)) {
        $errorMsg .= "Invalid phone number. Please enter an 8-digit number.<br>";
        $success = false;
    }
}

function sanitize_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateResetToken() {
    return bin2hex(random_bytes(32)); // Generate a secure random token
}

function processPasswordReset()
{
    global $email, $fname, $ph, $errorMsg, $success;
    
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
    
    // Check if email exists and matches with fname and phone_number in database
    $stmt = $conn->prepare("SELECT email FROM world_of_pets_members WHERE email = ? AND fname = ? AND phone_number = ?");
    $stmt->bind_param("sss", $email, $fname, $ph);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        $errorMsg = "The provided information doesn't match our records.";
        $success = false;
        $stmt->close();
        $conn->close();
        return;
    }
    
    // Generate reset token
    $token = generateResetToken();
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
    
    // Check if we already have a reset token for this user
    $checkStmt = $conn->prepare("SELECT email FROM password_reset_tokens WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    $checkStmt->close();
    
    if ($checkResult->num_rows > 0) {
        // Update existing token
        $updateStmt = $conn->prepare("UPDATE password_reset_tokens SET token = ?, expiry = ? WHERE email = ?");
        $updateStmt->bind_param("sss", $token, $expiry, $email);
        $updateStmt->execute();
        $updateStmt->close();
    } else {
        // Insert new token
        $insertStmt = $conn->prepare("INSERT INTO password_reset_tokens (email, token, expiry) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $email, $token, $expiry);
        $insertStmt->execute();
        $insertStmt->close();
    }
    
    $stmt->close();
    $conn->close();
    
    // In a real application, you would send an email with the reset link
    // For this implementation, we'll just store the token and simulate sending
    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_token'] = $token;
}

if ($success) {
    processPasswordReset();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include "inc/head.inc.php"; ?>
    <title>Password Reset Request - Gastronome's Guide</title>
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
            <?php if ($success): ?>
                <h1>Password Reset Link Ready</h1>
                <h3>Identity verified successfully. You can now reset your password.</h3>
                <p>
                    <!-- For demonstration purposes, create a direct link -->
                    <a href="reset_password.php?email=<?= urlencode($email) ?>&token=<?= urlencode($_SESSION['reset_token']) ?>">
                        <button class="action-button success-button">Click here to reset your password</button>
                    </a>
                </p>
                <p class="small text-muted">
                    (In a real application, this link would be sent to your email)
                </p>
            <?php else: ?>
                <h1>Identity Verification Failed</h1>
                <h3>The following errors were detected:</h3>
                <p><?= $errorMsg ?></p>
                <p>
                    <a href="forgot_password.php">
                        <button class="action-button error-button">Try Again</button>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>

</html>