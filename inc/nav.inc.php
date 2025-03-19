<?php session_start(); ?>
<nav class="navbar navbar-expand-sm" id="nav-bar">
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
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Services
                </a>
                <ul class="dropdown-menu" aria-labelledby="aboutServicesDropdown">
                    <li><a class="dropdown-item" href="restaurants.php">Restaurant Reviews</a></li>
                    <li><a class="dropdown-item" href="new_restaurants.php">Restaurant Reviews (API)</a></li>
                    <li><a class="dropdown-item" href="booking.php">Booking</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="restaurant-rankings.php">Rankings</a>
            </li>
        </ul>
        <ul class="navbar-nav">
            <li class="nav-item">
                <?php if (isset($_SESSION['fname'])): ?>
                    <h3><b>Welcome, <?php echo $_SESSION['fname']; ?>!</b></h3>
                </li>
                <li class="nav-item">
                    <a href="logout.php" onclick="return confirmLogout();">
                        <img src="images/logout.png" width="70" height="50" alt="logouticon">
                    </a>
                </li>
                <li class="nav-item">
                <?php else: ?>
                    <div style="display: inline-block; margin-right: 20px; white-space: nowrap;">
                        <a href="login.php">
                            <img src="images/account.png" width="70" height="50" alt="accounticon">
                        </a>
                    </div>
                <?php endif; ?>
            </li>
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