<!DOCTYPE html>
<html lang="en">

<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['student_full_name'])) {
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
        $pageTitle = "Student Profile";
        $pageHeaderClass = "header_image_home_stu";
        $pageHeaderTitle = "Student Profile";
        $pageProfileActive = "pageProfileActive";
        include 'include/stu_main_header.php';
    ?>

<!-- Main Content -->
    <main>
	
        
    <section class="main_menu_first_main">
	<!-------------------------------- example ------------------------------------------------------>
	<!-- Display session info -->
            <p>Student Name: <strong><?php echo $_SESSION['student_full_name']; ?></strong></p>
            <p>Student Email: <strong><?php echo $_SESSION['student_email']; ?></strong></p>
	<!-------------------------------- example ------------------------------------------------------>
    </section>




    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



