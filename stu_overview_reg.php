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

    <link rel="stylesheet" href="style/style.css">

<body>
    <!-- Header  -->
    <?php
    $pageTitle = "My Registrations";
    $pageHeaderClass = "header_image_reg_stu";
    $pageHeaderTitle = "My Registrations";
    $pageRegStuActive = "pageRegStuActive";
    include 'include/stu_main_header.php';
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

            $studentId = $_SESSION['student_id']; // Get the logged-in student ID

            $query = "
        SELECT r.registration_id, r.registration_status, r.created_at, r.updated_at, 
               r.student_id, r.certification_id, 
               c.certification_name, s.full_name, 
               rf.filepath AS registration_form_path,
               pi.invoice_id, pi.filepath AS payment_invoice_path,
               ts.transaction_id, ts.filepath AS transaction_slip_path,
               pr.receipt_id, pr.filepath AS payment_receipt_path,
               ecl.confirmation_id, ecl.filepath AS exam_confirmation_letter_path,
               er.result AS exam_result,
               cert.certificate_id, cert.filepath AS certificate_path, r.notification
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
        WHERE r.student_id = :student_id
    ";

            // Add filter condition for certification if provided
            if ($certificationId) {
                $query .= " AND r.certification_id = :certification_id";
            }

            // FINALISE retrieve the query from database 
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':student_id', $studentId); // Bind the student ID
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


            <!-- Session Info -->
            <p>Logged in as: <strong><?php echo $_SESSION['student_full_name']; ?></strong></p>
            <p>Your student ID is: <strong><?php echo $_SESSION['student_id']; ?></strong></p>

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
                            <th>Certificate Name</th>
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
                                    <td><?= htmlspecialchars($registration['certification_name']) ?></td>


                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'form_submitted' ||
                                            $registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted' ||
                                            $registration['registration_status'] === 'invoice_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['payment_invoice_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['payment_invoice_path']) ?>" class="btn btn-sm btn-info " onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && (empty($registration['transaction_slip_path']))) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>
                                            <?php else: ?>
                                                Please wait for lecturer to upload Payment Invoice.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted' ||
                                            $registration['registration_status'] === 'invoice_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['transaction_slip_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['transaction_slip_path']) ?>" class="btn btn-sm btn-info" target="_blank">Download</a>
                                            <?php else: ?>
                                                Please upload Transaction Slip.

                                                <form method="POST" enctype="multipart/form-data" action="upload_transactionslip.php" class="form-inline mt-2">
                                                    <input type="hidden" name="transaction_id" value="<?= htmlspecialchars($registration['transaction_id']) ?>">
                                                    <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>">
                                                    <input type="file" name="transaction_slip" accept=".pdf" class="form-control-file mb-2">
                                                    <input type="submit" value="Upload" class="btn btn-sm btn-primary">
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['payment_receipt_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['payment_receipt_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['exam_confirmation_letter_path'])) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>
                                                <?php else: ?>
                                                Please wait for lecturer to upload the Payment Receipt.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['exam_confirmation_letter_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['exam_confirmation_letter_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['certificate_path'])) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>
                                                <?php else: ?>
                                                Please wait for lecturer to uplaod Exam Confirmation Letter.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>


                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['exam_result'])): ?>
                                                <?= htmlspecialchars($registration['exam_result']) ?> 
                                            <?php else: ?>
                                                Please wait for Exam Result to be published.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>


                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['certificate_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['certificate_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1") { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>  
                                                <?php else: ?>
                                                Please wait for lecturer to upload the Certificate.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No registrations found.</td>
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
            "paging": true, // Enable pagination
            "ordering": true, // Enable column sorting
            "info": true, // Show table information
            "searching": true, // Enable search
            "stateSave": true, // Enable state saving
            "responsive": false
        });
    });

        function handleNotification(registration_id) {

            const data = new URLSearchParams(); // Create a URLSearchParams object
            data.append('registration_id', registration_id); // Add the registration_id

            fetch("clear_notification.php", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: data.toString(),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(result => {
                    console.log('Success:', result); // 
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>

</body>

</html>