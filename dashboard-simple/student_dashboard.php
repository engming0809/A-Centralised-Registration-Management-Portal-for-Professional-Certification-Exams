<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>
<!-- Introduction Content Start -->
    <div class="main-content d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "LMAO";
    include 'main_header.php';
    ?>

<!-- Something else -->
    <section class="welcome">
        <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
        <p>This is the student dashboard.</p>
        <p><a href="logout.php">Logout</a></p>
        
        <ul>
            <li><a href="xxx.php">Certification Overview</a></li>
            <li><a href="xxx.php">Registration Overview</a></li>
        </ul>
    </section>

    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website. All rights reserved.</p>
    </footer>

    </div>
</body>
</html>
