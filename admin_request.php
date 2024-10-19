<?php
// Database connection parameters
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "cert_reg_management_db"; // Database name
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch pending student registrations
$sql = "SELECT lecturer_id, full_name, email, created_at FROM Lecturer WHERE status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Student Registrations</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Registration Request";
        $pageHeaderClass = "header_image_home";
        $pageHeaderTitle = "Registration Request Overview";
        $pageHomeActive = "pageHomeActive";
        include 'include/admin_main_header.php';
    ?>
<div class="container mt-5">
    <h2>Pending Student Registrations</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Registration Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["lecturer_id"] . "</td>
                            <td>" . htmlspecialchars($row["full_name"]) . "</td>
                            <td>" . htmlspecialchars($row["email"]) . "</td>
                            <td>" . $row["created_at"] . "</td>
                            <td>
                                <form action='admin_request_approve.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='lecturer_id' value='" . $row["lecturer_id"] . "'>
                                    <button type='submit' class='btn btn-success btn-sm'>Approve</button>
                                </form>
                                <form action='admin_request_reject.php' method='POST' style='display:inline;'>
                                    <input type='hidden' name='lecturer_id' value='" . $row["lecturer_id"] . "'>
                                    <button type='submit' class='btn btn-danger btn-sm'>Reject</button>
                                </form>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No pending registrations.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<!-- Footer -->
<?php
        include 'include/footer.php';
    ?>
</body>
</html>

<?php
$conn->close();
?>
