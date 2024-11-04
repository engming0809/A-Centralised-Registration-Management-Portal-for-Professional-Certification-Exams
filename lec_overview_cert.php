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
            // Check if there are any related rows in certificationregistrations
            $check = "SELECT * FROM certificationregistrations WHERE certification_id = (SELECT certification_id FROM certifications WHERE certification_name = '$cert_name')";
            $result = $conn->query($check);

            if ($result->num_rows > 0) {
                // If there are related rows, prevent deletion and show a message
                echo "<div class='alert alert-warning'>Cannot delete $cert_name because there are registered students.</div>";
            } else {
                // Proceed with deletion
                $deleteCertification = "DELETE FROM certifications WHERE certification_name = '$cert_name'";
                $conn->query($deleteCertification);
                echo "<div class='alert alert-danger'>Record for $cert_name has been deleted.</div>";
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
        $sql = "SELECT certification_name, description, requirements, schedule, cost FROM certifications";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo "<table class='table table-striped table-bordered' id='certTable'>
                    <thead class='thead'>
                        <tr>
                            <th>Certification Name</th>
                            <th>Description</th>
                            <th>Requirements</th>
                            <th>Schedule</th>
                            <th>Cost</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>";

            // Output data of each row
            while ($row = $result->fetch_assoc()) {
                echo "<tr class='lec_cert_row'>
                        <td>" . htmlspecialchars($row["certification_name"]) . "</td>
                        <td>" . htmlspecialchars($row["description"]) . "</td>
                        <td>" . htmlspecialchars($row["requirements"]) . "</td>
                        <td>";
            
                // Create a DateTime object and format the date
                $dateTime = new DateTime($row["schedule"]); // Create DateTime object
                echo htmlspecialchars($dateTime->format('m/d/Y, h:i A')); // Output formatted date
            
                echo "</td>
                        <td>" . htmlspecialchars($row["cost"]) . "</td>
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
