<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Admin Dashboard";
        $pageHeaderClass = "header_image_admin_main";
        $pageHeaderTitle = "Admin Dashboard";
        $pageAdminHomeActive = "pageAdminHomeActive";
        include 'include/admin_main_header.php';
    ?>

<?php
// Database connection parameters
$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

// Create a PDO instance to connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get the total number of users and active/inactive users
    $sql_lecturers = "SELECT 
                        COUNT(*) AS total_lecturers, 
                        SUM(status = 'active') AS active_lecturers, 
                        SUM(status = 'inactive') AS inactive_lecturers 
                      FROM Lecturer";
    $sql_students = "SELECT 
                        COUNT(*) AS total_students, 
                        SUM(status = 'active') AS active_students, 
                        SUM(status = 'inactive') AS inactive_students 
                     FROM Student";

    // Execute queries
    $stmt_lecturers = $conn->query($sql_lecturers);
    $stmt_students = $conn->query($sql_students);

    // Fetch the results
    $lecturers = $stmt_lecturers->fetch(PDO::FETCH_ASSOC);
    $students = $stmt_students->fetch(PDO::FETCH_ASSOC);

    // Calculate totals
    $total_users = $lecturers['total_lecturers'] + $students['total_students'];
    $active_users = $lecturers['active_lecturers'] + $students['active_students'];
    $inactive_users = $lecturers['inactive_lecturers'] + $students['inactive_students'];

    // Query to get certifications and the count of registrations by result status
    $sql_cert_registration = "
    SELECT c.certification_id, c.certification_name, 
        COUNT(cr.registration_id) AS total_registrations, 
        SUM(CASE WHEN cr.result_status = 'completed' THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN cr.result_status = 'incomplete' THEN 1 ELSE 0 END) AS incomplete,
        SUM(CASE WHEN cr.result_status = 'pending' THEN 1 ELSE 0 END) AS pending
    FROM Certifications c
    LEFT JOIN CertificationRegistrations cr ON c.certification_id = cr.certification_id
    GROUP BY c.certification_id, c.certification_name
    ";
    $stmt_cert_registration = $conn->query($sql_cert_registration);
    $registrations = $stmt_cert_registration->fetchAll(PDO::FETCH_ASSOC);
        
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!-- Main Content -->
<main class="container-fluid py-5 lec_dashboard">



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


        
<div class="container mt-5">

<!-- Total Users Table -->
<div class="card shadow-sm mb-4 border-light rounded">
    <div class="card-header bg-danger text-white">
        <h4 class="text-center mb-0"> Users Statistics</h4>
    </div>
    <div class="card-body">
    
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col">Category</th>
                    <th scope="col">Total</th>
                    <th scope="col">Active</th>
                    <th scope="col">Inactive</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Lecturers</td>
                    <td><?php echo $lecturers['total_lecturers']; ?></td>
                    <td><?php echo $lecturers['active_lecturers']; ?></td>
                    <td><?php echo $lecturers['inactive_lecturers']; ?></td>
                </tr>
                <tr>
                    <td>Students</td>
                    <td><?php echo $students['total_students']; ?></td>
                    <td><?php echo $students['active_students']; ?></td>
                    <td><?php echo $students['inactive_students']; ?></td>
                </tr>
                <tr >
                    <td>Total Users</td>
                    <td><?php echo $total_users; ?></td>
                    <td><?php echo $active_users; ?></td>
                    <td><?php echo $inactive_users; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mb-4 border-light rounded">
    <div class="card-header bg-danger text-white">
        <h4 class="text-center mb-0">Certification Registration Statistics</h4>
    </div>
    <div class="card-body">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Certification Name</th>
                    <th scope="col">Total Registrations</th>
                    <th scope="col">Completed</th>
                    <th scope="col">Incomplete</th>
                    <th scope="col">Pending</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrations as $registration): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($registration['certification_name']); ?></td>
                        <td><?php echo $registration['total_registrations']; ?></td>
                        <td><?php echo $registration['completed']; ?></td>
                        <td><?php echo $registration['incomplete']; ?></td>
                        <td><?php echo $registration['pending']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

        </div>



    </main>

    
    <!-- Bootstrap JS and dependencies (jQuery and Popper.js) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



