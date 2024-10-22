<!DOCTYPE html>
<html lang="en">

<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['lecturer_full_name'])) {
    header("Location: index.php");
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Lecturer Profile";
        $pageHeaderClass = "header_image_home_lec";
        $pageHeaderTitle = "Lecturer Profile";
        $pageProfileActive = "pageProfileActive";
        include 'include/lec_main_header.php';
    ?>

<!-- Main Content -->
    <main>
        
    <!-- example -->
	<!-- Display session info -->
	<!-- example -->
		
    <section class="main_menu_first_main">
            <p>Name: <strong><?php echo $_SESSION['lecturer_full_name']; ?></strong></p>
            <p>Email: <strong><?php echo $_SESSION['lecturer_email']; ?></strong></p>
    </section>

    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



