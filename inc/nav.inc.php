<?php session_start(); ?>
<nav class="navbar navbar-expand-lg" id="nav-bar">
    <a class="navbar-brand">
        <img class="logo" src="images/logo.png" alt="LOGO">
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="navbar-toggler-icon"></span>
    </button>
    <nav class="collapse navbar-collapse" id="collapsibleNavbar" aria-label="Primary Navigation Bar">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="index.php#home">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="aboutus.php">About</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php#discover">Discover</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="aboutServicesDropdown" role="button"
                    data-toggle="dropdown" aria-expanded="false">
                    Services
                </a>
                <ul class="dropdown-menu" aria-labelledby="aboutServicesDropdown">
                    <li><a class="dropdown-item" href="new_restaurants.php">Restaurant Reviews</a></li>
                    <li><a class="dropdown-item" href="booking.php">Booking</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="restaurant-rankings.php">Rankings</a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <?php if (isset($_SESSION['fname'])): ?>
                <li class="nav-item">
                <div style="font-size: 24px; align-content: center; padding-top: 10px;">
                    <a class="nav-link" style="color: white; text-decoration:none;" href="profile.php">Welcome, <?php echo $_SESSION['fname']; ?>!</a>
                </div>
                </li>
                <li class="nav-item">
                    <div style="display: inline-block; padding: 5px; white-space: nowrap;">
                        <a href="logout.php" onclick="return confirmLogout();">
                            <img class="logout-image" src="images/logout.png" width="60" height="50" alt="logouticon">
                        </a>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <div style="display: inline-block; padding: 5px; white-space: nowrap;">
                        <a href="login.php" style="text-decoration: none;">
                            <h1 class="welcome-text">Login</h1>
                            <img class="account-image" src="images/account.png" alt="accounticon">
                        </a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</nav>
<script>
    function confirmLogout() {
        var confirmAction = confirm("Are you sure you want to log out?");
        if (confirmAction) {
            return true;
        } else {
            return false;
        }
    }
</script>