<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <link rel="stylesheet" href="style/style.css">
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Registration Overview";
        $pageHeaderClass = "header_image_reg_lec";
        $pageHeaderTitle = "Registration Overview";
        $pageRegLecActive = "pageRegLecActive";
        include 'include/lec_main_header.php';
    ?>

<!-- Main Content -->
    <main>

    <?php
session_start();

$host = '127.0.0.1';
$db = 'cert_reg_management_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // INITIALISE retrieve the query from database 
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
               er.examresult_id, er.result AS exam_result,
               cert.certificate_id,cert.filepath AS certificate_path
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

    // FINALISE retrieve the query from database 
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
    <div class="container-fluid  lec_overview_reg_main">
    <!-- Welcome Section -->
    <!-- example -->
	<!-- Display session info -->
            <p>Logged in as: <strong><?php echo $_SESSION['lecturer_full_name']; ?></strong></p>
            <p> Email is: <strong><?php echo $_SESSION['lecturer_email']; ?></strong></p>
	<!-- example -->
    <!-- Filter Section -->
    <section class="filter mb-4">
        <form method="GET" action="" class="form-inline">
            <label for="certification" class="mr-2">Filter by Certification:</label>
            <select name="certification" id="certification" class="form-control mr-2">
                <option value="">All Certifications</option>
                <?php foreach ($certifications as $certification): ?>
                    <option value="<?= htmlspecialchars($certification['certification_id']) ?>" 
                        <?= (isset($_GET['certification']) && $_GET['certification'] == $certification['certification_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($certification['certification_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Filter" class="btn btn-primary">
        </form>
    </section>

    <!-- Responsive Table -->
    <div class="table-responsive">
    <table id="certificationTable" class="table table-bordered table-striped table-hover">
    <thead class="thead-dark">
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

            <!-- Form Column -->
            <td>
                <?php if ($registration['registration_status'] === 'form_submitted' || 
                $registration['registration_status'] === 'transaction_submitted' || 
                $registration['registration_status'] === 'receipt_submitted' ||  
                $registration['registration_status'] === 'examletter_submitted' || 
                $registration['registration_status'] === 'result_submitted' || 
                $registration['registration_status'] === 'certificate_submitted' ||  
                $registration['registration_status'] === 'invoice_submitted'): ?>
                    <?php if (!empty($registration['registration_form_path'])): ?>
                        <a href="<?= htmlspecialchars($registration['registration_form_path']) ?>" class="btn btn-sm btn-info" target="_blank">Download</a>
                    <?php else: ?>
                        Please wait for lecturer to upload Payment Invoice
                    <?php endif; ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>

            <!-- Payment Invoice Column -->
            <td>
                <?php if ($registration['registration_status'] === 'form_submitted' || 
                $registration['registration_status'] === 'transaction_submitted' || 
                $registration['registration_status'] === 'receipt_submitted' ||  
                $registration['registration_status'] === 'examletter_submitted' || 
                $registration['registration_status'] === 'result_submitted' || 
                $registration['registration_status'] === 'certificate_submitted' || 
                $registration['registration_status'] === 'invoice_submitted'): ?>
                    <?php if (!empty($registration['payment_invoice_path'])): ?>
                        <a href="<?= htmlspecialchars($registration['payment_invoice_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                    <?php else: ?>
                        Please upload Payment Invoice
                    <?php endif; ?>
                    
                    <?php if ($registration['registration_status'] === 'form_submitted' || 
                    $registration['registration_status'] === 'transaction_submitted' || 
                    $registration['registration_status'] === 'receipt_submitted' ||  
                    $registration['registration_status'] === 'examletter_submitted' || 
                    $registration['registration_status'] === 'result_submitted' || 
                    $registration['registration_status'] === 'certificate_submitted' || 
                    $registration['registration_status'] === 'invoice_submitted'): ?>
                    <form method="POST" enctype="multipart/form-data" action="upload_invoice.php" class="form-inline mt-2">
                            <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($registration['invoice_id']) ?>">
                            <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>"> 
                            <input type="file" name="payment_invoice" accept=".pdf" class="form-control-file mb-2">
                            <input type="submit" value="Upload" class="btn btn-sm btn-primary">
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>

            <!-- Transaction Slip Column -->
            <td>
                <?php if ($registration['registration_status'] === 'transaction_submitted' || 
                $registration['registration_status'] === 'receipt_submitted' ||  
                $registration['registration_status'] === 'examletter_submitted' || 
                $registration['registration_status'] === 'result_submitted' || 
                $registration['registration_status'] === 'certificate_submitted' || 
                $registration['registration_status'] === 'invoice_submitted'): ?>
                    <?php if (!empty($registration['transaction_slip_path'])): ?>
                        <a href="<?= htmlspecialchars($registration['transaction_slip_path']) ?>" class="btn btn-sm btn-info" target="_blank">Download</a>
                    <?php else: ?>
                        Please wait for student to upload Transaction Slip.
                    <?php endif; ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>

            <!-- Payment Receipt Column -->
            <td>
                <?php if ($registration['registration_status'] === 'transaction_submitted' || 
                $registration['registration_status'] === 'receipt_submitted' ||  
                $registration['registration_status'] === 'examletter_submitted' || 
                $registration['registration_status'] === 'result_submitted' || 
                $registration['registration_status'] === 'certificate_submitted' ): ?>
                    <?php if (!empty($registration['payment_receipt_path'])): ?>
                        <a href="<?= htmlspecialchars($registration['payment_receipt_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                    <?php else: ?>
                        Please upload Payment Receipt.
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" action="upload_receipt.php" class="form-inline mt-2">
                        <input type="hidden" name="receipt_id" value="<?= htmlspecialchars($registration['receipt_id']) ?>">
                        <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>"> 
                        <input type="file" name="payment_receipt" accept=".pdf" class="form-control-file mb-2">
                        <input type="submit" value="Upload" class="btn btn-sm btn-primary">
                    </form>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>

            <!-- Exam Confirmation Letter Column -->
            <td>
                <?php if ($registration['registration_status'] === 'transaction_submitted' || 
                $registration['registration_status'] === 'receipt_submitted' ||  
                $registration['registration_status'] === 'examletter_submitted' || 
                $registration['registration_status'] === 'result_submitted' || 
                $registration['registration_status'] === 'certificate_submitted'): ?>
                    <?php if (!empty($registration['exam_confirmation_letter_path'])): ?>
                        <a href="<?= htmlspecialchars($registration['exam_confirmation_letter_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                    <?php else: ?>
                        Please upload Exam Confirmation Letter.
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" action="upload_examconfirmationletter.php" class="form-inline mt-2">
                        <input type="hidden" name="confirmation_id" value="<?= htmlspecialchars($registration['confirmation_id']) ?>">
                        <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>"> 
                        <input type="file" name="exam_confirmation_letter" accept=".pdf" class="form-control-file mb-2">
                        <input type="submit" value="Upload" class="btn btn-sm btn-primary">
                    </form>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </td>

            <!-- Exam Result Column -->
            <!---------------------------- VIEW the result ---------------------------------->
            <td>
            <?php if ($registration['registration_status'] === 'examletter_submitted' ||
                    $registration['registration_status'] === 'result_submitted' || 
                    $registration['registration_status'] === 'certificate_submitted'): ?>
                <?php if (!empty($registration['exam_result'])): ?>
                    <?= htmlspecialchars($registration['exam_result']) ?>
                    <a href="#" class="btn btn-sm btn-info" 
                    data-toggle="modal" 
                    data-target="#resultModal" 
                    data-registration-id="<?= htmlspecialchars($registration['registration_id']) ?>" 
                    data-examresult-id="<?= htmlspecialchars($registration['examresult_id']) ?>">Edit Result</a>
                <?php else: ?>
                    Please key in Exam Result.
                    <a href="#" class="btn btn-sm btn-info" 
                    data-toggle="modal" 
                    data-target="#resultModal" 
                    data-registration-id="<?= htmlspecialchars($registration['registration_id']) ?>" 
                    data-examresult-id="<?= htmlspecialchars($registration['examresult_id']) ?>">Insert Result</a>
                <?php endif; ?>
            <?php else: ?>
                N/A
            <?php endif; ?>
        </td>

       <!---------------------------- MODAL table for result ---------------------------------->
<div id="resultModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="examResultForm" action="upload_result.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Submit Exam Result</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="examResult">Exam Result</label>
                        <input type="text" name="examResult" id="examResult" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Publish this result:</label><br>
                        <div>
                            <input type="radio" name="publish" id="publishYes" value="1">
                            <label for="publishYes">Yes</label>
                        </div>
                        <div>
                            <input type="radio" name="publish" id="publishNo" value="0" checked>
                            <label for="publishNo">No</label>
                        </div>
                    </div>
                    <!-- Hidden fields to store dynamic data -->
                    <input type="hidden" name="registration_id" id="modalRegistrationId">
                    <input type="hidden" name="examresult_id" id="modalExamResultId">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <script>
        // jQuery to update the hidden inputs in the modal when the button is clicked
        $('#resultModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var registrationId = button.data('registration-id'); // Extract info from data-* attributes
            var examResultId = button.data('examresult-id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('#modalRegistrationId').val(registrationId); // Set the value of registration_id in modal
            modal.find('#modalExamResultId').val(examResultId); // Set the value of examresult_id in modal
        });
    </script>


            <!-- Certificate Column -->
            <td>
                <?php if (  
                $registration['registration_status'] === 'examletter_submitted' || 
                $registration['registration_status'] === 'result_submitted' || 
                $registration['registration_status'] === 'certificate_submitted'): ?>
                    <?php if (!empty($registration['certificate_path'])): ?>
                        <a href="<?= htmlspecialchars($registration['certificate_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                    <?php else: ?>
                        Please upload Certificate.
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" action="upload_certificate.php" class="form-inline mt-2">
                        <input type="hidden" name="certificate_id" value="<?= htmlspecialchars($registration['certificate_id']) ?>">
                        <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>"> 
                        <input type="file" name="certificate" accept=".pdf" class="form-control-file mb-2">
                        <input type="submit" value="Upload" class="btn btn-sm btn-primary">
                    </form>
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

    </div>





</div>

        
    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>


<script>
    $(document).ready(function() {
        $('#certificationTable').DataTable({
            "paging": true,          // Enable pagination
            "ordering": true,       // Enable column sorting
            "info": true,           // Show table information
            "searching": true       // Enable search
        });
    });
</script>

</body>
</html>



