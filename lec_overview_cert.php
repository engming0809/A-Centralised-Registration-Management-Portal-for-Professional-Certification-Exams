<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
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
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Certification Overview";
        $pageHeaderClass = "header_image_cert_lec";
        $pageHeaderTitle = "Certification Overview";
        $pageCertLecActive = "pageCertLecActive";
        include 'include/lec_main_header.php';
    ?>

<!-- Main Content -->
    <main>

    <div class="container-fluid lec_cert_overview_main">

        <!-- Search Bar -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="searchInput" class="form-control" placeholder="Search certifications..." onkeyup="filterTable()">
            </div>
        </div>

        <?php
    // Database credentials
    $servername = 'localhost';
    $db = 'cert_reg_management_db';
    $user = 'root';
    $pass = '';

    // Create connection
    $conn = new mysqli($servername, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
    }

    // Handle delete request
    if (isset($_GET['delete'])) {
        $cert_name = $_GET['delete'];
        // Fetch the certification_id based on the certification_name
        $getCertificationId = "SELECT certification_id FROM certifications WHERE certification_name = '$cert_name'";
        $result = $conn->query($getCertificationId);
        
        if ($result->num_rows > 0) {
            // If certification exists, get the certification_id
            $row = $result->fetch_assoc();
            $certification_id = $row['certification_id'];

            // Check if there are any related rows in certificationregistrations
            $check = "SELECT * FROM certificationregistrations WHERE certification_id = $certification_id";
            $result = $conn->query($check);

            if ($result->num_rows > 0) {
                // If there are related rows, delete from related tables first
                $deleteRelated = "
                    DELETE FROM reg_Certificate WHERE registration_id IN (SELECT registration_id FROM CertificationRegistrations WHERE certification_id = $certification_id);
                    DELETE FROM reg_ExamResult WHERE registration_id IN (SELECT registration_id FROM CertificationRegistrations WHERE certification_id = $certification_id);
                    DELETE FROM reg_ExamConfirmationLetter WHERE registration_id IN (SELECT registration_id FROM CertificationRegistrations WHERE certification_id = $certification_id);
                    DELETE FROM reg_PaymentReceipt WHERE registration_id IN (SELECT registration_id FROM CertificationRegistrations WHERE certification_id = $certification_id);
                    DELETE FROM reg_TransactionSlip WHERE registration_id IN (SELECT registration_id FROM CertificationRegistrations WHERE certification_id = $certification_id);
                    DELETE FROM reg_PaymentInvoice WHERE registration_id IN (SELECT registration_id FROM CertificationRegistrations WHERE certification_id = $certification_id);
                    DELETE FROM reg_RegistrationForm WHERE registration_id IN (SELECT registration_id FROM CertificationRegistrations WHERE certification_id = $certification_id);
                    DELETE FROM CertificationRegistrations WHERE certification_id = $certification_id;
                ";

                // Execute each DELETE query individually to avoid "Commands out of sync" error
                $queries = explode(';', $deleteRelated); // Split into separate queries
                foreach ($queries as $query) {
                    if (!empty($query)) {
                        $conn->query($query); // Execute each query
                        if ($conn->more_results()) {
                            $conn->next_result(); // Move to the next result set
                        }
                    }
                }

                // Now delete the certification
                $deleteCertification = "DELETE FROM certifications WHERE certification_id = $certification_id";
                $conn->query($deleteCertification);
                echo "<script>alert('Record for $cert_name and its related data have been deleted.');</script>";
            } else {
                // No related rows found, just delete the certification
                $deleteCertification = "DELETE FROM certifications WHERE certification_name = '$cert_name'";
                $conn->query($deleteCertification);
                echo "<script>alert('Record for $cert_name has been deleted.');</script>";
            }
        } else {
            echo "<script>alert('Certification with name $cert_name does not exist.');</script>";
        }
    }

    // Handle update request
    if (isset($_POST['update'])) {
        $cert_name = $_POST['certification_name'];
        $description = $_POST['description'];
        $requirements = $_POST['requirements'];
        $schedule = $_POST['schedule'];
        $cost = $_POST['cost'];

        $conn->query("UPDATE certifications SET certification_name='$cert_name', description='$description', requirements='$requirements', schedule='$schedule', cost='$cost' WHERE certification_name='$cert_name'");
        echo "<div class='alert alert-success'>Record with the certification name $cert_name updated successfully.</div>";
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
        echo "<table class='table table-striped table-bordered' id='certTable'>
                <thead class='thead'>
                    <tr>
                        <th>Certification Name</th>
                        <th>Number of Registrations</th>
                        <th>Schedule</th>
                        <th>Deadline</th>
                        <th>Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>";

        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            // Check if the deadline is expired
            $deadlineText = ($row['deadline'] < 0) ? "Expired" : $row['deadline'] . " day(s) left";

            echo "<tr class='lec_cert_row'>
                    <td>" . htmlspecialchars($row["certification_name"]) . "</td>
                    <td>" . htmlspecialchars($row["student_count"]) . "</td>
                    <td>";
        
                        // Create a DateTime object and format the date
                        $dateTime = new DateTime($row["schedule"]); // Create DateTime object
                        echo htmlspecialchars($dateTime->format('m/d/Y, h:i A')); // Output formatted date
                    
                        echo "</td>
                    <td>" . htmlspecialchars($deadlineText) . "</td>
                    <td> RM". htmlspecialchars($row["cost"]) . "</td>
                    <td>
                        <a href='lec_overview_cert_edit.php?edit=" . urlencode($row["certification_name"]) . "' class='btn btn-edit btn-sm'>Edit</a>
                        <a href='?delete=" . urlencode($row["certification_name"]) . "' class='btn btn-delete btn-sm' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>
                    </td>
                  </tr>";
        }

        echo "  </tbody>
              </table>";
        echo "<div class='text-right'><a href='lec_overview_cert_add.php' class='btn btn-primary mb-3'>
            Add New Certification</a></div>";
    } else {
        echo "<div class='alert alert-warning'>No certifications found.</div>";
        echo "<div class='text-right'><a href='lec_overview_cert_add.php' class='btn btn-primary mb-3'>
            Add New Certification</a></div>";
    }

    // Close connection
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
