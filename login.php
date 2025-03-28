<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "inc/head.inc.php"; ?>
    <title>Member Login</title>
</head>

<body>
    <?php include "inc/nav.inc.php"; ?>
    <main class="container mt-5">
        <h1>Member Login</h1>
        <?php if (!isset($_SESSION['fname'])): ?>
            <p>Existing members log in here. For new members, please go to the
                <a href="register.php" style='color: rgb(0, 78, 74)'>Member Registration page</a>.
            </p>
            <form action="process_login.php" method="post">
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input required maxlength="45" type="email" id="email" name="email" class="form-control"
                        placeholder="Enter email" required>
                </div>
                <div class="mb-3">
                    <label for="pwd" class="form-label">Password:</label>
                    <div class="mb-3" style="display: flex;">
                    <input required maxlength="45" type="password" id="pwd" name="pwd" class="form-control"
                        placeholder="Enter password" required> 
                        <button type="button" onclick="togglePwd()" aria-label="eyeicon" style="background: none; border: none; padding-left: 10px"><i class="fas fa-eye" id="eyeIcon"></i></button>
                    <div id="loginError" class="error-message">Account does not exist or password is incorrect.</div>
                    </div>
                </div>
                <div class="mb-3">
                    <button type="submit" class="btn"
                        style='background-color: rgb(0, 78, 74); color: white'>Submit</button>
                </div>
                <div class="mb-3">
                    <p>Forgot your password? <a href="forgot_password.php" style='color: rgb(0, 78, 74)'>Reset it here</a>.</p>
                </div>
            </form>
        <?php else: ?>
            <p><a href="index.php" style='color: rgb(0, 78, 74)'>You are already logged in!</a>.</p>
        <?php endif; ?>
    </main>
    <?php include "inc/footer.inc.php"; ?>
    <script src="js/main.js" defer></script>
</body>

</html>