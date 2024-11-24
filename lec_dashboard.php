<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery and Calendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Lecturer Dashboard";
        $pageHeaderClass = "header_image_home_lec";
        $pageHeaderTitle = "Lecturer Dashboard";
        $pageHomeLecActive = "pageHomeLecActive";
        include 'include/lec_main_header.php';
    ?>


<?php
// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['lecturer_full_name'])) {
    header("Location: index.php");
    exit();
}
?>


<!-- Main Content -->
    <main class="container-fluid py-5 lec_dashboard">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4 border-light rounded">
                    <div class="card-body">
                        <h5 class="card-title">Welcome, <?php echo $_SESSION['lecturer_full_name']; ?> <i class="fas fa-user-graduate"></i></h5>
                        <p class="card-text">
                        This portal simplifies the process of managing student registrations for professional certification exams. 
                        Our platform provides tools to streamline registration, invoicing, payment, and results publication.
                        Here's what you can do on our platform:
                        </p>
                        <p>Here's what you can do on our platform:</p>
                        <ul>
                            <li>Create and manage certification programs, including schedules and costs.</li>
                            <li>Collect student registration details efficiently.</li>
                            <li>Ensure accurate data entry with input validation on registration forms.</li>
                            <li>Download and submit registration forms to exam providers seamlessly.</li>
                            <li>Receive and manage invoices and payment receipts for student registrations.</li>
                            <li>Upload exam results and certificates for student access.</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Session Info Sidebar -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4 border-light rounded">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><b>Session Information</b></h6>
                    </div>
                    <div class="card-body">
                        <p><strong>Logged in as:</strong> <?php echo $_SESSION['lecturer_full_name']; ?></p>
                        <p><strong>Your Email:</strong> <?php echo $_SESSION['lecturer_email']; ?></p>
                        <p><strong>Logged in on:</strong> <?php echo isset($_SESSION['login_time']) ? date('l, F j, Y \a\t g:i A', $_SESSION['login_time']) : 'N/A'; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-4 border-light rounded">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Website Features Overview</h6>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Home</strong></td>
                            <td>The homepage provides a brief introduction to the platform and allows you to navigate to all key sections.</td>
                        </tr>
                        <tr>
                            <td><strong>Certification Overview</strong></td>
                            <td>View, edit and add certifications, along with their schedules, deadlines, and more detailed information.</td>
                        </tr>
                        <tr>
                            <td><strong>Registration Overview</strong></td>
                            <td>See all the exams registration of the student, along with payment status, deadlines, and upcoming exam dates.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <h3 class="text-center mb-4">Available Certification Exams</h3>
        
        <div class="table-container">
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

            $sql = "SELECT certification_name, description, requirements, schedule FROM certifications";  // Replace with your table name
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo "<table class='table table-striped table-bordered'>";
                echo "<thead>
                        <tr>
                            <th>Certification Name</th>
                            <th>Schedule</th>
                            <th>Deadline</th>
                        </tr>
                    </thead>
                    <tbody>";
                
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    $scheduleDateTime = new DateTime($row["schedule"]);
                    $currentDateTime = new DateTime(); 
                    $interval = $currentDateTime->diff($scheduleDateTime); 
                    
                    // Check if past the expiry date
                    if ($currentDateTime > $scheduleDateTime) {
                        $deadline = "Expired";
                    } else {
                        // Calculate days and hours left
                        $daysLeft = $interval->format('%a');
                        $hoursLeft = $interval->h + ($daysLeft * 24);
                        
                        if ($daysLeft > 0) {
                            $deadline = "$daysLeft days left";
                        } else {
                            $deadline = "$hoursLeft hours left";
                        }
                    }
                    
                    echo "<tr>
                    <td>" . htmlspecialchars($row["certification_name"]) . "</td>
                    <td>" . htmlspecialchars($scheduleDateTime->format('m/d/Y, h:i A')) . "</td>
                    <td>" . htmlspecialchars($deadline) . "</td>
                  </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='text-center text-danger'>No certification exams available at the moment.</p>";
            }
            ?>
        </div>

        <!-- Calendar -->
        <div class="card shadow-sm mb-4 border-light rounded cert_calender">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0">Certification Exam Calendar</h6>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>

        <script>
        // Initialize FullCalendar
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: [
                    <?php
                    // Fetch certification exams from the database and display them in the calendar
                    $sql = "SELECT certification_name, schedule FROM certifications";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        // Loop through the results and create event objects for FullCalendar
                        while ($row = $result->fetch_assoc()) {
                            $scheduleDateTime = new DateTime($row["schedule"]);
                            $eventDate = $scheduleDateTime->format('Y-m-d H:i:s');
                            echo "{ title: '" . htmlspecialchars($row["certification_name"]) . "', start: '" . $eventDate . "' },";
                        }
                    }
                    ?>
                ]
            });
        });
        </script>
    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



