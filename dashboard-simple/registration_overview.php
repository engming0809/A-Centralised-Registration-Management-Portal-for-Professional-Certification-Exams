<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}

$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $certStmt = $pdo->query("SELECT certification_id, certification_name FROM certifications");
    $certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);

    $certificationId = isset($_GET['certification']) ? $_GET['certification'] : null;

    $query = "
        SELECT r.registration_id, r.registration_status, r.created_at, r.updated_at, 
               r.student_id, r.certification_id, 
               c.certification_name, s.full_name, 
               rf.filepath AS registration_form_path,
               pi.invoice_id, pi.filepath AS payment_invoice_path,
               ts.filepath AS transaction_slip_path,
               pr.receipt_id, pr.filepath AS payment_receipt_path,
               ecl.confirmation_id,ecl.filepath AS exam_confirmation_letter_path,
               er.result AS exam_result,
               cert.filepath AS certificate_path
        FROM certificationregistrations r
        JOIN certifications c ON r.certification_id = c.certification_id
        LEFT JOIN student s ON r.student_id = s.student_id
        LEFT JOIN reg_registrationform rf ON r.registration_id = rf.registration_id
        LEFT JOIN reg_paymentinvoice pi ON r.registration_id = pi.registration_id
        LEFT JOIN reg_transactionslip ts ON r.registration_id = ts.registration_id
        LEFT JOIN reg_paymentreceipt pr ON r.registration_id = pr.registration_id
        LEFT JOIN reg_examconfirmationletter ecl ON r.registration_id = ecl.registration_id
        LEFT JOIN reg_examresult er ON r.registration_id = er.registration_id
        LEFT JOIN reg_certificate cert ON r.registration_id = cert.registration_id
    ";

    // Add filter condition
    if ($certificationId) {
        $query .= " WHERE r.certification_id = :certification_id";
    }

    $stmt = $pdo->prepare($query);
    if ($certificationId) {
        $stmt->bindParam(':certification_id', $certificationId);
    }
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard</title>
    <link rel="stylesheet" href="../dashboard-simple/style/style.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        object {
            width: 100%;
            height: 300px;
        }
    </style>
</head>

<body>
    <div class="main-content d-flex flex-column min-vh-100">

        <?php
        $pageTitle = "LMAO";
        include 'main_header.php';
        ?>

        <section class="welcome">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
            <p><a href="logout.php">Logout</a></p>
        </section>

        <section class="filter">
            <form method="GET" action="">
                <label for="certification">Filter by Certification:</label>
                <select name="certification" id="certification">
                    <option value="">All Certifications</option>
                    <?php foreach ($certifications as $certification): ?>
                        <option value="<?= htmlspecialchars($certification['certification_id']) ?>" <?= (isset($_GET['certification']) && $_GET['certification'] == $certification['certification_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($certification['certification_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Filter">
            </form>
        </section>

        <table id="certificationTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student ID</th>
                    <th>Certificate Name</th>
                    <th>Student Name</th>
                    <th>Registration Form</th>
                    <th>Payment Invoice</th>
                    <th>Transaction Slip</th>
                    <th>Payment Receipt</th>
                    <th>Exam Confirmation Letter</th>
                    <th>Exam Results</th>
                    <th>Certificate</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($registrations)): ?>
                    <?php foreach ($registrations as $registration): ?>
                        <tr>
                            <td><?= htmlspecialchars($registration['registration_id']) ?></td>
                            <td><?= htmlspecialchars($registration['student_id']) ?></td>
                            <td><?= htmlspecialchars($registration['certification_name']) ?></td>
                            <td><?= htmlspecialchars($registration['full_name'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (!empty($registration['registration_form_path'])): ?>
                                    <object data="<?= htmlspecialchars($registration['registration_form_path']) ?>" type="application/pdf">
                                    </object>
                                    <p><a href="<?= htmlspecialchars($registration['registration_form_path']) ?>">Download the PDF</a>.</p>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($registration['payment_invoice_path'])): ?>
                                    <object data="<?= htmlspecialchars($registration['payment_invoice_path']) ?>" type="application/pdf"></object>
                                <?php else: ?>
                                    <p>No invoice uploaded. Please upload a new invoice:</p>
                                    <form method="POST" enctype="multipart/form-data" action="upload_invoice.php">
                                        <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($registration['invoice_id']) ?>">
                                        <input type="file" name="payment_invoice" accept=".pdf" required>
                                        <input type="submit" value="Upload New Invoice">
                                    </form>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($registration['transaction_slip_path'])): ?>
                                    <object data="<?= htmlspecialchars($registration['transaction_slip_path']) ?>" type="application/pdf">
                                        <p><a href="<?= htmlspecialchars($registration['transaction_slip_path']) ?>">Download the PDF</a>.</p>
                                    </object>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($registration['payment_receipt_path'])): ?>
                                    <object data="<?= htmlspecialchars($registration['payment_receipt_path']) ?>" type="application/pdf"></object>
                                <?php else: ?>
                                    <p>No receipt uploaded. Please upload a new receipt:</p>
                                    <form method="POST" enctype="multipart/form-data" action="upload_receipt.php">
                                        <input type="hidden" name="receipt_id" value="<?= htmlspecialchars($registration['receipt_id']) ?>">
                                        <input type="file" name="payment_receipt" accept=".pdf" required>
                                        <input type="submit" value="Upload New Receipt">
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($registration['exam_confirmation_letter_path'])): ?>
                                      <object data="<?= htmlspecialchars($registration['exam_confirmation_letter_path']) ?>" type="application/pdf">
                                    </object>
                                    <p><a href="<?= htmlspecialchars($registration['exam_confirmation_letter_path']) ?>">Download the PDF</a>.</p>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($registration['exam_result'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <?php if (!empty($registration['certificate_path'])): ?>
                                    <object data="<?= htmlspecialchars($registration['certificate_path']) ?>" type="application/pdf"></object>
                                    </object>
                                    <p><a href="<?= htmlspecialchars($registration['certificate_path']) ?>">Download the PDF</a>.</p>

                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">No registrations found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> My Website. All rights reserved.</p>
        </footer>

    </div>
</body>

</html>