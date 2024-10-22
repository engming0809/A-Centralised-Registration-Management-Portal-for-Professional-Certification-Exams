<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$sql = "SELECT * FROM stu_overview WHERE student_username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
</head>
<body>
    <div class="main-content d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Student Registration Overview";
    include 'main_header.php';
    ?>

    <section class="ro">
        <div><h2>Registration Overview</h2></div>
        <div class="mini-nav">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link active" href="student_dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Certification Overview</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Registration Overview</a>
                </li>
            </ul>
        </div>
        <div>
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                    <th scope="col">Certificate Name</th>
                    <th scope="col">Payment Invoice</th>
                    <th scope="col">Transaction Slip</th>
                    <th scope="col">Payment Receipt</th>
                    <th scope="col">Exam Confirmation Letter</th>
                    <th scope="col">Exam Results</th>
                    <th scope="col">Certificate</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <th scope="row"><?php echo htmlspecialchars($row['certificate_name']); ?></th>
                            <td>
                                <?php if ($row['payment_invoice']): ?>
                                    Available <button class="btn btn-primary btn-sm">Download</button>
                                <?php else: ?>
                                    Not Available
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['transaction_slip']): ?>
                                    Submitted on <?php echo htmlspecialchars($row['transaction_slip']); ?> 
                                    <button onclick="upload_file()" class="btn btn-primary btn-sm">Submit</button>
                                <?php else: ?>
                                    Not Submitted <button onclick="upload_file()" class="btn btn-primary btn-sm">Submit</button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['payment_receipt']): ?>
                                    Available <button class="btn btn-primary btn-sm">Download</button>
                                <?php else: ?>
                                    Not Available
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['exam_confirmation_letter']): ?>
                                    Available <button class="btn btn-primary btn-sm">Download</button>
                                <?php else: ?>
                                    Not Available
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['exam_results']); ?></td>
                            <td>
                                <?php if ($row['certificate']): ?>
                                    <button class="btn btn-primary btn-sm">Download</button>
                                <?php else: ?>
                                    Not Available
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
    <script>
    function upload_file() {
        const file_input = document.createElement('input');
        file_input.type = 'file';
        file_input.accept = '.pdf,.doc,.docx,.jpg,.png';

        file_input.onchange = () => {
            const file = file_input.files[0];
            if (file) {
                alert(`You selected: ${file.name}`);
            }
        };

        file_input.click();
    }
    </script>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> My Website. All rights reserved.</p>
    </footer>

    </div>
</body>
</html>
