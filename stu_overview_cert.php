<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
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
    <div class="stu_overview_cert_main"> 
    <h1>All Certification Overview</h1> 

    <?php

    
    session_start();
    // Database credentials
    $servername = 'localhost';  // Typically 'localhost' for local databases
    $db   = 'cert_reg_management_db';  // Replace with your database name
    $user = 'root';  // Replace with your database username
    $pass = '';  // Replace with your database password

    // Create a connection
    $conn = new mysqli($servername, $user, $pass, $db);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL query to fetch data
    $sql = "SELECT certification_name, description, requirements, schedule, cost FROM certifications";  // Replace with your table name
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Start the table with Bootstrap classes
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead>
                <tr>
                    <th>Certification Name</th>
                    <th>Requirements</th>
                    <th>Schedule</th>
                    <th>Cost</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";
        
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["certification_name"] . "</td>
                    <td>" . $row["requirements"] . "</td>
                    <td>";
            
                // Create a DateTime object and format the date
                $dateTime = new DateTime($row["schedule"]); // Create DateTime object
                echo htmlspecialchars($dateTime->format('m/d/Y, h:i A')); // Output formatted date
            
                echo "</td>
                    <td>" . $row["cost"] . "</td>
                    <td>
                        <a href='stu_overview_cert_view.php?cert_name=" . urlencode($row["certification_name"]) . "' class='btn btn-view'>[View]</a>
                    </td>
                </tr>";
        }
        // End the table
        echo "</tbody></table>";
    } else {
        echo "<p class='text-center'>No results found</p>";
    }

    // Close the connection
    $conn->close();
    ?>
    </div>
</main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



