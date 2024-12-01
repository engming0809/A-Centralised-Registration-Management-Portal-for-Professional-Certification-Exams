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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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

    
<?php
// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['lecturer_full_name'])) {
    header("Location: index.php");
    exit();
}
?>


    <!-- Main Content -->
    <main>

        <?php

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
            $resultStatus = isset($_GET['result_status']) ? $_GET['result_status'] : null;
        

            $query = "
SELECT r.registration_id, r.registration_status, r.result_status, r.created_at, r.updated_at, 
r.student_id, r.certification_id, 
s.email as studentemail,
c.certification_name, s.full_name,  c.schedule,
DATEDIFF(c.schedule, CURRENT_DATE) AS deadline,
rf.form_id, rf.filepath AS registration_form_path, rf.status AS registration_form_status, rf.reason AS registration_form_reason,
pi.invoice_id, pi.filepath AS payment_invoice_path, pi.status AS payment_invoice_status, pi.reason AS payment_invoice_reason,
ts.transaction_id, ts.filepath AS transaction_slip_path, ts.status AS transaction_slip_status, ts.reason AS transaction_slip_reason,
pr.receipt_id, pr.filepath AS payment_receipt_path, pr.status AS payment_receipt_status, pr.reason AS payment_receipt_reason,
ecl.confirmation_id, ecl.filepath AS exam_confirmation_letter_path, ecl.status AS exam_confirmation_letter_status, ecl.reason AS exam_confirmation_letter_reason,
er.examresult_id, er.result AS exam_result, er.publish AS publish, er.status AS exam_status,
cert.certificate_id, cert.filepath AS certificate_path, cert.status AS certificate_status, cert.reason AS certificate_reason, r.notification
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

            /// Add filter conditions
            $conditions = [];
            if ($certificationId) {
                $conditions[] = "r.certification_id = :certification_id";
            }
            if ($resultStatus) {
                $conditions[] = "r.result_status = :result_status";
            }

            // If there are any filter conditions, add them to the query
            if (count($conditions) > 0) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            // FINALISE retrieve the query from database 
            $stmt = $pdo->prepare($query);

            // Bind parameters
            if ($certificationId) {
                $stmt->bindParam(':certification_id', $certificationId);
            }
            if ($resultStatus) {
                $stmt->bindParam(':result_status', $resultStatus);
            }

            $stmt->execute();
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }

        // Check if result_status is not set in the query parameters
        if (!isset($_GET['result_status'])) {
            // Redirect to the same page with the default filter applied
            header("Location: " . $_SERVER['PHP_SELF'] . "?result_status=pending");
            exit();
        }

        // Set the default value for result_status based on the query parameter
        $result_status = $_GET['result_status'];

        ?>
        <div class="container-fluid  lec_overview_reg_main">
            <!-- Welcome Section -->
             
            <!-- Filter Section -->
            <section class="filter mb-4">
                <form method="GET" action="" class="form-inline">
                    <label for="result_status" class="mr-2">Filter by Result Status:</label>
                    <select name="result_status" id="result_status" class="form-control mr-2">
                        <option value="" <?= ($result_status == '') ? 'selected' : '' ?>>All Result Status</option>
                        <option value="pending" <?= ($result_status == 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="completed" <?= ($result_status == 'completed') ? 'selected' : '' ?>>Completed</option>
                        <option value="incomplete" <?= ($result_status == 'incomplete') ? 'selected' : '' ?>>Incomplete</option>
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
                            <th>Student Name</th>
                            <th>Certification Name</th>
                            <th>Schedule</th>
                            <th>Registration Form</th>
                            <th>Payment Invoice</th>
                            <th>Transaction Slip</th>
                            <th>Payment Receipt</th>
                            <th>Exam Confirmation Letter</th>
                            <th>Exam Results</th>
                            <th>Certificate</th>
                            <th>Status</th>
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
                                    <td><?= htmlspecialchars($registration['full_name'] ?? 'N/A') ?></td>


                                    <td><?= htmlspecialchars($registration['certification_name']) ?></td>
                                    
                                    <td>
                                        <?php 
                                        $dateTime = new DateTime($registration["schedule"]); 
                                        $deadline = $registration['deadline']; // Assuming this is the number of days left
                                        if ($deadline < 0) {
                                            $deadlineText = "Expired";
                                            $deadlineClass = "expired"; // Optional class for expired items
                                        } elseif ($deadline <= 3) {
                                            $deadlineText = $deadline . " day(s) left";
                                            $deadlineClass = "near-deadline"; // Class for deadlines within 3 days
                                        } else {
                                            $deadlineText = $deadline . " day(s) left";
                                            $deadlineClass = "far-deadline"; // No special class
                                        }
                                        ?>
                                        <?= htmlspecialchars($dateTime->format('m/d/Y, h:i A')) ?>
                                        <br><br>
                                        <span class="<?= htmlspecialchars($deadlineClass) ?>">
                                            <?= htmlspecialchars($deadlineText) ?>
                                        </span>
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
                                            <?php if (!empty($registration['registration_form_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['registration_form_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['payment_invoice_path'])) { ?>
                                                    <span class="notification" ></span>
                                                <?php } ?>
<?php if ($registration['registration_form_status'] === 'pending' ): ?>
	
    <br><br>
<button type="button" class="btn btn-sm btn-danger regFormUpdateButton" 
data-toggle="modal" 
data-target="#regFormUpdateModal"
data-regform-id="<?= htmlspecialchars($registration['form_id']) ?>"
data-regform-status="<?= htmlspecialchars($registration['registration_form_status']) ?>"
data-regform-reason="<?= htmlspecialchars($registration['registration_form_reason']) ?>">
    Verify
</button>
<!-- Modal -->
<div class="modal fade" id="regFormUpdateModal" tabindex="-1" aria-labelledby="regFormUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="verify_registrationform.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="regFormUpdateModalLabel">Modal Table</h5>
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
                                        <input type="radio" name="regformStatusOption" id="regformaccept" value="accept">
                                        <label for="regformaccept">Accept</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="regformStatusOption" id="regformreject" value="reject">
                                        <label for="regformreject">Reject</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="regformStatusOption" id="regformpending" value="pending">
                                        <label for="regformpending">Pending</label>
                                    </div>
                                </td>
                            </tr>
                            <tr id="regformReasonRow" style="display: none;">
                                <td>Reason:</td>
                                <td>
                                    <textarea class="form-control" id="ModalRegFormReasonInput" name="registration_form_reason" placeholder="Enter your reason" rows="4"></textarea>
                                </td>
                            </tr>
                            <!-- Hidden fields to store data to be sent to upload php files -->
                            <input type="hidden" name="form_id" id="modalRegFormID">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="regformConfirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
    
<?php elseif ($registration['registration_form_status'] === 'reject' ): ?>   

<br><br>Please wait for Student to resubmit this Registration Form
<?php else: ?>
<?php endif; ?>      






                                            <?php else: ?>
                                                Please wait for student to upload what
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <!-- Payment Invoice Column mysword -->
                                    <td>
                                        <?php if (
                                            ($registration['registration_status'] === 'form_submitted' ||
                                            $registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted' ||
                                            $registration['registration_status'] === 'invoice_submitted'
                                        )&& $registration['registration_form_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['payment_invoice_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['payment_invoice_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
    
<?php if ($registration['payment_invoice_status'] === 'pending' ): ?>
	
    <br><br>Please wait for Student to verify this Payment Invoice
    
<?php elseif ($registration['payment_invoice_status'] === 'reject' ): ?>   
  
    
  <!-- Reupload Payment Invoice -->
<br><br><button type="button" class="btn btn-sm btn-danger invoiceReuploadButton" 
data-toggle="modal" 
data-target="#reuploadInvoiceModal"
data-reinvoice-id="<?= htmlspecialchars($registration['invoice_id']) ?>"
data-reinvoice-reason="<?= htmlspecialchars($registration['payment_invoice_reason']) ?>"
data-reinvoice-filepath="<?= htmlspecialchars($registration['payment_invoice_path'] ?? '') ?>">
	Reupload
</button>
											


<!---------------------------- MODAL table for Payment Invoice ---------------------------------->
<div class="modal fade" id="reuploadInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="reuploadInvoiceModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reuploadInvoiceModalLabel">Reupload Payment Invoice</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Form for file upload -->
				<form method="POST" enctype="multipart/form-data" action="upload_invoice.php" class="mt-2">
                <div class="form-row align-items-center mb-3">
                    <div class="col-md-4">
                        <label for="invoiceReasonModal" class="col-form-label">Reason of Rejection:</label>
                    </div>
                    <div class="col-md-8">
                        <textarea name="displayreason" id="invoiceReasonModal" class="form-control reasondisplay" rows="4" readonly></textarea>
                    </div>
                </div>

					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="oldInvoiceFilePathModal" class="col-form-label">Uploaded File:</label>
						</div>
						<div class="col-md-8">
							<input type="text" name="displayfilepath" id="oldInvoiceFilePathModal" class="form-control filePathDisplay" readonly>
						</div>
					</div>
					<!-- <div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label class="col-form-label">Select File:</label>
						</div>
						<div class="col-md-8">
							<input type="file" name="payment_invoice" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
						</div>
					</div> -->


    <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Payment Invoice:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="payment_invoice">Drag and drop file here or click to upload</div>
            <input type="file" name="payment_invoice" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="payment_invoice-input">
            <div class="file-preview" id="payment_invoice-preview"></div>
        </div>
    </div>
					<!---------- Hidden fields ------->
					<input type="hidden" name="invoice_id" id="modalReuploadInvoiceId">
					<!-------------------------------->
					<div class="text-right">
						<input type="submit" value="Upload" class="btn btn-primary">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>  
 
<?php else: ?>
<?php endif; ?>
                                          
                                            
                                            
                                            
                                            <?php else: ?>
                                                Please upload Payment Invoice<br>


                                                <!-- Upload Payment Invoice -->
                                                <br><button type="button" class="btn btn-sm btn-info invoiceUploadButton" 
                                            data-toggle="modal" 
                                            data-target="#uploadInvoiceModal"
                                            data-reginvoice-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                            data-invoice-id="<?= htmlspecialchars($registration['invoice_id']) ?>"
                                            data-invoice-filepath="<?= htmlspecialchars($registration['payment_invoice_path'] ?? '') ?>">
                                                Upload
                                            </button>
                                                                                        


                                            <!---------------------------- MODAL table for Payment Invoice ---------------------------------->
                                            <div class="modal fade" id="uploadInvoiceModal" tabindex="-1" role="dialog" aria-labelledby="uploadInvoiceModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="uploadInvoiceModalLabel">Upload Payment Invoice</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Form for file upload -->
                                                            <form method="POST" enctype="multipart/form-data" action="upload_invoice.php" class="mt-2">
                                                                <!-- <div class="form-row align-items-center mb-3">
                                                                    <div class="col-md-4">
                                                                        <label class="col-form-label">Select File:</label>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="file" name="payment_invoice" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
                                                                    </div>
                                                                </div> -->

    <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Payment Invoice:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="payment_invoice">Drag and drop file here or click to upload</div>
            <input type="file" name="payment_invoice" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="payment_invoice-input">
            <div class="file-preview" id="payment_invoice-preview"></div>
        </div>
    </div>
                                                                <!---------- Hidden fields ------->
                                                                <input type="hidden" name="registration_id" id="modalRegInvoiceId">
                                                                <input type="hidden" name="invoice_id" id="modalInvoiceId">
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

                                    <!-- Transaction Slip Column -->
                                    <td>
                                        <?php if (
                                            ($registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted' ||
                                            $registration['registration_status'] === 'invoice_submitted'
                                        )&& $registration['payment_invoice_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['transaction_slip_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['transaction_slip_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['payment_receipt_path'])) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>


	
<?php if ($registration['transaction_slip_status'] === 'pending' ): ?>
	

    <br><br>
<button type="button" class="btn btn-sm btn-danger transactionUpdateButton" 
data-toggle="modal" 
data-target="#transactionUpdateModal"
data-transaction-id="<?= htmlspecialchars($registration['transaction_id']) ?>"
data-transaction-status="<?= htmlspecialchars($registration['transaction_slip_status']) ?>"
data-transaction-reason="<?= htmlspecialchars($registration['transaction_slip_reason']) ?>">
    Verify
</button>
<!-- Modal -->
<div class="modal fade" id="transactionUpdateModal" tabindex="-1" aria-labelledby="transactionUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="verify_transactionslip.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="transactionUpdateModalLabel">Modal Table</h5>
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
                                        <input type="radio" name="transactionStatusOption" id="transactionaccept" value="accept">
                                        <label for="transactionaccept">Accept</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="transactionStatusOption" id="transactionreject" value="reject">
                                        <label for="transactionreject">Reject</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="transactionStatusOption" id="transactionpending" value="pending">
                                        <label for="transactionpending">Pending</label>
                                    </div>
                                </td>
                            </tr>
                            <tr id="transactionReasonRow" style="display: none;">
                                <td>Reason:</td>
                                <td>
                                    <textarea class="form-control" id="ModalTransactionReasonInput" name="transaction_slip_reason" placeholder="Enter your reason" rows="4"></textarea>
                                </td>
                            </tr>
                            <!-- Hidden fields to store data to be sent to upload php files -->
                            <input type="hidden" name="transaction_id" id="modalTransactionID">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="transactionConfirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

    
<?php elseif ($registration['transaction_slip_status'] === 'reject' ): ?>   

<br><br>Please wait for Student to reupload this Transaction Slip
<?php else: ?>
<?php endif; ?>










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
                                            ($registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['transaction_slip_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['payment_receipt_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['payment_receipt_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
            <?php if ($registration['payment_receipt_status'] === 'pending' ): ?>
	
    <br><br>Please wait for Student to verify this Payment Receipt
    
<?php elseif ($registration['payment_receipt_status'] === 'reject' ): ?>   
    
    <br><!-- Reupload Payment Receipt -->
<br><button type="button" class="btn btn-sm btn-danger receiptReuploadButton" 
data-toggle="modal" 
data-target="#reuploadReceiptModal"
data-rereceipt-id="<?= htmlspecialchars($registration['receipt_id']) ?>"
data-rereceipt-reason="<?= htmlspecialchars($registration['payment_receipt_reason']) ?>"
data-rereceipt-filepath="<?= htmlspecialchars($registration['payment_receipt_path'] ?? '') ?>">
	Reupload
</button>
											


<!---------------------------- MODAL table for Payment Receipt ---------------------------------->
<div class="modal fade" id="reuploadReceiptModal" tabindex="-1" role="dialog" aria-labelledby="reuploadReceiptModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reuploadReceiptModalLabel">Reupload Payment Receipt</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Form for file upload -->
				<form method="POST" enctype="multipart/form-data" action="upload_receipt.php" class="mt-2">
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="receiptReasonModal" class="col-form-label">Reason of Rejection:</label>
						</div>
						<div class="col-md-8">
							<textarea name="displayreason" id="receiptReasonModal" class="form-control reasondisplay" rows="4" readonly></textarea>
						</div>
					</div>
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="oldReceiptFilePathModal" class="col-form-label">Uploaded File:</label>
						</div>
						<div class="col-md-8">
							<input type="text" name="displayfilepath" id="oldReceiptFilePathModal" class="form-control filePathDisplay" readonly>
						</div>
					</div>
					<!-- <div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label class="col-form-label">Select File:</label>
						</div>
						<div class="col-md-8">
							<input type="file" name="payment_receipt" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
						</div>
					</div> -->
                    <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Payment Receipt:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="payment_receipt">Drag and drop file here or click to upload</div>
            <input type="file" name="payment_receipt" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="payment_receipt-input">
            <div class="file-preview" id="payment_receipt-preview"></div>
        </div>
    </div>
					<!---------- Hidden fields ------->
					<input type="hidden" name="receipt_id" id="modalReuploadReceiptId">
					<!-------------------------------->
					<div class="text-right">
						<input type="submit" value="Upload" class="btn btn-primary">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php else: ?>
<?php endif; ?>
                                            
                                                <?php else: ?>
                                                Please upload Payment Receipt.<br>


                                            <!-- Upload Payment Receipt -->
                                            <br><button type="button" class="btn btn-sm btn-info receiptUploadButton" 
                                            data-toggle="modal" 
                                            data-target="#uploadReceiptModal"
                                            data-regreceipt-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                            data-receipt-id="<?= htmlspecialchars($registration['receipt_id']) ?>"
                                            data-receipt-filepath="<?= htmlspecialchars($registration['payment_receipt_path'] ?? '') ?>">
                                                Upload
                                            </button>
                                                                                        


                                            <!---------------------------- MODAL table for Payment Receipt ---------------------------------->
                                            <div class="modal fade" id="uploadReceiptModal" tabindex="-1" role="dialog" aria-labelledby="uploadReceiptModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="uploadReceiptModalLabel">Upload Payment Receipt</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Form for file upload -->
                                                            <form method="POST" enctype="multipart/form-data" action="upload_receipt.php" class="mt-2">
                                                                <!-- <div class="form-row align-items-center mb-3">
                                                                    <div class="col-md-4">
                                                                        <label class="col-form-label">Select File:</label>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="file" name="payment_receipt" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
                                                                    </div>
                                                                </div> -->
                                                                <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Payment Receipt:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="payment_receipt">Drag and drop file here or click to upload</div>
            <input type="file" name="payment_receipt" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="payment_receipt-input">
            <div class="file-preview" id="payment_receipt-preview"></div>
        </div>
    </div>
                                                                <!---------- Hidden fields ------->
                                                                <input type="hidden" name="registration_id" id="modalRegReceiptId">
                                                                <input type="hidden" name="receipt_id" id="modalReceiptId">
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

                                    <!-- Exam Confirmation Letter Column -->
                                    <td>
                                        <?php if (
                                            ($registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['transaction_slip_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['exam_confirmation_letter_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['exam_confirmation_letter_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>

<?php if ($registration['exam_confirmation_letter_status'] === 'pending' ): ?>

<br><br>Please wait for Student to verify this Exam Confirmation Letter
    
<?php elseif ($registration['exam_confirmation_letter_status'] === 'reject' ): ?>   
    
    <br><!-- Reupload Exam Confirmation Letter -->
<br><button type="button" class="btn btn-sm btn-danger examletterReuploadButton" 
data-toggle="modal" 
data-target="#reuploadExamLetterModal"
data-reexamletter-id="<?= htmlspecialchars($registration['confirmation_id']) ?>"
data-reexamletter-reason="<?= htmlspecialchars($registration['exam_confirmation_letter_reason']) ?>"
data-reexamletter-filepath="<?= htmlspecialchars($registration['exam_confirmation_letter_path'] ?? '') ?>">
	Reupload
</button>
											


<!---------------------------- MODAL table for Exam Confirmation Letter ---------------------------------->
<div class="modal fade" id="reuploadExamLetterModal" tabindex="-1" role="dialog" aria-labelledby="reuploadExamLetterModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reuploadExamLetterModalLabel">Reupload Exam Confirmation Letter</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Form for file upload -->
				<form method="POST" enctype="multipart/form-data" action="upload_examconfirmationletter.php" class="mt-2">
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="examletterReasonModal" class="col-form-label">Reason of Rejection:</label>
						</div>
						<div class="col-md-8">
							<textarea name="displayreason" id="examletterReasonModal" class="form-control reasondisplay" rows="4" readonly></textarea>
						</div>
					</div>
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="oldExamLetterFilePathModal" class="col-form-label">Uploaded File:</label>
						</div>
						<div class="col-md-8">
							<input type="text" name="displayfilepath" id="oldExamLetterFilePathModal" class="form-control filePathDisplay" readonly>
						</div>
					</div>
					<!-- <div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label class="col-form-label">Select File:</label>
						</div>
						<div class="col-md-8">
							<input type="file" name="exam_confirmation_letter" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
						</div>
					</div> -->
                    <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Exam Confirmation Letter:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="exam_confirmation_letter">Drag and drop file here or click to upload</div>
            <input type="file" name="exam_confirmation_letter" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="exam_confirmation_letter-input">
            <div class="file-preview" id="exam_confirmation_letter-preview"></div>
        </div>
    </div>
					<!---------- Hidden fields ------->
					<input type="hidden" name="confirmation_id" id="modalReuploadExamLetterId">
					<!-------------------------------->
					<div class="text-right">
						<input type="submit" value="Upload" class="btn btn-primary">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php else: ?>
<?php endif; ?>
                                            
                                            
                                            
                                            <?php else: ?>
                                                Please upload Exam Confirmation Letter.<br>
                                            <!-- Upload Exam Confirmation Letter -->
                                            <br><button type="button" class="btn btn-sm btn-info examletterUploadButton" 
                                            data-toggle="modal" 
                                            data-target="#uploadExamLetterModal" 
                                            data-regexamletter-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                            data-examletter-id="<?= htmlspecialchars($registration['confirmation_id']) ?>"
                                            data-examletter-filepath="<?= htmlspecialchars($registration['exam_confirmation_letter_path'] ?? '') ?>">
                                                Upload
                                            </button>
                                                                                        


                                            <!---------------------------- MODAL table for Exam Confirmation Letter ---------------------------------->
                                            <div class="modal fade" id="uploadExamLetterModal" tabindex="-1" role="dialog" aria-labelledby="uploadExamLetterModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="uploadExamLetterModalLabel">Upload Exam Confirmation Letter</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Form for file upload -->
                                                            <form method="POST" enctype="multipart/form-data" action="upload_examconfirmationletter.php" class="mt-2">
                                                                
                                                                <!-- <div class="form-row align-items-center mb-3">
                                                                    <div class="col-md-4">
                                                                        <label class="col-form-label">Select File:</label>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="file" name="exam_confirmation_letter" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
                                                                    </div>
                                                                </div> -->
                                                                <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Exam Confirmation Letter:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="exam_confirmation_letter">Drag and drop file here or click to upload</div>
            <input type="file" name="exam_confirmation_letter" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="exam_confirmation_letter-input">
            <div class="file-preview" id="exam_confirmation_letter-preview"></div>
        </div>
    </div>
                                                                <!---------- Hidden fields ------->
                                                                <input type="hidden" name="registration_id" id="modalRegExamLetterId">
                                                                <input type="hidden" name="confirmation_id" id="modalExamLetterId">
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

                                    <!-- Exam Result Column -->
                                    <!---------------------------- VIEW the result ---------------------------------->
                                    <td>
                                        <?php if (
                                            ($registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['payment_receipt_status'] === 'accept' && $registration['exam_confirmation_letter_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['exam_result'])): ?>
                                                <?= htmlspecialchars($registration['exam_result']) ?><br><br>
                                                <a href="#" class="btn btn-sm btn-info editButton"
                                                    data-toggle="modal"
                                                    data-target="#resultModal"
                                                    data-registration-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                                    data-examresult-id="<?= htmlspecialchars($registration['examresult_id']) ?>"
                                                    data-exam-result="<?= htmlspecialchars($registration['exam_result'] ?? '') ?>"
                                                    data-publish="<?= htmlspecialchars($registration['publish'] ?? 'not_published') ?>"
                                                    data-exam-status="<?= htmlspecialchars($registration['exam_status'] ?? 'pending') ?>">
                                                    Edit
                                                </a>

                                            <?php else: ?>
                                                Please key in Exam Result.<br><br>
                                                <a href="#" class="btn btn-sm btn-danger"
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
                                                            <label for="examResultModal">Exam Result</label>
                                                            <input type="text" name="examResult" id="examResultModal" class="form-control" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Status:</label><br>
                                                            <div>
                                                                <input type="radio" name="examstatus" id="passYes" value="pass">
                                                                <label for="passYes">Pass</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" name="examstatus" id="passNo" value="fail" checked>
                                                                <label for="passNo">Failed</label>
                                                            </div>
                                                            <div>
                                                                <input type="radio" name="examstatus" id="passWait" value="pending" checked>
                                                                <label for="passWait">Pending</label>
                                                            </div>
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
                                            ($registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['payment_receipt_status'] === 'accept' && $registration['exam_confirmation_letter_status'] === 'accept'
                                        ): ?>

<?php if ($registration['exam_status'] === 'pass' ): ?>
	
    <?php if (!empty($registration['certificate_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['certificate_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>

                    <?php if ($registration['certificate_status'] === 'pending' ): ?>
	
			<br><br>Please wait for Student to verify this Certificate
			
		<?php elseif ($registration['certificate_status'] === 'reject' ): ?>   
			
<!-- Reupload Certificate -->
<br><br><button type="button" class="btn btn-sm btn-danger certificateReuploadButton" 
data-toggle="modal" 
data-target="#reuploadCertificateModal"
data-certifcate-id="<?= htmlspecialchars($registration['certificate_id']) ?>"
data-certificate-reason="<?= htmlspecialchars($registration['certificate_reason']) ?>"
data-certificate-filepath="<?= htmlspecialchars($registration['certificate_path'] ?? '') ?>">
	Reupload
</button>
											


<!---------------------------- MODAL table for Certificate ---------------------------------->
<div class="modal fade" id="reuploadCertificateModal" tabindex="-1" role="dialog" aria-labelledby="reuploadCertificateModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reuploadCertificateModalLabel">Reupload Certificate</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Form for file upload -->
				<form method="POST" enctype="multipart/form-data" action="upload_certificate.php" class="mt-2">
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="certificateReasonModal" class="col-form-label">Reason of Rejection:</label>
						</div>
						<div class="col-md-8">
							<textarea name="displayreason" id="certificateReasonModal" class="form-control reasondisplay" rows="4" readonly></textarea>
						</div>
					</div>
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="oldCertificateFilePathModal" class="col-form-label">Uploaded File:</label>
						</div>
						<div class="col-md-8">
							<input type="text" name="displayfilepath" id="oldCertificateFilePathModal" class="form-control filePathDisplay" readonly>
						</div>
					</div>
					<!-- <div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label class="col-form-label">Select File:</label>
						</div>
						<div class="col-md-8">
							<input type="file" name="certificate" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
						</div>
					</div> -->


                    <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Certificate:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="certificate">Drag and drop file here or click to upload</div>
            <input type="file" name="certificate" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="certificate-input">
            <div class="file-preview" id="certificate-preview"></div>
        </div>
    </div>
					<!---------- Hidden fields ------->
					<input type="hidden" name="certificate_id" id="modalReuploadCertificateId">
					<!-------------------------------->
					<div class="text-right">
						<input type="submit" value="Upload" class="btn btn-primary">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


		<?php else: ?>
		<?php endif; ?>
                                            
                                            
                                            <?php else: ?>
                                                Please upload Certificate.<br>

                                            
                                            <!-- Upload Certificate -->
                                            <br><button type="button" class="btn btn-sm btn-info certificateUploadButton" 
                                            data-toggle="modal" 
                                            data-target="#uploadCertificateModal"
                                            data-regcertificate-id="<?= htmlspecialchars($registration['registration_id']) ?>"
                                            data-certificate-id="<?= htmlspecialchars($registration['certificate_id']) ?>"
                                            data-certificate-filepath="<?= htmlspecialchars($registration['certificate_path'] ?? '') ?>">
                                                Upload
                                            </button>
                                                                                        

                                            <!---------------------------- MODAL table for Certificate ---------------------------------->
                                            <div class="modal fade" id="uploadCertificateModal" tabindex="-1" role="dialog" aria-labelledby="uploadCertificateModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="uploadCertificateModalLabel">Upload Certificate</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <!-- Form for file upload -->
                                                            <form method="POST" enctype="multipart/form-data" action="upload_certificate.php" class="mt-2">
                                                                
                                                                <!-- <div class="form-row align-items-center mb-3">
                                                                    <div class="col-md-4">
                                                                        <label class="col-form-label">Select File:</label>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="file" name="certificate" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
                                                                    </div>
                                                                </div> -->
                                                                
    <div class="dragupload form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Certificate:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" data-target="certificate">Drag and drop file here or click to upload</div>
            <input type="file" name="certificate" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="certificate-input">
            <div class="file-preview" id="certificate-preview"></div>
        </div>
    </div>
                                                              
                                                                <!---------- Hidden fields ------->
                                                                <input type="hidden" name="registration_id" id="modalRegCertificateId">
                                                                <input type="hidden" name="certificate_id" id="modalCertificateId">
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
    
<?php elseif ($registration['exam_status'] === 'fail' ): ?>   

Certificate is not uploadable due to failed exam
<?php else: ?>
Certificate is not available yet
<?php endif; ?>


                                            

                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

<td>
    <br>
<button type="button" class="btn btn-sm btn-primary regresultstatusUpdateButton" 
data-toggle="modal" 
data-target="#regresultstatusUpdateModal"
data-regresultstatus-id="<?= htmlspecialchars($registration['registration_id']) ?>"
data-regresultstatus-status="<?= htmlspecialchars($registration['result_status']) ?>">
    Action
</button>
<!-- Modal -->
<div class="modal fade" id="regresultstatusUpdateModal" tabindex="-1" aria-labelledby="regresultstatusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="update_registration_resultstatus.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="regresultstatusUpdateModalLabel">Change Registration Status</h5>
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
                                        <input type="radio" name="regresultStatusOption" id="regcomplete" value="completed">
                                        <label for="regcomplete">Completed</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="regresultStatusOption" id="regincomplete" value="incomplete">
                                        <label for="regincomplete">Incomplete</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="regresultStatusOption" id="regpending" value="pending">
                                        <label for="regpending">Pending</label>
                                    </div>
                                </td>
                            </tr>
                            <!-- Hidden fields to store data to be sent to upload php files -->
                            <input type="hidden" name="registration_id" id="modalRegResultStatusID">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="regResultConfirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
    




</td>



                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13">No registrations found.</td>
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
        /////////////////////// Javascript //////////////////////////////// mysword
        // Display accurate information on modal table (Edit) 
        document.addEventListener("DOMContentLoaded", function () {
            const editButtons = document.querySelectorAll(".editButton");
            const certificateUploadButtons = document.querySelectorAll(".certificateUploadButton");
            const examletterUploadButtons = document.querySelectorAll(".examletterUploadButton");
            const receiptUploadButtons = document.querySelectorAll(".receiptUploadButton");
            const invoiceUploadButtons = document.querySelectorAll(".invoiceUploadButton");
            const invoiceReuploadButtons = document.querySelectorAll(".invoiceReuploadButton");
            const receiptReuploadButtons = document.querySelectorAll(".receiptReuploadButton");
            const examletterReuploadButtons = document.querySelectorAll(".examletterReuploadButton");
            const certificateReuploadButtons = document.querySelectorAll(".certificateReuploadButton");
            const transactionUpdateButtons = document.querySelectorAll(".transactionUpdateButton");
            const regFormUpdateButtons = document.querySelectorAll(".regFormUpdateButton");
            const regresultstatusUpdateButtons = document.querySelectorAll(".regresultstatusUpdateButton");

            //////////////////////////////////////////// Upload ///////////////////////////////////////
            // Exam Result
            editButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const examResult = this.getAttribute("data-exam-result") || "";
                    const registrationId = this.getAttribute("data-registration-id") || "";
                    const examResultId = this.getAttribute("data-examresult-id") || "";
                    const publish = this.getAttribute("data-publish") || "not_published";  // Default to 'not_published' exam_status
                    const examStatus = this.getAttribute("data-exam-status") || "pending";


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
                    
                    if (examStatus === "pending") {
                        document.getElementById("passWait").checked = true;
                    } else if (examStatus === "fail") {
                        document.getElementById("passNo").checked = true;
                    }else {
                        document.getElementById("passYes").checked = true;
                    }

                });
            });

            // Certificate
            certificateUploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const certificateFilePath = this.getAttribute("data-certificate-filepath") || "";
                    const certificateId = this.getAttribute("data-certificate-id") || "";
                    const regcertificateId = this.getAttribute("data-regcertificate-id") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = certificateFilePath.substring(certificateFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("CertFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalRegCertificateId").value = regcertificateId;
                    document.getElementById("modalCertificateId").value = certificateId;
                });
            });


            // Exam Confirmation Letter
            examletterUploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const examletterFilePath = this.getAttribute("data-examletter-filepath") || "";
                    const examletterId = this.getAttribute("data-examletter-id") || "";
                    const regexamletterId = this.getAttribute("data-regexamletter-id") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = examletterFilePath.substring(examletterFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("ExamLetterFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalRegExamLetterId").value = regexamletterId;
                    document.getElementById("modalExamLetterId").value = examletterId;
                });
            });

            // Payment Receipt
            receiptUploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const receiptFilePath = this.getAttribute("data-receipt-filepath") || "";
                    const receiptId = this.getAttribute("data-receipt-id") || "";
                    const regreceiptId = this.getAttribute("data-regreceipt-id") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = receiptFilePath.substring(receiptFilePath.lastIndexOf('/') + 1).substring(24);



                    // Set modal field values (Display)
                    document.getElementById("ReceiptFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalRegReceiptId").value = regreceiptId;
                    document.getElementById("modalReceiptId").value = receiptId;
                });
            });

            // Payment Invoice
            invoiceUploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const invoiceFilePath = this.getAttribute("data-invoice-filepath") || "";
                    const invoiceId = this.getAttribute("data-invoice-id") || "";
                    const reginvoiceId = this.getAttribute("data-reginvoice-id") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = invoiceFilePath.substring(invoiceFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("InvoiceFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalRegInvoiceId").value = reginvoiceId;
                    document.getElementById("modalInvoiceId").value = invoiceId;
                });
            });


            //////////////////////////////////////////// Reupload ///////////////////////////////////////

            // Payment Invoice
            invoiceReuploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const invoiceFilePath = this.getAttribute("data-reinvoice-filepath") || "";
                    const invoiceId = this.getAttribute("data-reinvoice-id") || "";
                    const invoiceReason = this.getAttribute("data-reinvoice-reason") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = invoiceFilePath.substring(invoiceFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("oldInvoiceFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalReuploadInvoiceId").value = invoiceId;
                    document.getElementById("invoiceReasonModal").value = invoiceReason;
                });
            });

            // Payment Receipt
            receiptReuploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const receiptFilePath = this.getAttribute("data-rereceipt-filepath") || "";
                    const receiptId = this.getAttribute("data-rereceipt-id") || "";
                    const receiptReason = this.getAttribute("data-rereceipt-reason") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = receiptFilePath.substring(receiptFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("oldReceiptFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalReuploadReceiptId").value = receiptId;
                    document.getElementById("receiptReasonModal").value = receiptReason;
                });
            });

            // Exam Confirmation Letter
            examletterReuploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const examletterFilePath = this.getAttribute("data-reexamletter-filepath") || "";
                    const examletterId = this.getAttribute("data-reexamletter-id") || "";
                    const examletterReason = this.getAttribute("data-reexamletter-reason") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = examletterFilePath.substring(examletterFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("oldExamLetterFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalReuploadExamLetterId").value = examletterId;
                    document.getElementById("examletterReasonModal").value = examletterReason;
                });
            });

            // Certificate
            certificateReuploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const certificateFilePath = this.getAttribute("data-certificate-filepath") || "";
                    const certificateId = this.getAttribute("data-certifcate-id") || "";
                    const certificateReason = this.getAttribute("data-certificate-reason") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = certificateFilePath.substring(certificateFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("oldCertificateFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalReuploadCertificateId").value = certificateId;
                    document.getElementById("certificateReasonModal").value = certificateReason;
                });
            });

            // Update Transaction Slip
            transactionUpdateButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const transactionUpdateID = this.getAttribute("data-transaction-id") || "";
                    const transactionStatus = this.getAttribute("data-transaction-status") || "";
                    const transactionReason = this.getAttribute("data-transaction-reason") || "";

                    document.getElementById("modalTransactionID").value = transactionUpdateID;
                    document.getElementById("modaltransactionStatus").value = transactionStatus;
                    document.getElementById("ModalTransactionReasonInput").value = transactionReason;
                });
            });

            // Update Registration Form
            regFormUpdateButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const regformUpdateID = this.getAttribute("data-regform-id") || "";
                    const regformStatus = this.getAttribute("data-regform-status") || "";
                    const regformReason = this.getAttribute("data-regform-reason") || "";

                    document.getElementById("modalRegFormID").value = regformUpdateID;
                    document.getElementById("modalregformStatus").value = regformStatus;
                    document.getElementById("ModalRegFormReasonInput").value = regformReason;
                });
            });


            //////// Update Regstration Result Status
            // Update Registration Result
            regresultstatusUpdateButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const regResultUpdateID = this.getAttribute("data-regresultstatus-id") || "";
                    const regResultStatus = this.getAttribute("data-regresultstatus-status") || "";

                    document.getElementById("modalRegResultStatusID").value = regResultUpdateID;
                    document.getElementById("modalregResultStatus").value = regResultStatus;
                });
            });


        // drag and drop
        document.querySelectorAll('.drop-zone').forEach(zone => {
            const inputId = `${zone.dataset.target}-input`;
            const fileInput = document.getElementById(inputId);
            const preview = document.getElementById(`${zone.dataset.target}-preview`);

            zone.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', (event) => {
                handleFileUpload(event.target.files, preview);
            });

            zone.addEventListener('dragover', (event) => {
                event.preventDefault();
                zone.classList.add('dragover');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('dragover');
            });

            zone.addEventListener('drop', (event) => {
                event.preventDefault();
                zone.classList.remove('dragover');
                fileInput.files = event.dataTransfer.files; // Assign dropped files to input
                handleFileUpload(event.dataTransfer.files, preview);
            });
        });

        function handleFileUpload(files, preview) {
            preview.innerHTML = ''; // Clear existing previews
            Array.from(files).forEach(file => {
                const fileReader = new FileReader();
                fileReader.onload = () => {
                    if (file.type.startsWith('image/')) {
                        const img = document.createElement('img');
                        img.src = fileReader.result;
                        preview.appendChild(img);
                    } else {
                        const fileName = document.createElement('div');
                        fileName.textContent = file.name;
                        fileName.classList.add('file-name');
                        preview.appendChild(fileName);
                    }
                };
                fileReader.readAsDataURL(file);
            });
        }

        



        });

        

        //////////////////////////////////////   JQUERY   ////////////////////////////////////////////////
        // Display accurate information on modal table (Insert) JQuert

        //////////////////////////////////////////// Upload ///////////////////////////////////////
        // Exam Result
        $('#resultModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var registrationId = button.data('registration-id'); // Extract info from data-* attributes
            var examResultId = button.data('examresult-id'); // Extract info from data-* attributes
            var publish = button.data('publish'); // Extract publish status (either 'published' or 'not_published')
            var examStatus = button.data('exam-status'); // Extract publish status (either 'published' or 'not_published')

            var modal = $(this);
            modal.find('#modalRegistrationId').val(registrationId); // Set the value of registration_id in modal
            modal.find('#modalExamResultId').val(examResultId); // Set the value of examresult_id in modal

            // Set the correct publish radio button
            if (publish === 'published') {
                modal.find('#publishYes').prop('checked', true);  // Set "Yes" if published
            } else {
                modal.find('#publishNo').prop('checked', true);  // Set "No" if not published
            }

            
            if (examStatus === 'pass') {
                modal.find('#passYes').prop('checked', true);  // Set "Yes" if published
            } else if (examStatus === 'fail') {
                modal.find('#passNo').prop('checked', true);  // Set "Yes" if published
            }else {
                modal.find('#passWait').prop('checked', true);  // Set "No" if not published
            }
        });

        // Certificate
        $('#uploadCertificateModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var regcertificateId = button.data('regcertificate-id'); // Extract info from data-* attributes
            var certificatetId = button.data('certificate-id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('#modalRegCertificateId').val(regcertificateId); // Set the value of registration_id in modal
            modal.find('#modalCertificateId').val(certificatetId); // Set the value of examresult_id in modal

        });

        // Exam Confirmation Letter
        $('#uploadExamLetterModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var regexamletterId = button.data('regexamletter-id'); // Extract info from data-* attributes
            var examletterId = button.data('examletter-id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('#modalRegExamLetterId').val(regexamletterId); 
            modal.find('#modalExamLetterId').val(examletterId); 

        });


        // Payment Receipt
        $('#uploadReceiptModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var regreceiptId = button.data('regreceipt-id'); // Extract info from data-* attributes
            var receiptId = button.data('receipt-id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('#modalRegReceiptId').val(regreceiptId); 
            modal.find('#modalReceiptId').val(receiptId); 

        });


        // Payment Invoice
        $('#uploadInvoiceModal').on('show.bs.modal', function(event) {
            // jQuery to update the hidden inputs in the modal when the button is clicked
            var button = $(event.relatedTarget); // Button that triggered the modal
            var reginvoiceId = button.data('reginvoice-id'); // Extract info from data-* attributes
            var invoiceId = button.data('invoice-id'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('#modalRegInvoiceId').val(reginvoiceId); 
            modal.find('#modalInvoiceId').val(invoiceId); 

        });


        //////////////////////////////// Reupload Files (Store Reason and ID)  ////////////////////////

        // Payment Invoice
        $('#reuploadInvoiceModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var invoiceId = button.data('reinvoice-id'); 
            var invoiceReason = button.data('reinvoice-reason'); 

            var modal = $(this);
            modal.find('#modalReuploadInvoiceId').val(invoiceId); 
            modal.find('#invoiceReasonModal').val(invoiceReason); 

        });


        // Payment Receipt
        $('#reuploadReceiptModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var receiptId = button.data('rereceipt-id'); 
            var receiptReason = button.data('rereceipt-reason'); 

            var modal = $(this);
            modal.find('#modalReuploadReceiptId').val(receiptId); 
            modal.find('#receiptReasonModal').val(receiptReason); 

        });

        // Exam Confirmation Letter
        $('#reuploadExamLetterModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var examletterId = button.data('reexamletter-id'); 
            var examletterReason = button.data('reexamletter-reason'); 

            var modal = $(this);
            modal.find('#modalReuploadExamLetterId').val(examletterId); 
            modal.find('#examletterReasonModal').val(examletterReason); 

        });


        // Certificate
        $('#reuploadCertificateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var certificateId = button.data('certifcate-id'); 
            var certificateReason = button.data('certificate-reason'); 

            var modal = $(this);
            modal.find('#modalReuploadCertificateId').val(certificateId); 
            modal.find('#certificateReasonModal').val(certificateReason); 

        });

        // Transaction Slip
        $(document).ready(function() {
            // Use event delegation for dynamically added elements
            $(document).on('show.bs.modal', '#transactionUpdateModal', function(event) {
                var button = $(event.relatedTarget); 
                var transactionId = button.data('transaction-id'); 
                var transactionStatus = button.data('transaction-status'); 
                var transactionReason = button.data('transaction-reason'); 

                var modal = $(this);

                modal.find('#modalTransactionID').val(transactionId); 
                modal.find('#ModalTransactionReasonInput').val(transactionReason); 

                // Set radio button
                modal.find('input[type=radio][name=transactionStatusOption]').each(function() {
                    if ($(this).val() === transactionStatus) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
            });

        });


        // Registration Form
        $(document).ready(function() {
            $('.regFormUpdateButton').on('click', function() {
                var button = $(this); 
                var regformId = button.data('regform-id');
                var regformStatus = button.data('regform-status');
                var regformReason = button.data('regform-reason');
                
                var modal = $('#regFormUpdateModal');
                modal.find('#modalRegFormID').val(regformId);
                modal.find('#ModalRegFormReasonInput').val(regformReason);

                modal.find('input[type=radio][name=regformStatusOption]').each(function() {
                    if ($(this).val() === regformStatus) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
            });
        });

        // Registration Result Status
        $(document).ready(function() {
            // Use event delegation for dynamically added elements
            $(document).on('show.bs.modal', '#regresultstatusUpdateModal', function(event) {
                var button = $(event.relatedTarget); 
                var regResultId = button.data('regresultstatus-id'); 
                var regResultStatus = button.data('regresultstatus-status'); 

                var modal = $(this);
                
                modal.find('#modalRegResultStatusID').val(regResultId); 

                // Set radio button
                modal.find('input[type=radio][name=regresultStatusOption]').each(function() {
                    if ($(this).val() === regResultStatus) {
                        $(this).prop('checked', true);
                    } else {
                        $(this).prop('checked', false);
                    }
                });
            });
        });



        

        ////////////////////////////////// Reupload Files (Show Reason Input When Reject) /////////////////////////////////////////
        // JavaScript to toggle reason text box
        $(document).ready(function () {
            /////// Transaction Slip
            $('input[name="transactionStatusOption"]').change(function () {
                if ($('#transactionreject').is(':checked')) {
                    $('#transactionReasonRow').show();
                } else {
                    $('#transactionReasonRow').hide();
                }
            });

            $('#transactionConfirmButton').click(function (event) { 
                const selectedOption = $('input[name="transactionStatusOption"]:checked').val();
                const reason = $('#ModalTransactionReasonInput').val();

                // If the status is "transactionreject" and the reason is empty, alert the user and stop form submission
                if (selectedOption === 'reject' && !reason) {
                    alert('Please provide a reason for rejection.');
                    event.preventDefault(); 
                    return;
                }

                // If a reason is provided, show the selection and reason
                alert(`You selected: ${selectedOption}${selectedOption === 'reject' ? ' with reason: ' + reason : ''}`);
                $('#transactionUpdateModal').modal('hide');
            });

            ////// Registration Form
            $('input[name="regformStatusOption"]').change(function () {
                if ($('#regformreject').is(':checked')) {
                    $('#regformReasonRow').show();
                } else {
                    $('#regformReasonRow').hide();
                }
            });

            $('#regformConfirmButton').click(function (event) { 
                const selectedOption = $('input[name="regformStatusOption"]:checked').val();
                const reason = $('#ModalRegFormReasonInput').val();

                // If the status is "regformreject" and the reason is empty, alert the user and stop form submission
                if (selectedOption === 'reject' && !reason) {
                    alert('Please provide a reason for rejection.');
                    event.preventDefault(); 
                    return;
                }

                // If a reason is provided, show the selection and reason
                alert(`You selected: ${selectedOption}${selectedOption === 'reject' ? ' with reason: ' + reason : ''}`);
                $('#regFormUpdateModal').modal('hide');
            });
        });


        /////////////////////////////////// Other function ////////////////////////////////////////////
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

            // Add custom placeholder text and icon
            $('.dataTables_filter input')
                .attr('placeholder', 'Search...')
                .before('<i class="fas fa-search" style="margin-right: 10px; color: #007BFF;"></i>');
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