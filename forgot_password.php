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
        <p>Please enter your email, first name, and phone number to verify your identity.</p>
        
        <form action="process_forgot_password.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input required maxlength="45" type="email" id="email" name="email" class="form-control"
                    placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="fname" class="form-label">First Name:</label>
                <input required maxlength="45" type="text" id="fname" name="fname" class="form-control"
                    placeholder="Enter your first name" required>
            </div>
            <div class="mb-3">
                <label for="ph" class="form-label">Phone Number:</label>
                <input required maxlength="20" type="tel" id="ph" name="ph" class="form-control"
                    placeholder="Enter your phone number" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn" style='background-color: rgb(0, 78, 74); color: white'>Reset Password</button>
            </div>
        </form>
        <div class="mb-3">
            <p>Remember your password? <a href="login.php" style='color: rgb(0, 78, 74)'>Back to login</a>.</p>
        </div>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>

</html>