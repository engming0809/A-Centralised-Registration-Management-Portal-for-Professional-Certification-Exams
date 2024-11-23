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

    <script>
        // JavaScript function to filter the table
        function filterTable() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const table = document.getElementById('certTable');
            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) { // Start from 1 to skip the table header
                const cells = rows[i].getElementsByTagName('td');
                let match = false;

                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(searchInput) > -1) {
                        match = true;
                        break;
                    }
                }

                rows[i].style.display = match ? '' : 'none';
            }
        }
    </script>

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
    <!-- Search Bar -->
    <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Search certifications..." onkeyup="filterTable()">
            </div>
        </div>

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
    $sql = "SELECT c.certification_name, c.description, c.requirements, c.schedule, c.cost, 
    COUNT(cr.student_id) AS student_count,
    DATEDIFF(c.schedule, CURRENT_DATE) AS deadline
    FROM certifications c
    LEFT JOIN CertificationRegistrations cr ON c.certification_id = cr.certification_id
    GROUP BY c.certification_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Start the table with Bootstrap classes
        echo "<table class='table table-striped table-bordered' id='certTable'>";
        echo "<thead>
                <tr>
                    <th>Certification Name</th>
                    <th>Requirements</th>
                    <th>Schedule</th>
                    <th>Deadline</th>
                    <th>Cost</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";
        
       // Output data of each row
        while($row = $result->fetch_assoc()) {
            // Check if the deadline has expired
            if ($row['deadline'] < 0) {
                continue; // Skip this row if the deadline is expired
            }

            // Otherwise, output the row data
            $deadlineText = ($row['deadline'] < 0) ? "Expired" : $row['deadline'] . " day(s) left";
            echo "<tr>
                    <td>" . $row["certification_name"] . "</td>
                    <td>" . $row["requirements"] . "</td>
                    <td>";
            
            // Create a DateTime object and format the date
            $dateTime = new DateTime($row["schedule"]); // Create DateTime object
            echo htmlspecialchars($dateTime->format('m/d/Y, h:i A')); // Output formatted date
            
            echo "</td>
                    <td>" . htmlspecialchars($deadlineText) . "</td>
                    <td> RM" . htmlspecialchars($row["cost"]) . "</td>
                    <td>
                        <a href='stu_overview_cert_view.php?cert_name=" . urlencode($row["certification_name"]) . "' class='btn btn-primary'>View</a>
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



