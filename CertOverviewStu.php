<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Certification Overview</title>
</head>
<body>

    <h1>Student Certification Overview</h1>

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

    if ($result->num_rows > 0) {
        // Start the table
        echo "
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
                        <a href='Form_Files/form.html' class='btn btn-edit'>Register</a>
                    </td>
                  </tr>";
        }
        // End the table
        echo "</table>";
    } else {
        echo "<p>No results found</p>";
    }

    // Close the connection
    $conn->close();
    ?>
    

</body>
</html>