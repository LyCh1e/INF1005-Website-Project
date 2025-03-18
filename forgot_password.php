<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Forgot Password - Gastronome's Guide</title>
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <h1>Forgot Password</h1>
        <p>Please enter your email address below. We'll send you a link to reset your password.</p>
        
        <form action="process_forgot_password.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input required maxlength="45" type="email" id="email" name="email" class="form-control"
                    placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn"
                    style='background-color: rgb(0, 146, 131); color: white'>Reset Password</button>
            </div>
        </form>
        <div class="mb-3">
            <p>Remember your password? <a href="login.php" style='color: rgb(0, 146, 131)'>Back to login</a>.</p>
        </div>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>

</html>