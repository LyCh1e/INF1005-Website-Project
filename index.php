<?php
// Start the session if needed
session_start();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'inc/head.inc.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My PHP Webpage</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <?php include "inc/nav.inc.php"; ?>
        <?php include "inc/header.inc.php"; ?>
        <!-- Navigation Bar -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">MyWebsite</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        
        <!-- Main Content -->
        <div class="container mt-4">
            <h1>Welcome to My PHP Webpage</h1>
            <p>This is a simple webpage built with PHP and Bootstrap.</p>
        </div>
        
        <!-- Footer -->
        <footer class="bg-dark text-light text-center py-3 mt-4">
            <p>&copy; <?php echo date('Y'); ?> MyWebsite. All rights reserved.</p>
        </footer>
        
    </body>
    </html>
    