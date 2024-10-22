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
        <h1 class="text-center my-4">Certification Details</h1>
        <div class="certification-details">
            <?php
            session_start();

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
                $sql = "SELECT certification_name, description, requirements, schedule, cost FROM certifications WHERE certification_name='$cert_name'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data for the selected certification
                    $row = $result->fetch_assoc();
                    ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col" colspan="2" class="importantth text-center"><?php echo htmlspecialchars($row['certification_name']); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row"><i class="fas fa-info-circle"></i> Description:</th>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><i class="fas fa-check-circle"></i> Requirements:</th>
                                <td><?php echo htmlspecialchars($row['requirements']); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><i class="fas fa-calendar-alt"></i> Schedule:</th>
                                <td><?php echo htmlspecialchars($row['schedule']); ?></td>
                            </tr>
                            <tr>
                                <th scope="row"><i class="fas fa-dollar-sign"></i> Cost:</th>
                                <td>$<?php echo htmlspecialchars($row['cost']); ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <a class="btn btn-primary register-button text-center" href='stu_overview_cert_form.php'>Register</a>
                    <?php
                } else {
                    echo "<p class='text-danger'>No details found for this certification.</p>";
                }
            } else {
                echo "<p class='text-danger'>No certification specified.</p>";
            }

///////////////////////////////////////////////// Check if the certification name is provided//////////////////////////////
        if (isset($_GET['cert_name'])) {
            $certName = $_GET['cert_name'];
            // Prepare the SQL statement to retrieve the ID
            $stmt = $conn->prepare("SELECT certification_id FROM certifications WHERE certification_name = ?");
            $stmt->bind_param("s", $certName); // Use "s" for string
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the certification exists
            if ($result->num_rows > 0) {
                // Fetch the certification ID
                $row = $result->fetch_assoc();
                $_SESSION['certification_id'] = $row['certification_id']; // Store the ID in the session
            } else {
                // Handle the case where the certification does not exist
                echo "Certification not found.";
            }
        }

        // Check if the session variables are set
        if (isset($_SESSION['student_id']) && isset($_SESSION['certification_id'])) {
            // Retrieve the values from the session
            $studentId = $_SESSION['student_id'];
            $certificationId = $_SESSION['certification_id'];
            $studentname = $_SESSION['student_full_name']; 

        } else {
            // Handle the case where session variables are not set
            echo "<p>No session information available.</p>";
        }

        ///////////////////////////////////////////////// Check if the certification name is provided//////////////////////////////

                // Close the connection
                $conn->close();
            ?>

        </div>
    </div>
    </div>
    <?php 
    
            // Print out the session information
            echo "<p>Session Information</p>";
            echo "<p>Student ID: " . htmlspecialchars($studentId) . "</p>";
            echo "<p>Student Name: " . htmlspecialchars($studentname) . "</p>";
            echo "<p>Certification ID: " . htmlspecialchars($certificationId) . "</p>";
    ?>
    
</main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>







