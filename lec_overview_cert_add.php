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

    <?php 
    $cert_nameErr = $descriptionErr = $requirementsErr = $scheduleErr = $costErr = "";//Empty error variables
    $cert_name = $description = $requirements = $schedule = $cost = "";//Empty input variables

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (empty($_POST["cert_name"])) {
            $cert_nameErr = "A name of this certification is required";
        } else {
            $cert_name = $_POST["cert_name"];
        }
        
        if (empty($_POST["description"])) {
            $descriptionErr = "A description is required";
        } else {
            $description = $_POST["description"];
        }
        
        if (empty($_POST["requirements"])) {
            $requirementsErr = "This field is required";
        } else {
            $requirements = $_POST["requirements"];
        }
        
        if (empty($_POST["schedule"])) {
            $scheduleErr = "A schedule is required";
        } else {
            $schedule = $_POST["schedule"];
        }

        if (empty($_POST["cost"])) {
            $costErr = "A cost is required";
        } else {
            $cost = $_POST["cost"];
        }
    }


    if (!empty($_POST["cert_name"]) && !empty($_POST["description"])) {
        $conn = connectDB();

        // Sanitize user inputs
        $cert_name = $conn->real_escape_string($_POST["cert_name"]);
        $description = $conn->real_escape_string($_POST["description"]);
        $requirements = $conn->real_escape_string($_POST["requirements"] ?? "");
        $schedule = $conn->real_escape_string($_POST["schedule"] ?? "");
        $cost = $conn->real_escape_string($_POST["cost"] ?? "");

        // Check if cert_name already exists
        $query = "SELECT COUNT(*) AS count FROM certifications WHERE certification_name = '$cert_name'";
        $result = $conn->query($query);

        if ($result) {
            $row = $result->fetch_assoc();
            if ($row['count'] > 0) {
                echo "<script>alert('Certification name already exists. Please choose a different name.');</script>";
            } else {
                // Insert the new record
                insertRecord($cert_name, $description, $requirements, $schedule, $cost, $conn);
                echo "<script>alert('Certification Added successfully.'); window.location.href='lec_overview_cert.php';</script>";
            }
        } else {
            echo "<script>alert('localhost said: Error checking for duplicate certification: " . $conn->error . "');</script>";
        }
        
    }

    function insertRecord($cert_name, $description, $requirements, $schedule, $cost, $conn) {
        $sql = "INSERT INTO certifications (certification_name, description, requirements, schedule, cost) 
                VALUES ('$cert_name', '$description', '$requirements', '$schedule', '$cost')";
        if (!mysqli_query($conn, $sql)) {
            echo "<script>alert('ERROR: Could not execute SQL!');</script>". mysqli_error($conn);
        }
    }

    function connectDB() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $db = "cert_reg_management_db";
        
        $conn = mysqli_connect($servername, $username, $password, $db);
        
        if (!$conn) {
            die('Connection Failed: ' . mysqli_connect_error());
        }
        return $conn;
    }
    ?>
    <section class="lec_overview_cert_add">
    <div class="form-container">
        <h1>Add New Certification</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <fieldset>
                <legend>Certification Information</legend>
                <div class="form-group">
                    <label for="cert_name">Certification Name</label>
                    <input type="text" id="cert_name" name="cert_name" class="form-control">
                    <span class="error"><?php echo $cert_nameErr;?></span>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" class="form-control">
                    <span class="error"><?php echo $descriptionErr;?></span>
                </div>
                
                <div class="form-group">
                    <label for="requirements">Requirements</label>
                    <input type="text" id="requirements" name="requirements" class="form-control">
                    <span class="error"><?php echo $requirementsErr;?></span>
                </div>
                
                <div class="form-group">
                    <label for="schedule">Schedule Date and Time</label>
                    <input type="datetime-local" id="schedule" name="schedule" class="form-control">
                    <span class="error"><?php echo $scheduleErr; ?></span>
                </div>
                
                <div class="form-group">
                    <label for="cost">Cost</label>
                    <input type="number" id="cost" name="cost" class="form-control" step="1" min="0" required>
                    <span class="error"><?php echo $costErr;?></span>
                </div>


                <button type="submit" class="btn btn-primary">Add Certification</button>
                <a href="lec_overview_cert.php" class="btn btn-secondary mb-3">Return to Overview</a>
            </fieldset>
        </form>
    </div>
</section>

        
    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



