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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Lecturer Profile";
        $pageHeaderClass = "header_image_home_lec";
        $pageHeaderTitle = $_SESSION['lecturer_full_name'];
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
            <p>Email: <strong><?php echo $_SESSION['lecturer_id']; ?></strong></p>
    </section>

    <section class="view_profile_main">
    <?php

    // Database credentials
    $servername = 'localhost';
    $db = 'cert_reg_management_db';
    $user = 'root';
    $pass = '';

    // Create connection
    $conn = new mysqli($servername, $user, $pass, $db);

    // Fetch lecturer information from the database
    $email = $_SESSION['email'];
    $query = "SELECT full_name, email, status FROM Lecturer WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $lecturer_info = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='alert alert-danger'>Lecturer not found.</div>";
        exit();
    }

    // Assign lecturer information to variables
    $full_name = $lecturer_info['full_name'];
    $email = $lecturer_info['email'];
    $status = $lecturer_info['status'];

    // Set profile image based on status (you can customize this based on your needs)
    $profile_image = 'image_user/default.jpg'; // https://www.vecteezy.com/free-vector/default-profile-picture
    
    ?>

    <!-- Main Content -->
    <h2>Lecturer Profile Information</h2>

    <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="view_profile_img">

    <table class="table table-bordered">
        <tr>
            <th>Full Name</th>
            <td><?php echo htmlspecialchars($full_name); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($email); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?php echo htmlspecialchars($status); ?></td>
        </tr>
    </table>

    <div class="text-center">
        <a href="update_profile.php" class="btn btn-primary">Edit Profile</a>
    </div>
</section>


    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



