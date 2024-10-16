<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certification Overview</title>
</head>
<body>

    <h1>Certification Overview</h1>

    <?php
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
        // Handle delete request
        if (isset($_GET['delete'])) {
            $cert_name = $_GET['delete'];
            $delete = "DELETE FROM certifications WHERE certification_name='$cert_name'";
            $conn->query($delete);
            echo "<p style='color:red;'>Record for $cert_name has been deleted.</p>";
        }
    
        // Handle edit request (update form submission)
        if (isset($_POST['update'])) {
            $cert_name = $_POST['certification_name'];
            $description = $_POST['description'];
            $requirements = $_POST['requirements'];
            $schedule = $_POST['schedule'];
            $cost = $_POST['cost'];
    
            $conn->query("UPDATE certifications SET certification_name='$cert_name', description='$description', requirements='$requirements', schedule='$schedule', cost='$cost' WHERE certification_name='$cert_name'");
            echo "<p style='color:blue;'>Record with the certification name $cert_name updated successfully.</p>";
        }

    if ($result->num_rows > 0) {
        // Start the table
        echo "<a href='addCert.php'>Add New Certification</a>
        <table>
                <tr>
                    <th>Certification Name</th>
                    <th>Description</th>
                    <th>Requirements</th>
                    <th>Schedule</th>
                    <th>Cost</th>
                </tr>";
        
        // Output data of each row
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row["certification_name"] . "</td>
                    <td>" . $row["description"] . "</td>
                    <td>" . $row["requirements"] . "</td>
                    <td>" . $row["schedule"] . "</td>
                    <td>" . $row["cost"] . "</td>
                    <td>
                        <a href='?edit=" . $row["certification_name"] . "' class='btn btn-edit'>Edit</a>
                        <a href='?delete=" . $row["certification_name"] . "' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</a>
                    </td>
                  </tr>";
        }
        // End the table
        echo "</table>";
    } else {
        echo "<p>No results found</p>";
    }
    if (isset($_GET['edit'])) {
        $cert_name = $_GET['edit'];
        $record = $conn->query("SELECT * FROM certifications WHERE certification_name='$cert_name'")->fetch_assoc();
        ?>
        <h2>Edit Record</h2>
        <form action="" method="post">
            <label for="certification_name">Certification Name:</label>
            <input type="text" name="certification_name" value="<?php echo $record['certification_name']; ?>">
            <label for="description">Description: </label>
            <input type="text" name="description" value="<?php echo $record['description']; ?>" required><br>
            <label for="requirements">Requirements:</label>
            <input type="text" name="requirements" value="<?php echo $record['requirements']; ?>" required><br>
            <label for="schedule">Schedule:</label>
            <input type="date" name="schedule" value="<?php echo $record['schedule']; ?>" required><br>
            <label for="cost">Cost:</label>
            <input type="text" name="cost" value="<?php echo $record['cost']; ?>" required><br>
            <input type="submit" name="update" value="Update" class="btn">
        </form>
        <?php
    }

    // Close the connection
    $conn->close();
    ?>
    

</body>
</html>