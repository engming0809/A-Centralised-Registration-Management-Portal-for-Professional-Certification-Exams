
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
        $pageTitle = "Manage Student Account";
        $pageHeaderClass = "header_image_admin_main";
        $pageHeaderTitle = "Manage Student Account";
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


    $query = "SELECT * FROM Student"; // Query updated to use 'Student' table
    $result = mysqli_query($conn, $query);

    // Total Number of Students
    $total_students = mysqli_num_rows($result);

    // Handle users through edit or add from modal table (POST)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = $_POST['email'];
        $fullName = $_POST['full_name'];
        $status = $_POST['status']; // Student status field added
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

        $query = "SELECT * FROM Student WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        // Update or insert user into Student table
        if (mysqli_num_rows($result) > 0) {
            // Update Student record
            $query = "UPDATE Student SET full_name='$fullName', email='$email', status='$status', biography='$biography' WHERE email='$email'";
            mysqli_query($conn, $query);

            // Update password if necessary
            if ($hashedPassword) {
                $query = "UPDATE Student SET password='$hashedPassword' WHERE email='$email'";
                mysqli_query($conn, $query);
            }
            
            $conn->close();
            echo "<script>alert('Student information updated successfully!'); window.location.href = 'admin_management_stuacc.php';</script>";
        } else {
            // Insert new Student into Student table
            $query = "INSERT INTO Student (email, full_name, status, biography) VALUES ('$email', '$fullName', '$status', '$biography')";
            mysqli_query($conn, $query);

            $conn->close();
            echo "<script>alert('Student Added successfully!'); window.location.href = 'admin_management_stuacc.php';</script>";
        }
    }

   // Handle Delete User (GET)
    if (isset($_GET['delete'])) {
        $email = $_GET['delete'];

        // Get the student_id for the email to ensure we delete the correct student
        $result = mysqli_query($conn, "SELECT student_id FROM Student WHERE email = '$email'");
        $row = mysqli_fetch_assoc($result);
        $student_id = $row['student_id'];

        // Delete related records in reg_registrationform first (to avoid foreign key constraint)
        $query = "DELETE FROM reg_registrationform WHERE registration_id IN (SELECT registration_id FROM certificationregistrations WHERE student_id = '$student_id')";
        mysqli_query($conn, $query);

        $query = "DELETE FROM reg_PaymentInvoice WHERE registration_id IN (SELECT registration_id FROM certificationregistrations WHERE student_id = '$student_id')";
        mysqli_query($conn, $query);
        
        $query = "DELETE FROM reg_TransactionSlip WHERE registration_id IN (SELECT registration_id FROM certificationregistrations WHERE student_id = '$student_id')";
        mysqli_query($conn, $query);

        $query = "DELETE FROM reg_PaymentReceipt WHERE registration_id IN (SELECT registration_id FROM certificationregistrations WHERE student_id = '$student_id')";
        mysqli_query($conn, $query);

        
        $query = "DELETE FROM reg_ExamConfirmationLetter WHERE registration_id IN (SELECT registration_id FROM certificationregistrations WHERE student_id = '$student_id')";
        mysqli_query($conn, $query);

        
        $query = "DELETE FROM reg_ExamResult WHERE registration_id IN (SELECT registration_id FROM certificationregistrations WHERE student_id = '$student_id')";
        mysqli_query($conn, $query);

        
        $query = "DELETE FROM reg_Certificate WHERE registration_id IN (SELECT registration_id FROM certificationregistrations WHERE student_id = '$student_id')";
        mysqli_query($conn, $query);

        // Delete from certificationregistrations table
        $query = "DELETE FROM certificationregistrations WHERE student_id = '$student_id'";
        mysqli_query($conn, $query);

        // Delete student from Student table
        $query = "DELETE FROM Student WHERE email = '$email'";
        mysqli_query($conn, $query);

        // Redirect back to the user management page
        $conn->close();
        header("Location: admin_management_stuacc.php");
        exit;
    }


    // Fetch Students to display in the table
    $query = "SELECT * FROM Student"; // Query updated to use 'Student' table
    $result = mysqli_query($conn, $query);

    // Check if there are any records
    if (mysqli_num_rows($result) > 0) {
        $students = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $students = [];
    }

    ?>

    <section>
        <!-- Manage student account table -->
        <div class="container-fluid mt-5 manage_account_main">
    <h3 class="text-center text-primary"><?php echo "Total Number of Registered Students: " . $total_students; ?> </h3>
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
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo $student['full_name']; ?></td>
                        <td><?php echo $student['status']; ?></td>
                        <td class="stuaccbioColumn"><?php echo $student['biography']; ?></td>
                        <td>
                            <?php if (!empty($student['profileimg'])): ?>
                                <img src="<?php echo $student['profileimg']; ?>" alt="Profile Image" width="100" class="rounded-circle">
                            <?php else: ?>
                                <p class="text-muted">No image</p>
                            <?php endif; ?>
                        </td>
                        <td class="stuaccAction">
                            <button class="editstudentbtn btn btn-warning btn-sm" onclick="editUser('<?php echo $student['email']; ?>')">Edit</button>
                            <button class="deletestudentbtn btn btn-danger btn-sm" onclick="deleteUser('<?php echo $student['email']; ?>')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <button class="btn btn-primary adduserBtn mt-3" data-toggle="modal" data-target="#userModal">Add Student</button>
</div>


        <!-- Modal for Add/Edit Student -->
        <div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Add/Edit Student</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm" action="admin_management_stuacc.php" method="POST">
                            <!-- <input type="hidden" id="userEmail" name="email"> -->
                            <div class="form-group">
                                <label for="userEmail">Email</label>
                                <input type="text" class="form-control" id="userEmail" name="email" required>
                            </div>
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

</main>

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

                // Set the form fields with the retrieved values
                document.getElementById('userEmail').value = email;
                document.getElementById('fullName').value = fullName;
                document.getElementById('status').value = status;
                document.getElementById('biography').value = biography;
                
                // Show the modal
                $('#userModal').modal('show');
                break;
            }
        }
    }

    // Function for deletion
    function deleteUser(email) {
        if (confirm("Are you sure you want to delete this student?")) {
            window.location.href = 'admin_management_stuacc.php?delete=' + email;
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
