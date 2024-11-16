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
        $pageTitle = "Lecturer Dashboard";
        $pageHeaderClass = "header_image_home_lec";
        $pageHeaderTitle = "Lecturer Dashboard";
        $pageHomeLecActive = "pageHomeLecActive";
        include 'include/lec_main_header.php';
    ?>

<!-- Main Content -->
    <main>
        
    <!-- example -->
	<!-- Display session info -->
            <p>Logged in as: <strong><?php echo $_SESSION['lecturer_full_name']; ?></strong></p>
            <p> Email is: <strong><?php echo $_SESSION['lecturer_email']; ?></strong></p>
	<!-- example -->
		
    <section class="main_menu_first_main">
        <h1>Welcome, Lecturer <strong><?php echo $_SESSION['lecturer_full_name']; ?></h1>
        <p>
            This portal simplifies the process of managing student registrations for professional certification exams. 
            Our platform provides tools to streamline registration, invoicing, payment, and results publication.
            Here's what you can do on our platform:
        </p>
        <ul>
            <li>Create and manage certification programs, including schedules and costs.</li>
            <li>Collect student registration details efficiently.</li>
            <li>Ensure accurate data entry with input validation on registration forms.</li>
            <li>Download and submit registration forms to exam providers seamlessly.</li>
            <li>Receive and manage invoices and payment receipts for student registrations.</li>
            <li>Upload exam results and certificates for student access.</li>
        </ul>
    </section>

    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



