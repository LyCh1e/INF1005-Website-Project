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
    if (isset($_SESSION['reset_email']) && isset($_SESSION['reset_token']) && 
        $_SESSION['reset_email'] === $email && $_SESSION['reset_token'] === $token) {
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
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <h1>Reset Your Password</h1>
        
        <?php if ($validToken): ?>
        <p>Please enter your new password below.</p>
        <form action="process_reset_password.php" method="post">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="mb-3">
                <label for="pwd" class="form-label">New Password:</label>
                <input required maxlength="45" type="password" id="pwd" name="pwd" class="form-control" 
                    placeholder="Enter new password" required>
            </div>
            <div class="mb-3">
                <label for="pwd_confirm" class="form-label">Confirm New Password:</label>
                <input required maxlength="45" type="password" id="pwd_confirm" name="pwd_confirm" class="form-control" 
                    placeholder="Confirm new password" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn" 
                    style='background-color: rgb(0, 146, 131); color: white'>Update Password</button>
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
</body>

</html>