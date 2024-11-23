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
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
               pi.invoice_id, pi.filepath AS payment_invoice_path, pi.status AS payment_invoice_status, pi.reason AS payment_invoice_reason,
               ts.transaction_id, ts.filepath AS transaction_slip_path,
               pr.receipt_id, pr.filepath AS payment_receipt_path,
               ecl.confirmation_id, ecl.filepath AS exam_confirmation_letter_path,
               er.result AS exam_result, er.publish AS publish,
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
                            <th>Registration Form</th>
                            <th>Payment Invoice</th>
                            <th>Transaction Slip</th>
                            <th>Payment Receipt</th>
                            <th>Confirmation Letter</th>
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
                                    <td><a href="<?= htmlspecialchars($registration['registration_form_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">View</a>
                                    </td>


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
                                                <!-- Action Button -->
                                                <br><br>


<!-- Action Button -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#actionModal">
    Action
</button>

<!-- Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel">Modal Table</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <tbody>
                        <tr>
                            <th>Option</th>
                            <th>Action</th>
                        </tr>
                    </tbody>
                    <tbody>
                        <tr>
                            <td>Choose:</td>
                            <td>
                                <div>
                                    <input type="radio" name="actionOption" id="accept" value="accept">
                                    <label for="accept">Accept</label>
                                </div>
                                <div>
                                    <input type="radio" name="actionOption" id="reject" value="reject">
                                    <label for="reject">Reject</label>
                                </div>
                            </td>
                        </tr>
                        <tr id="reasonRow" style="display: none;">
                            <td>Reason:</td>
                            <td>
                                <input type="text" class="form-control" id="reasonInput" placeholder="Enter your reason">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

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
                                                <a href="<?= htmlspecialchars($registration['transaction_slip_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                                            <?php else: ?>
                                                Please upload Transaction Slip. <br>

                                                <!-- Upload Transaction Slip -->
                                                <button type="button" class="btn btn-sm btn-info transactionUploadButton" 
                                                data-toggle="modal" 
                                                data-target="#uploadInvoiceModal"
                                                data-regtransaction-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                                data-transaction-id="<?= htmlspecialchars($registration['transaction_id']) ?>"
                                                data-transaction-filepath="<?= htmlspecialchars($registration['transaction_slip_path'] ?? '') ?>">
                                                    Upload
                                                </button>
                                                                                            


                                                <!---------------------------- MODAL table for Transaction Slip ---------------------------------->
                                                <div class="modal fade" id="uploadInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="uploadInvoiceModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="uploadInvoiceModalLabel">Upload Transaction Slip</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Form for file upload -->
                                                                <form method="POST" enctype="multipart/form-data" action="upload_transactionslip.php" class="mt-2">
                                                                    <div class="form-row align-items-center mb-3">
                                                                        <div class="col-md-4">
                                                                            <label for="TransactionFilePathModal" class="col-form-label">Uploaded Filepath:</label>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="text" name="displayfilepath" id="TransactionFilePathModal" class="form-control filePathDisplay" readonly>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-row align-items-center mb-3">
                                                                        <div class="col-md-4">
                                                                            <label class="col-form-label">Select File:</label>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <input type="file" name="transaction_slip" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
                                                                        </div>
                                                                    </div>
                                                                    <!---------- Hidden fields ------->
                                                                    <input type="hidden" name="registration_id" id="modalRegTransactionId">
                                                                    <input type="hidden" name="transaction_id" id="modalTransactionId">
                                                                    <!-------------------------------->
                                                                    <div class="text-right">
                                                                        <input type="submit" value="Upload" class="btn btn-primary">
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


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
                                            <?php if (!empty($registration['exam_result']) && $registration['publish'] === 'published'): ?>
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

    
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.4.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>

        /////////////////////// Javascript ////////////////////////////////
        // Display accurate information on modal table (Edit) 
        document.addEventListener("DOMContentLoaded", function () {
            const transactionUploadButtons = document.querySelectorAll(".transactionUploadButton");
            // Payment Invoice
            transactionUploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const transactionFilePath = this.getAttribute("data-transaction-filepath") || "";
                    const transactionId = this.getAttribute("data-transaction-id") || "";
                    const regtransactionId = this.getAttribute("data-regtransaction-id") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = transactionFilePath.substring(transactionFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("TransactionFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalRegTransactionId").value = regtransactionId;
                    document.getElementById("modalTransactionId").value = transactionId;
                });
            });
        });


        ////////////////////////////////// Reupload Files /////////////////////////////////////////
        // JavaScript to toggle reason text box
    $(document).ready(function () {
        $('input[name="actionOption"]').change(function () {
            if ($('#reject').is(':checked')) {
                $('#reasonRow').show();
            } else {
                $('#reasonRow').hide();
                $('#reasonInput').val(''); // Clear input when hidden
            }
        });

        $('#confirmButton').click(function () {
            const selectedOption = $('input[name="actionOption"]:checked').val();
            const reason = $('#reasonInput').val();

            if (selectedOption === 'reject' && !reason) {
                alert('Please provide a reason for rejection.');
                return;
            }

            alert(`You selected: ${selectedOption}${selectedOption === 'reject' ? ` with reason: ${reason}` : ''}`);
            $('#actionModal').modal('hide');
        });
    });


        //////////////////////////////////////   JQUERY   ////////////////////////////////////////////////
        // Display accurate information on modal table (Insert) JQuert
        // Payment Invoice
        $('#uploadInvoiceModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var regtransactionId = button.data('regtransaction-id'); // Extract info from data-* attributes
            var transactionId = button.data('transaction-id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('#modalRegTransactionId').val(regtransactionId); 
            modal.find('#modalTransactionId').val(transactionId); 

        });

    
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