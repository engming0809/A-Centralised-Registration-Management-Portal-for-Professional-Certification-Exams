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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>
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
            $certStmt = $pdo->query("SELECT certification_id, certification_name, schedule FROM certifications");
            $certifications = $certStmt->fetchAll(PDO::FETCH_ASSOC);
            $certificationId = isset($_GET['certification']) ? $_GET['certification'] : null;

            $query = "
        SELECT r.registration_id, r.registration_status, r.created_at, r.updated_at, 
               r.student_id, r.certification_id, 
               s.email as studentemail,
               c.certification_name, s.full_name, c.schedule,
               rf.filepath AS registration_form_path,
               pi.invoice_id, pi.filepath AS payment_invoice_path,
               ts.filepath AS transaction_slip_path,
               pr.receipt_id, pr.filepath AS payment_receipt_path,
               ecl.confirmation_id,ecl.filepath AS exam_confirmation_letter_path,
               er.examresult_id, er.result AS exam_result, er.publish as publish,
               cert.certificate_id,cert.filepath AS certificate_path, r.notification
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
            <p> Email is: <strong><?php echo $_SESSION['lecturer_id']; ?></strong></p>
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
                            <th>Schedule</th>
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

                                    <?php
                                    // Get the student's email
                                    $studentEmail = htmlspecialchars($registration['studentemail']);
                                    ?>

                                    <!-- HTML with tooltip and copy button -->
                                    <td class="email-cell" title="<?= $studentEmail ?>">  <!-- Tooltip added here -->
                                        <span class="short-email"><?= substr($studentEmail, 0, 9) ?></span>  <!-- Display only the first 9 characters -->
                                        <button class="copy-button btn btn-sm btn-info" onclick="copyEmail('<?= $studentEmail ?>')">Copy Email</button>
                                    </td>


                                    <td><?php
                                        $schedule = new DateTime($registration['schedule']);
                                        echo htmlspecialchars($schedule->format('m/d/Y, h:i A'));?>
                                    </td>
                                    <td><?= htmlspecialchars($registration['certification_name']) ?></td>
                                    <td><?= htmlspecialchars($registration['full_name'] ?? 'N/A') ?></td>

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
                                            <?php if (!empty($registration['registration_form_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['registration_form_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['payment_invoice_path'])) { ?>
                                                    <span class="notification" ></span>
                                                <?php } ?>
                                            <?php else: ?>
                                                Please wait for lecturer to upload Payment Invoice
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <!-- Payment Invoice Column -->
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
                                                <a href="<?= htmlspecialchars($registration['payment_invoice_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                                            <?php else: ?>
                                                Please upload Payment Invoice
                                            <?php endif; ?>

                                            <?php if (
                                                $registration['registration_status'] === 'form_submitted' ||
                                                $registration['registration_status'] === 'transaction_submitted' ||
                                                $registration['registration_status'] === 'receipt_submitted' ||
                                                $registration['registration_status'] === 'examletter_submitted' ||
                                                $registration['registration_status'] === 'result_submitted' ||
                                                $registration['registration_status'] === 'certificate_submitted' ||
                                                $registration['registration_status'] === 'invoice_submitted'
                                            ): ?>
                                                        <form method="POST" enctype="multipart/form-data" action="upload_invoice.php">
                                                            <input type="hidden" name="invoice_id" value="<?= htmlspecialchars($registration['invoice_id']) ?>">
                                                            <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>">
                                                            <input type="file" name="payment_invoice" accept=".pdf" class="form-control-file" style="margin-bottom: 15px;" required>
                                                            <input type="submit" value="Upload" class="btn btn-primary">
                                                        </form>


                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <!-- Transaction Slip Column -->
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
                                                <a href="<?= htmlspecialchars($registration['transaction_slip_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['payment_receipt_path'])) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>
                                            <?php else: ?>
                                                Please wait for student to upload Transaction Slip.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <!-- Payment Receipt Column -->
                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
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
                                        <?php if (
                                            $registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
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
                                        <?php if (
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['exam_result'])): ?>
                                                <?= htmlspecialchars($registration['exam_result']) ?><br>
                                                <a href="#" class="btn btn-sm btn-info editButton"
                                                    data-toggle="modal"
                                                    data-target="#resultModal"
                                                    data-registration-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                                    data-examresult-id="<?= htmlspecialchars($registration['examresult_id']) ?>"
                                                    data-exam-result="<?= htmlspecialchars($registration['exam_result'] ?? '') ?>"
                                                    data-publish="<?= htmlspecialchars($registration['publish'] ?? 'not_published') ?>">
                                                    Edit
                                                </a>

                                            <?php else: ?>
                                                Please key in Exam Result.
                                                <a href="#" class="btn btn-sm btn-info"
                                                    data-toggle="modal"
                                                    data-target="#resultModal"
                                                    data-registration-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                                    data-examresult-id="<?= htmlspecialchars($registration['examresult_id']) ?>">Insert</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <!---------------------------- MODAL table for result ---------------------------------->
                                    <div id="resultModal" class="modal fade" tabindex="-1" role="dialog">
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
                                                            <input type="text" name="examResult" id="examResultModal" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Publish this result:</label><br>
                                                            <div>
                                                                <input type="radio" name="publish" id="publishYes" value="published">
                                                                <label for="publishYes">Yes</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" name="publish" id="publishNo" value="not_published" checked>
                                                                <label for="publishNo">No</label>
                                                            </div>
                                                        </div>
                                                        <!-- Hidden fields to store data to be sent to upload php files -->
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


                                    <!-- Certificate Column -->
                                    <td>
                                        <?php if (
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        ): ?>
                                            <?php if (!empty($registration['certificate_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['certificate_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                                            <?php else: ?>
                                                Please upload Certificate.
                                            <?php endif; ?>

                                            <!-- <form method="POST" enctype="multipart/form-data" action="upload_certificate.php" class="form-inline mt-2">
                                                <input type="hidden" name="certificate_id" value="<?= htmlspecialchars($registration['certificate_id']) ?>">
                                                <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>">
                                                <input type="file" name="certificate" accept=".pdf" class="form-control-file mb-2">
                                                <input type="submit" value="Upload" class="btn btn-sm btn-primary">
                                            </form> -->

                                            <button type="button" class="btn btn-sm btn-info certificateUploadButton" 
                                            data-toggle="modal" 
                                            data-target="#uploadModal"
                                            data-regcertificate-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                            data-certificate-id="<?= htmlspecialchars($registration['certificate_id']) ?>">
                                                Upload Certificate
                                            </button>
                                            


                                            <!-- Modal Structure -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Certificate</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form for file upload -->
                <form method="POST" enctype="multipart/form-data" action="upload_certificate.php" class="form-inline mt-2">
                    <!-- Hidden input -->
                    <!-- <input type="hidden" name="certificate_id" value="<?= htmlspecialchars($registration['certificate_id']) ?>">
                    <input type="hidden" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>"> -->
                    <!-- Not so hidden input  -->
                    <input type="text" name="certificate_id" value="<?= htmlspecialchars($registration['certificate_id']) ?>" class="form-control mb-2">
                    <input type="text" name="registration_id" value="<?= htmlspecialchars($registration['registration_id']) ?>" class="form-control mb-2">
                
                    <div class="form-group">
                        <label for="certificate" class="mr-2">Select Certificate</label>
                        <input type="file" name="certificate" accept=".pdf" class="form-control-file mb-2" id="certificate">
                    </div>

                    
                <!-- Hidden fields to store data to be sent to upload php files -->
                <input type="hidden" name="registration_id" id="modalRegCertificateId">
                <input type="hidden" name="certificate_id" id="modalCertificateId">
                    <input type="submit" value="Upload" class="btn btn-sm btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>

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
        /////////////////////// Javascript ////////////////////////////////
        // Display accurate information on modal table (Edit) Javascript
        document.addEventListener("DOMContentLoaded", function () {
            const editButtons = document.querySelectorAll(".editButton");
            const certificateUploadButtons = document.querySelectorAll(".certificateUploadButton");

            // For Exam Result
            editButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const examResult = this.getAttribute("data-exam-result") || "";
                    const registrationId = this.getAttribute("data-registration-id") || "";
                    const examResultId = this.getAttribute("data-examresult-id") || "";
                    const publish = this.getAttribute("data-publish") || "not_published";  // Default to 'not_published'

                    // Set modal field values (Display)
                    document.getElementById("examResultModal").value = examResult;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalRegistrationId").value = registrationId;
                    document.getElementById("modalExamResultId").value = examResultId;

                    // Set the correct publish radio button
                    if (publish === "published") {
                        document.getElementById("publishYes").checked = true;
                    } else {
                        document.getElementById("publishNo").checked = true;
                    }
                });
            });

            // For Certificate Upload
            certificateUploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const certificateId = this.getAttribute("data-certificate-id") || "";
                    const regcertificateId = this.getAttribute("data-regcertificate-id") || "";

                    // Set modal field values (Display)
                    // document.getElementById("examResultModal").value = examResult;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalRegCertificateId").value = regcertificateId;
                    document.getElementById("modalCertificateId").value = certificateId;

                    // Set the correct publish radio button
                    if (publish === "published") {
                        document.getElementById("publishYes").checked = true;
                    } else {
                        document.getElementById("publishNo").checked = true;
                    }
                });
            });


        });

        //////////////////////////////////////   JQUERY   ////////////////////////////////////////////////
        // Display accurate information on modal table (Insert) JQuert
        // Exam Result
        $('#resultModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var registrationId = button.data('registration-id'); // Extract info from data-* attributes
            var examResultId = button.data('examresult-id'); // Extract info from data-* attributes
            var publish = button.data('publish'); // Extract publish status (either 'published' or 'not_published')

            var modal = $(this);
            modal.find('#modalRegistrationId').val(registrationId); // Set the value of registration_id in modal
            modal.find('#modalExamResultId').val(examResultId); // Set the value of examresult_id in modal

            // Set the correct publish radio button
            if (publish === 'published') {
                modal.find('#publishYes').prop('checked', true);  // Set "Yes" if published
            } else {
                modal.find('#publishNo').prop('checked', true);  // Set "No" if not published
            }
        });

        // Certificate
        $('#uploadModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var regcertificateId = button.data('regcertificate-id'); // Extract info from data-* attributes
            var certificatetId = button.data('certificate-id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('#modalRegCertificateId').val(regcertificateId); // Set the value of registration_id in modal
            modal.find('#modalCertificateId').val(certificatetId); // Set the value of examresult_id in modal

        });
        //////////////////////////////////////////////////////////////////////////////////////////////////

        //Function to copy email
        function copyEmail(email) {
            // Create a temporary textarea element
            var tempInput = document.createElement('textarea');
            tempInput.value = email; // Set the email value to the input
            document.body.appendChild(tempInput); // Append it to the body
            tempInput.select(); // Select the text
            document.execCommand('copy'); // Copy the selected text to clipboard
            document.body.removeChild(tempInput); // Remove the textarea from the DOM

            // Optional: Change button text to "Copied" temporarily
            var button = event.target;
            button.textContent = 'Copied!';
            setTimeout(() => {
                button.textContent = 'Copy Email';
            }, 1500); // Reset text after 1.5 seconds
        }


       // Data Table
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

        // Function for Basic Notificaiton 
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