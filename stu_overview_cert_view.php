<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Available Certifications";
        $pageHeaderClass = "header_image_cert_stu";
        $pageHeaderTitle = "Available Certifications";
        $pageCertStuActive = "pageCertStuActive";
        include 'include/stu_main_header.php';
    ?>

    

<!-- Main Content -->
<main>
<div class="stu_overview_cert_view">
<div class="container">
        <div class="certification-details">
            

<?php
// Database credentials
$servername = 'localhost';  
$db = 'cert_reg_management_db';  
$user = 'root';  
$pass = '';  

// Create a connection
$conn = new mysqli($servername, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the certification name from the URL
if (isset($_GET['cert_name'])) {
    $cert_name = $conn->real_escape_string(urldecode($_GET['cert_name']));

    // SQL query to fetch the certification details
    $sql = "SELECT certification_id, certification_name, description, requirements, schedule, cost FROM certifications WHERE certification_name='$cert_name'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output data for the selected certification
        $row = $result->fetch_assoc();
        
        // Get the certification ID
        $certificationID = $row['certification_id'];
    } else {
        echo "<p class='text-danger'>No details found for this certification.</p>";
        exit();
    }
} else {
    echo "<p class='text-danger'>No certification specified.</p>";
    exit();
}

// Check if the session variables are set
if (isset($_SESSION['student_id']) ) {
    // Retrieve the values from the session
    $studentId = $_SESSION['student_id'];
    $studentname = $_SESSION['student_full_name']; 

} else {
    // Handle the case where session variables are not set
    echo "<p>No session information available.</p>";
}

// Close the connection
$conn->close();
?>



    <!-- Certification Details Table -->
    <h1 class="text-center my-4"><?php echo htmlspecialchars($row['certification_name']); ?></h1>
    <table class="table table-bordered table-striped shadow-sm">
        <tbody>
            <tr class="align-middle">
                <th scope="row"><i class="fas fa-info-circle text-primary"></i> Description:</th>
                <td class="text-muted"><?php echo htmlspecialchars($row['description']); ?></td>
            </tr>
            <tr class="align-middle">
                <th scope="row"><i class="fas fa-check-circle text-success"></i> Requirements:</th>
                <td class="text-muted"><?php echo htmlspecialchars($row['requirements']); ?></td>
            </tr>
            <tr class="align-middle">
                <th scope="row"><i class="fas fa-calendar-alt text-warning"></i> Schedule:</th>
                <td class="text-muted">
                    <?php 
                    // Assuming $row['schedule'] contains a datetime string
                    $schedule = $row['schedule'];
                    $date = new DateTime($schedule);
                    echo $date->format('F j, Y h:i A'); // Example: November 23, 2024 04:30 PM
                    ?>
                </td>
            </tr>
            <tr class="align-middle">
                <th scope="row"><i class="fas fa-dollar-sign text-danger"></i> Cost:</th>
                <td class="text-muted">RM <?php echo htmlspecialchars($row['cost']); ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Registration Button -->
    <a class="btn btn-primary" href="stu_overview_cert_form.php?certificationID=<?php echo urlencode($certificationID); ?>"> 
        Register Now
    </a>

    <!-- Return Button -->
    <a class="btn btn-secondary" href='stu_overview_cert.php'>Return</a>

        </div>
    </div>
    </div>
    <?php 
    
    ?>
    
</main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>







