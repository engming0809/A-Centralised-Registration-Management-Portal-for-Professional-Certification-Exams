<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <link rel="stylesheet" href="style/style.css">
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
    
    <div class="container lec_overview_cert_edit">
        <h1>Edit Certification</h1>

        <?php
        $servername = 'localhost';
        $db = 'cert_reg_management_db';
        $user = 'root';
        $pass = '';

        $conn = new mysqli($servername, $user, $pass, $db);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        if (isset($_GET['edit'])) {
            $cert_name = $_GET['edit'];

            $record = $conn->query("SELECT * FROM certifications WHERE certification_name='$cert_name'")->fetch_assoc();
        }

        if (isset($_POST['update'])) {
            $cert_name = $_POST['certification_name'];
            $description = $_POST['description'];
            $requirements = $_POST['requirements'];
            $schedule = $_POST['schedule'];
            $cost = $_POST['cost'];

            $cert_id = $record['certification_id'];  // Use a unique ID for matching
            $update_query = "UPDATE certifications 
                SET certification_name='$cert_name', 
                    description='$description', 
                    requirements='$requirements', 
                    schedule='$schedule', 
                    cost='$cost' 
                WHERE certification_id='$cert_id'";

            if ($conn->query($update_query)) {
                echo "<div class='alert alert-warning'>Record for certification $cert_name updated successfully.</div>";
                header('Location: lec_overview_cert.php');
                exit;
            } else {
                echo "<p class='message text-danger'>Failed to update record for $cert_name.</p>";
            }
        }
        ?>

        <!-- Edit Form -->
        <form action="lec_overview_cert_edit.php?edit=<?php echo $cert_name; ?>" method="post">
            <div class="form-group">
                <label for="certification_name">Certification Name:</label>
                <input type="text" class="form-control" name="certification_name" value="<?php echo $record['certification_name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description:</label>
                <input type="text" class="form-control" name="description" value="<?php echo $record['description']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="requirements">Requirements:</label>
                <input type="text" class="form-control" name="requirements" value="<?php echo $record['requirements']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="schedule">Schedule:</label>
                <input type="datetime-local" class="form-control" name="schedule" value="<?php echo date('Y-m-d\TH:i', strtotime($record['schedule'])); ?>" required>
            </div>

            
            <div class="form-group">
                <label for="cost">Cost:</label>
                <input type="number" class="form-control" name="cost" value="<?php echo $record['cost']; ?>" required step="1" min="0">
            </div>

            
            <button type="submit" name="update" class="btn btn-primary">Update</button>
            <a href="lec_overview_cert.php" class="btn btn-secondary mb-3">Return to Overview</a>
        </form>

        <?php
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



