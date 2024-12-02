
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Student Registrations</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JavaScript for handling deletion, retrieve and display value for modal table-->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>


    

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
        $studentid = $_POST['studentid'];
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

        $query = "SELECT * FROM Student WHERE student_id = '$studentid'";
        $result = mysqli_query($conn, $query);

        // Update or insert user into Student table
        if (mysqli_num_rows($result) > 0) {
            // Update Student record
            $query = "UPDATE Student SET full_name='$fullName', email='$email', status='$status', biography='$biography' WHERE student_id='$studentid'";
            mysqli_query($conn, $query);

            // Update password if necessary
            if ($hashedPassword) {
                $query = "UPDATE Student SET password='$hashedPassword' WHERE student_id='$studentid'";
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
        $query = "DELETE FROM Student WHERE student_id = '$student_id'";
        mysqli_query($conn, $query);

        // Redirect back to the user management page
        $conn->close();
        header("Location: admin_management_stuacc.php");
        exit;
    }


    // Fetch Students to display in the table
    $query = "SELECT * FROM Student"; // Query updated to use 'Student' table
    $result = mysqli_query($conn, $query);
    
    $countquery = "SELECT 
            COUNT(*) AS total_students, 
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_students, 
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_students, 
            SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) AS inactive_students 
          FROM Student"; 

    $countresult = mysqli_query($conn, $countquery);

    // Check if query was successful
    if ($countresult) {
        $countstudents = mysqli_fetch_assoc($countresult);
    } else {
        echo "Error fetching data: " . mysqli_error($conn);
        $countstudents = ['active_students' => 0, 'pending_students' => 0, 'inactive_students' => 0, 'total_students' => 0]; // default values if error occurs
    }

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


        <div class="totalusercontainer">
    <div class="totalusertable card shadow-sm mb-4 border-light rounded">
    <div class="card-header bg-danger text-white">
        <h4 class="text-center mb-0"> Student Status Statistic</h4>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th scope="col">Category</th>
                    <th scope="col">Currently Approved</th>
                    <th scope="col">Currently Pending</th>
                    <th scope="col">Currently Rejected</th>
                    <th scope="col">Total Number of Student</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Students</td>
                    <td><?php echo $countstudents['active_students']; ?></td>
                    <td><?php echo $countstudents['pending_students']; ?></td>
                    <td><?php echo $countstudents['inactive_students']; ?></td>
                    <td><?php echo $countstudents['total_students']; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
    <div class="d-flex justify-content-end">
        <button class="btn btn-primary adduserBtn" data-toggle="modal" data-target="#userModal">Add New Student</button>
    </div>
</div>

    <div class="row">
        <div class="col-md-12">
            <table id="studentTable" class="table table-striped table-hover table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Profile Image</th>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Status</th>
                        <th>Biography</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo $student['student_id']; ?></td>
                        <td>
                            <?php if (!empty($student['profileimg'])): ?>
                                <img src="<?php echo $student['profileimg']; ?>" alt="Profile Image" class="adminimage rounded-circle">
                            <?php else: ?>
                                <p class="text-muted">No image</p>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo $student['full_name']; ?></td>
                        <td><?php echo $student['status']; ?></td>
                        <td class="stuaccbioColumn"><?php echo $student['biography']; ?></td>
                        <td class="stuaccAction">
                            <button class="editstudentbtn btn btn-warning btn-sm" onclick="editUser('<?php echo $student['student_id']; ?>')">Edit</button>
                            <button class="deletestudentbtn btn btn-danger btn-sm" onclick="deleteUser('<?php echo $student['student_id']; ?>')">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
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
                            <input type="hidden" id="student_id" name="studentid">
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


<script>

    // Function for retrieve and display value for modal table
    function editUser(student_id) {
        var rows = document.querySelectorAll("tr");
        for (var i = 1; i < rows.length; i++) {
            var row = rows[i];
            if (row.cells[0].textContent === student_id) {
                var email = row.cells[2].textContent;
                var fullName = row.cells[3].textContent;
                var status = row.cells[4].textContent;
                var biography = row.cells[5].textContent;

                // Set the form fields with the retrieved values
                document.getElementById('student_id').value = student_id;
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
    function deleteUser(student_id) {
        if (confirm("Are you sure you want to delete this student?")) {
            window.location.href = 'admin_management_stuacc.php?delete=' + student_id;
        }
    }

    
    //Data table
    $(document).ready(function() {
        $('#studentTable').DataTable({
            paging: true,      // Enable pagination
            searching: true,   // Enable search functionality
            ordering: true,    // Enable column sorting
            lengthChange: true // Allow users to change the number of rows displayed
        });
    });
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
