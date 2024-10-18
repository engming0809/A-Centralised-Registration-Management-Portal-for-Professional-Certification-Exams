<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Certification</title>
</head>
<body>

    <h1>Edit Certification</h1>

    <?php
    // Database credentials
    $servername = 'localhost';
    $db   = 'cert_reg_management_db';
    $user = 'root';
    $pass = '';

    // Create a connection
    $conn = new mysqli($servername, $user, $pass, $db);

    // Check the connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if 'edit' GET parameter is set
    if (isset($_GET['edit'])) {
        $cert_name = $_GET['edit'];

        // Fetch the certification details for editing
        $record = $conn->query("SELECT * FROM certifications WHERE certification_name='$cert_name'")->fetch_assoc();
    }

    // Handle the update form submission
    if (isset($_POST['update'])) {
        $cert_name = $_POST['certification_name'];
        $description = $_POST['description'];
        $requirements = $_POST['requirements'];
        $schedule = $_POST['schedule'];
        $cost = $_POST['cost'];

        // Update query
        $update_query = "UPDATE certifications 
                         SET certification_name='$cert_name', 
                             description='$description', 
                             requirements='$requirements', 
                             schedule='$schedule', 
                             cost='$cost' 
                         WHERE certification_name='$cert_name'";

        if ($conn->query($update_query)) {
            echo "<p>Record for certification $cert_name updated successfully.</p>";
            // Redirect back to the main page after update
            header('Location: CertOver.php');
            exit;
        } else {
            echo "<p>Failed to update record for $cert_name.</p>";
        }
    }
    ?>

    <!-- Edit Form -->
    <form action="edit.php?edit=<?php echo $cert_name; ?>" method="post">
        <label for="certification_name">Certification Name:</label>
        <input type="text" name="certification_name" value="<?php echo $record['certification_name']; ?>" required><br>
        
        <label for="description">Description: </label>
        <input type="text" name="description" value="<?php echo $record['description']; ?>" required><br>
        
        <label for="requirements">Requirements:</label>
        <input type="text" name="requirements" value="<?php echo $record['requirements']; ?>" required><br>
        
        <label for="schedule">Schedule:</label>
        <input type="date" name="schedule" value="<?php echo $record['schedule']; ?>" required><br>
        
        <label for="cost">Cost:</label>
        <input type="text" name="cost" value="<?php echo $record['cost']; ?>" required><br><br>
        
        <input type="submit" name="update" value="Update">
    </form>

    <?php
    // Close the connection
    $conn->close();
    ?>

</body>
</html>