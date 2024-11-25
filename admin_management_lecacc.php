
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
        $pageTitle = "Manage Lecturer Account";
        $pageHeaderClass = "header_image_admin_main";
        $pageHeaderTitle = "Manage Lecturer Account";
        $pageAdminPassActive = "pageAdminPassActive";
        include 'include/admin_main_header.php';
    ?>


<main>
    <!-- Backend -->
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

    $query = "SELECT * FROM Lecturer"; // Query updated to use 'Lecturer' table
    $result = mysqli_query($conn, $query);

    // Total Number of Lecturers
    $total_lecturers = mysqli_num_rows($result);

    // Handle users through edit or add from modal table (POST)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $fullName = $_POST['full_name'];
        $status = $_POST['status']; // Lecturer status field added
        $biography = $_POST['biography'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        // Check if password is set and matches confirm password
        if (!empty($password) && $password === $confirmPassword) {
            // Hash the new password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        } else {
            $hashedPassword = null;  // No password update if not set or mismatched
        }

        $query = "SELECT * FROM Lecturer WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        // Update or insert user into Lecturer table
        if (mysqli_num_rows($result) > 0) {
            // Update Lecturer record
            $query = "UPDATE Lecturer SET full_name='$fullName', email='$email', status='$status', biography='$biography' WHERE email='$email'";
            mysqli_query($conn, $query);

            // Update password if necessary
            if ($hashedPassword) {
                $query = "UPDATE Lecturer SET password='$hashedPassword' WHERE email='$email'";
                mysqli_query($conn, $query);
            }
            
            $conn->close();
            echo "<script>alert('Lecturer information updated successfully!'); window.location.href = 'admin_management_lecacc.php';</script>";
        } else {
            // Insert new Lecturer into Lecturer table
            $query = "INSERT INTO Lecturer (email, full_name, status, biography) VALUES ('$email', '$fullName', '$status', '$biography')";
            mysqli_query($conn, $query);

            // Insert the user into the account_table (only necessary if accounts are separate)
            $password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : password_hash("defaultPassword", PASSWORD_DEFAULT); // Hash password
            $query = "INSERT INTO account_table (email, password) VALUES ('$email', '$password')";
            mysqli_query($conn, $query);

            $conn->close();
            echo "<script>alert('Lecturer Added successfully!'); window.location.href = 'admin_management_lecacc.php';</script>";
        }
    }

    // Handle Delete User (GET)
    if (isset($_GET['delete'])) {
        $email = $_GET['delete'];

        // Delete lecturer from Lecturer table
        $query = "DELETE FROM Lecturer WHERE email = '$email'";
        mysqli_query($conn, $query);

        // Redirect back to the user management page (optional)
        $conn->close();
        header("Location: admin_management_lecacc.php");
        exit;
    }

    // Fetch Lecturers to display in the table
    $query = "SELECT * FROM Lecturer"; // Query updated to use 'Lecturer' table
    $result = mysqli_query($conn, $query);

    // Check if there are any records
    if (mysqli_num_rows($result) > 0) {
        $lecturers = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $lecturers = [];
    }
?>

<section>
    <!-- Manage lecturer account table -->
    <div class="container-fluid mt-5 manage_account_main">
    <h3 class="text-center text-primary"><?php echo "Total Number of Registered Lecturers: " . $total_lecturers; ?> </h3>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Status</th>
                        <th>Biography</th>
                        <th>Profile Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lecturers as $lecturer): ?>
                    <tr>
                        <td><?php echo $lecturer['email']; ?></td>
                        <td><?php echo $lecturer['full_name']; ?></td>
                        <td><?php echo $lecturer['status']; ?></td>
                        <td class="stuaccbioColumn"><?php echo $lecturer['biography']; ?></td>
                        <td>
                            <?php if (!empty($lecturer['profileimg'])): ?>
                                <img src="<?php echo $lecturer['profileimg']; ?>" alt="Profile Image" width="100" class="rounded-circle">
                            <?php else: ?>
                                <p class="text-muted">No image</p>
                            <?php endif; ?>
                        </td>
                        <td class="lectureraccAction">
                            <button class="editlecturerbtn btn btn-warning btn-sm" onclick="editUser('<?php echo $lecturer['email']; ?>')">Edit</button>
                            <button class="deletelecturerbtn btn btn-danger btn-sm" onclick="deleteUser('<?php echo $lecturer['email']; ?>')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <button class="btn btn-primary adduserBtn mt-3" data-toggle="modal" data-target="#userModal">Add Lecturer</button>
</div>

    <!-- Modal for Add/Edit Lecturer -->
    <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add/Edit Lecturer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="userForm" action="admin_management_lecacc.php" method="POST">
                        <input type="hidden" id="userEmail" name="email">
                        <div class="form-group">
                            <label for="fullName">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="biography">Biography</label>
                            <input type="text" class="form-control" id="biography" name="biography">
                        </div>
                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<?php
include 'include/footer.php';
?>

<!-- JavaScript for handling deletion, retrieve and display value for modal table-->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Function for retrieve and display value for modal table
    function editUser(email) {
        var rows = document.querySelectorAll("tr");
        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            if (row.cells[0].textContent === email) {
                var fullName = row.cells[1].textContent;
                var status = row.cells[2].textContent;
                var biography = row.cells[3].textContent;
                document.getElementById("fullName").value = fullName;
                document.getElementById("status").value = status;
                document.getElementById("biography").value = biography;
                document.getElementById("userEmail").value = email;
                
                // Show the modal
                $('#userModal').modal('show');
                break;
            }
        }
    }

    // Function to delete user
    function deleteUser(email) {
        if (confirm("Are you sure you want to delete this lecturer?")) {
            window.location.href = "admin_management_lecacc.php?delete=" + email;
        }
    }
</script>




<!-- Footer -->
<?php
        include 'include/footer.php';
    ?>
</body>
</html>

<?php
$conn->close();
?>
