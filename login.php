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
        <p>Existing members log in here. For new members, please go to the 
            <a href="register.php">Member Registration page</a>.
        </p>
        <form action="process_login.php" method="post">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input required maxlength="45" type="email" id="email" name="email" class="form-control" placeholder="Enter email" required>
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Password:</label>
                <input required maxlength="45" type="password" id="pwd" name="pwd" class="form-control" placeholder="Enter password" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </main>
    <?php include "inc/footer.inc.php"; ?>
</body>
</html>