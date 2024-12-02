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
</script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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
// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['student_full_name'])) {
    header("Location: index.php");
    exit();
}
?>


        <?php

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
            $resultStatus = isset($_GET['result_status']) ? $_GET['result_status'] : null;

            $studentId = $_SESSION['student_id']; // Get the logged-in student ID

            $query = "
        SELECT r.registration_id, r.registration_status, r.result_status, r.created_at, r.updated_at, 
            r.student_id, r.certification_id, 
            c.certification_name, s.full_name, c.schedule,
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
        WHERE r.student_id = :student_id
    ";

            if ($certificationId) {
                $query .= " AND r.certification_id = :certification_id";
            }

            

            


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
                $query .= " AND " . implode(" AND ", $conditions);
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

        // To allow reupload registration form without issue
        if (isset($_SESSION['certification_id'])) {
            unset($_SESSION['certification_id']);
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
                            <th>Certification Name</th>
                            <th>Schedule</th>
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
                                <tr  class="<?= in_array($registration['result_status'], ['incomplete']) ? 'non-interactable' : '' ?>">
                                    <td><?= htmlspecialchars($registration['registration_id']) ?></td>
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
                                    <a href="<?= htmlspecialchars($registration['registration_form_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">View</a>
                                    
                                        <?php if ($registration['registration_form_status'] === 'reject' ): ?>
        
                                            
<!-- Reupload Registration Form -->
<br><br><button type="button" class="btn btn-sm btn-danger regformReuploadButton" 
data-toggle="modal" 
data-target="#reuploadRegFormModal"
data-reregform-id="<?= htmlspecialchars($registration['form_id']) ?>"
data-reregform-reason="<?= htmlspecialchars($registration['registration_form_reason']) ?>"
data-reregform-filepath="<?= htmlspecialchars($registration['registration_form_path'] ?? '') ?>">
	Resubmit
</button>
											


<!---------------------------- MODAL table for Registration Form ---------------------------------->
<div class="modal fade" id="reuploadRegFormModal" tabindex="-1" role="dialog" aria-labelledby="reuploadRegFormModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="reuploadRegFormModalLabel">Reupload Registration Form</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Form for file upload -->
				<form method="POST" enctype="multipart/form-data" action="stu_overview_cert_form.php" class="mt-2">
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label for="regformReasonModal" class="col-form-label">Reason of Rejection:</label>
						</div>
						<div class="col-md-8">
							<textarea name="displayreason" id="regformReasonModal" class="form-control reasondisplay" rows="4" readonly></textarea>
						</div>
					</div>
					<div class="form-row align-items-center mb-3">
						<div class="col-md-4">
							<label class="col-form-label">Refill Form:</label>
						</div>
						<div class="col-md-8">
                            <!---------- Hidden fields ------->
                            <input type="hidden" name="regform_id" id="modalReuploadRegFormId">
                            <!-------------------------------->
                            <button type="submit" class="btn btn-primary" id="confirmButton">Confirm</button>
                        </div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
                                            
                                            <?php elseif ($registration['registration_form_status'] === 'pending' ): ?>   
                                                <br><br>Please wait for Lecturer to verify this Registration Form
                                            <?php else: ?>
                                                    
                                                <?php endif; ?>
                                
                                
                                    <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                </td>


                                    <td>
                                        <?php if (
                                                ($registration['registration_status'] === 'form_submitted' ||
                                                    $registration['registration_status'] === 'transaction_submitted' ||
                                                    $registration['registration_status'] === 'receipt_submitted' ||
                                                    $registration['registration_status'] === 'examletter_submitted' ||
                                                    $registration['registration_status'] === 'result_submitted' ||
                                                    $registration['registration_status'] === 'certificate_submitted' ||
                                                    $registration['registration_status'] === 'invoice_submitted'
                                                ) && $registration['registration_form_status'] === 'accept'
                                            ): ?>
                                            <?php if (!empty($registration['payment_invoice_path'])): ?>

                                                <a href="<?= htmlspecialchars($registration['payment_invoice_path']) ?>" class="btn btn-sm btn-info " onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && (empty($registration['transaction_slip_path']))) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>

<?php if ($registration['payment_invoice_status'] === 'pending' ): ?>
	

    <br><br>
<button type="button" class="btn btn-sm btn-danger invoiceUpdateButton" 
data-toggle="modal" 
data-target="#invoiceUpdateModal"
data-invoice-id="<?= htmlspecialchars($registration['invoice_id']) ?>"
data-invoice-status="<?= htmlspecialchars($registration['payment_invoice_status']) ?>"
data-invoice-reason="<?= htmlspecialchars($registration['payment_invoice_reason']) ?>">
    Verify
</button>
<!-- Modal -->
<div class="modal fade" id="invoiceUpdateModal" tabindex="-1" aria-labelledby="invoiceUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="verify_invoice.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceUpdateModalLabel">Accept or Reject This Payment Invoice</h5>
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
                                        <input type="radio" name="invoiceStatusOption" id="invoiceaccept" value="accept">
                                        <label for="invoiceaccept">Accept</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="invoiceStatusOption" id="invoicereject" value="reject">
                                        <label for="invoicereject">Reject</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="invoiceStatusOption" id="invoicepending" value="pending">
                                        <label for="invoicepending">Pending</label>
                                    </div>
                                </td>
                            </tr>
                            <tr id="reasonRow" style="display: none;">
                                <td>Reason:</td>
                                <td>
                                    <textarea class="form-control" id="ModalinvoiceReasonInput" name="payment_invoice_reason" placeholder="Enter your reason" rows="4"></textarea>
                                </td>
                            </tr>
                            <!-- Hidden fields to store data to be sent to upload php files -->
                            <input type="hidden" name="invoice_id" id="modalInvoiceID">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

    
<?php elseif ($registration['payment_invoice_status'] === 'reject' ): ?>   

<br><br>Please wait for Lecturer to reupload this Payment Invoice
<?php else: ?>
<?php endif; ?>




                                            <?php else: ?>
                                                <?php if ($registration['registration_form_status'] !== 'accept' ): ?>
                                                    N/A
                                                <?php else: ?>
                                                    Please wait for lecturer to upload Payment Invoice.
                                                    <?php endif; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            ($registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted' ||
                                            $registration['registration_status'] === 'invoice_submitted'
                                        ) && $registration['payment_invoice_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['transaction_slip_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['transaction_slip_path']) ?>" class="btn btn-sm btn-info" target="_blank">View</a>
                                                <?php if ($registration['transaction_slip_status'] === 'pending' ): ?>
	
	
                                <br><br>Please wait for Lecturer to verify this Transaction Slip
   
<?php elseif ($registration['transaction_slip_status'] === 'reject' ): ?>   
                                       
<!-- Reupload Transaction Slip -->
<br><br><button type="button" class="btn btn-sm btn-danger transactionReuploadButton" 
data-toggle="modal" 
data-target="#reuploadTransactionModal"
data-retransaction-id="<?= htmlspecialchars($registration['transaction_id']) ?>"
data-retransaction-reason="<?= htmlspecialchars($registration['transaction_slip_reason']) ?>"
data-retransaction-filepath="<?= htmlspecialchars($registration['transaction_slip_path'] ?? '') ?>">
Reupload
</button>
                                       


<!---------------------------- MODAL table for Transaction Slip ---------------------------------->
<div class="modal fade" id="reuploadTransactionModal" tabindex="-1" role="dialog" aria-labelledby="reuploadTransactionModalLabel" aria-hidden="true">
<div class="modal-dialog" role="document">
   <div class="modal-content">
       <div class="modal-header">
           <h5 class="modal-title" id="reuploadTransactionModalLabel">Reupload Transaction Slip</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
               <span aria-hidden="true">&times;</span>
           </button>
       </div>
       <div class="modal-body">
           <!-- Form for file upload -->
           <form method="POST" enctype="multipart/form-data" action="upload_transactionslip.php" class="mt-2">
               <div class="form-row align-items-center mb-3">
                   <div class="col-md-4">
                       <label for="transactionReasonModal" class="col-form-label">Reason of Rejection:</label>
                   </div>
                   <div class="col-md-8">
                       <textarea name="displayreason" id="transactionReasonModal" class="form-control reasondisplay" rows="4" readonly></textarea>
                   </div>
               </div>
               <div class="form-row align-items-center mb-3">
                   <div class="col-md-4">
                       <label for="oldTransactionFilePathModal" class="col-form-label">Uploaded File:</label>
                   </div>
                   <div class="col-md-8">
                       <input type="text" name="displayfilepath" id="oldTransactionFilePathModal" class="form-control filePathDisplay" readonly>
                   </div>
               </div>
               <!-- <div class="form-row align-items-center mb-3">
                   <div class="col-md-4">
                       <label class="col-form-label">Select File:</label>
                   </div>
                   <div class="col-md-8">
                       <input type="file" name="transaction_slip" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
                   </div>
               </div> -->
               <div class="form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Transaction Slip:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" id="transaction-slip-zone">Drag and drop file here or click to upload</div>
            <input type="file" name="transaction_slip" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="transaction-slip-input">
            <div class="file-preview" id="transaction-slip-preview"></div>
        </div>
    </div>
               <!---------- Hidden fields ------->
               <input type="hidden" name="transaction_id" id="modalReuploadTransactionId">
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
                                                Please upload Transaction Slip.

	
	
	
		
		 <!-- Upload Transaction Slip -->
         <br><button type="button" class="btn btn-sm btn-info transactionUploadButton" 
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
                            <!-- <div class="form-row align-items-center mb-3">
                                <div class="col-md-4">
                                    <label for="TransactionFilePathModal" class="col-form-label">Uploaded File:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="displayfilepath" id="TransactionFilePathModal" class="form-control filePathDisplay" readonly>
                                </div>
                            </div> -->

                            
                            <!-- <div class="form-row align-items-center mb-3">
                                <div class="col-md-4">
                                    <label class="col-form-label">Select File:</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="file" name="transaction_slip" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="selectfile">
                                </div>
                            </div> -->

    <div class="form-row align-items-center mb-3">
        <div class="col-md-4">
            <label class="col-form-label">Transaction Slip:</label>
        </div>
        <div class="col-md-8">
            <div class="drop-zone" id="transaction-slip-zone">Drag and drop file here or click to upload</div>
            <input type="file" name="transaction_slip" accept=".png, .jpg, .jpeg, .pdf" class="form-control-file" id="transaction-slip-input">
            <div class="file-preview" id="transaction-slip-preview"></div>
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
                                            ($registration['registration_status'] === 'transaction_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['transaction_slip_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['payment_receipt_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['payment_receipt_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['exam_confirmation_letter_path'])) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>
	
<?php if ($registration['payment_receipt_status'] === 'pending' ): ?>
	

<br><br>
<button type="button" class="btn btn-sm btn-danger receiptUpdateButton" 
data-toggle="modal" 
data-target="#receiptUpdateModal"
data-receipt-id="<?= htmlspecialchars($registration['receipt_id']) ?>"
data-receipt-status="<?= htmlspecialchars($registration['payment_receipt_status']) ?>"
data-receipt-reason="<?= htmlspecialchars($registration['payment_receipt_reason']) ?>">
    Verify
</button>
<!-- Modal -->
<div class="modal fade" id="receiptUpdateModal" tabindex="-1" aria-labelledby="receiptUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="verify_receipt.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptUpdateModalLabel">Accept or Reject This Payment Receipt</h5>
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
                                        <input type="radio" name="receiptStatusOption" id="receipteaccept" value="accept">
                                        <label for="receipteaccept">Accept</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="receiptStatusOption" id="receiptreject" value="reject">
                                        <label for="receiptreject">Reject</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="receiptStatusOption" id="receiptpending" value="pending">
                                        <label for="receiptpending">Pending</label>
                                    </div>
                                </td>
                            </tr>
                            <tr id="receiptReasonRow" style="display: none;">
                                <td>Reason:</td>
                                <td>
                                    <textarea class="form-control" id="ModalReceiptReasonInput" name="payment_receipt_reason" placeholder="Enter your reason" rows="4"></textarea>
                                </td>
                            </tr>
                            <!-- Hidden fields to store data to be sent to upload php files -->
                            <input type="hidden" name="receipt_id" id="modalReceiptID">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="confirmReceiptButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>                         
    
<?php elseif ($registration['payment_receipt_status'] === 'reject' ): ?>   

<br><br>Please wait for Lecturer to reupload this Payment Receipt
<?php else: ?>
<?php endif; ?>


               



                                                <?php else: ?>
                                                Please wait for lecturer to upload the Payment Receipt.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if (
                                            ($registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'receipt_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['transaction_slip_status'] === 'accept'
                                        ): ?>
                                            <?php if (!empty($registration['exam_confirmation_letter_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['exam_confirmation_letter_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1" && empty($registration['certificate_path'])) { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>

<?php if ($registration['exam_confirmation_letter_status'] === 'pending' ): ?>
	

    <br><br>
<button type="button" class="btn btn-sm btn-danger examletterUpdateButton" 
data-toggle="modal" 
data-target="#examletterUpdateModal"
data-examletter-id="<?= htmlspecialchars($registration['confirmation_id']) ?>"
data-examletter-status="<?= htmlspecialchars($registration['exam_confirmation_letter_status']) ?>"
data-examletter-reason="<?= htmlspecialchars($registration['exam_confirmation_letter_reason']) ?>">
    Verify
</button>
<!-- Modal -->
<div class="modal fade" id="examletterUpdateModal" tabindex="-1" aria-labelledby="examletterUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="verify_examconfirmationletter.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="examletterUpdateModalLabel">Accept or Reject This Confirmation Letter</h5>
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
                                        <input type="radio" name="examletterStatusOption" id="eclaccept" value="accept">
                                        <label for="eclaccept">Accept</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="examletterStatusOption" id="eclreject" value="reject">
                                        <label for="eclreject">Reject</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="examletterStatusOption" id="eclpending" value="pending">
                                        <label for="eclpending">Pending</label>
                                    </div>
                                </td>
                            </tr>
                            <tr id="examletterReasonRow" style="display: none;">
                                <td>Reason:</td>
                                <td>
                                    <textarea class="form-control" id="ModalExamLetterReasonInput" name="exam_confirmation_letter_reason" placeholder="Enter your reason" rows="4"></textarea>
                                </td>
                            </tr>
                            <!-- Hidden fields to store data to be sent to upload php files -->
                            <input type="hidden" name="confirmation_id" id="modalExamLetterID">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="examletterConfirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>
    
<?php elseif ($registration['exam_confirmation_letter_status'] === 'reject' ): ?>   

<br><br>Please wait for Lecturer to reupload this Exam Confirmation Letter
<?php else: ?>
<?php endif; ?>







                                                <?php else: ?>
                                                Please wait for lecturer to uplaod Exam Confirmation Letter.
                                            <?php endif; ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>


                                    <td>
                                        <?php if (
                                            ($registration['registration_status'] === 'examletter_submitted' ||
                                            $registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['payment_receipt_status'] === 'accept' && $registration['exam_confirmation_letter_status'] === 'accept'
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
                                            ($registration['registration_status'] === 'result_submitted' ||
                                            $registration['registration_status'] === 'certificate_submitted'
                                        )&& $registration['payment_receipt_status'] === 'accept' && $registration['exam_confirmation_letter_status'] === 'accept'
                                        ): ?>



<?php if ($registration['exam_status'] === 'pass' ): ?>
    <?php if ($registration['publish'] === 'published'): ?>

                                            <?php if (!empty($registration['certificate_path'])): ?>
                                                <a href="<?= htmlspecialchars($registration['certificate_path']) ?>" class="btn btn-sm btn-info" onclick="return handleNotification('<?= $registration['registration_id'] ?>')" target="_blank">Download</a>
                                                <?php if ($registration['notification'] == "1") { ?>
                                                    <span class="notification"></span>
                                                <?php } ?>  

	
<?php if ($registration['certificate_status'] === 'pending' ): ?>
	

    <br><br>
<button type="button" class="btn btn-sm btn-danger certificateUpdateButton" 
data-toggle="modal" 
data-target="#certificateUpdateModal"
data-certificate-id="<?= htmlspecialchars($registration['certificate_id']) ?>"
data-certificate-status="<?= htmlspecialchars($registration['certificate_status']) ?>"
data-certificate-reason="<?= htmlspecialchars($registration['certificate_reason']) ?>">
    Verify
</button>
<!-- Modal -->
<div class="modal fade" id="certificateUpdateModal" tabindex="-1" aria-labelledby="certificateUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="verify_certificate.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="certificateUpdateModalLabel">Modal Table</h5>
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
                                        <input type="radio" name="certificateStatusOption" id="certificateaccept" value="accept">
                                        <label for="certificateaccept">Accept</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="certificateStatusOption" id="certificatereject" value="reject">
                                        <label for="certificatereject">Reject</label>
                                    </div>
                                    <div>
                                        <input type="radio" name="certificateStatusOption" id="certificatepending" value="pending">
                                        <label for="certificatepending">Pending</label>
                                    </div>
                                </td>
                            </tr>
                            <tr id="certificateReasonRow" style="display: none;">
                                <td>Reason:</td>
                                <td>
                                    <textarea class="form-control" id="ModalCertificateReasonInput" name="certificate_reason" placeholder="Enter your reason" rows="4"></textarea>
                                </td>
                            </tr>
                            <!-- Hidden fields to store data to be sent to upload php files -->
                            <input type="hidden" name="certificate_id" id="modalCertificateID">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="certificateConfirmButton">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

    
<?php elseif ($registration['certificate_status'] === 'reject' ): ?>   
    <br><br>
Please wait for Lecturer to reupload this Certificate
<?php else: ?>
<?php endif; ?>




                <?php else: ?>
                Please wait for lecturer to upload the Certificate.
            <?php endif; ?>
            <?php else: ?>
        Certificate not available yet
    <?php endif; ?>
            
<?php elseif ($registration['exam_status'] === 'fail' ): ?>   
    <?php if ($registration['publish'] === 'published'): ?>
	
    Certificate not available due to failed exam
                
    <?php else: ?>
        Certificate not available yet
    <?php endif; ?>

<?php else: ?>
    Certificate not available yet
<?php endif; ?>


                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10">No registrations found.</td>
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
        // Display accurate information on modal table (Edit) 
        document.addEventListener("DOMContentLoaded", function () {
            const transactionUploadButtons = document.querySelectorAll(".transactionUploadButton");
            const invoiceUpdateButtons = document.querySelectorAll(".invoiceUpdateButton");
            const receiptUpdateButtons = document.querySelectorAll(".receiptUpdateButton");
            const examletterUpdateButtons = document.querySelectorAll(".examletterUpdateButton");
            const certificateUpdateButtons = document.querySelectorAll(".certificateUpdateButton");
            const transactionReuploadButtons = document.querySelectorAll(".transactionReuploadButton");
            const regformReuploadButtons = document.querySelectorAll(".regformReuploadButton");
            

            //////////////////////////////////////////// Upload ///////////////////////////////////////
            // Upload Transaction Slip
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


            //////////////////////////////////////////// Reupload ///////////////////////////////////////
            // Update Payment Invoice (mysword)
            invoiceUpdateButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const invoiceUpdateID = this.getAttribute("data-invoice-id") || "";
                    const invoiceStatus = this.getAttribute("data-invoice-status") || "";
                    const invoiceReason = this.getAttribute("data-invoice-reason") || "";

                    document.getElementById("modalInvoiceID").value = invoiceUpdateID;
                    document.getElementById("modalInvoiceStatus").value = invoiceStatus;
                    document.getElementById("ModalinvoiceReasonInput").value = invoiceReason;
                });
            });
            
            // Update Payment Receipt
            receiptUpdateButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const receiptUpdateID = this.getAttribute("data-receipt-id") || "";
                    const receiptStatus = this.getAttribute("data-receipt-status") || "";
                    const receiptReason = this.getAttribute("data-receipt-reason") || "";

                    document.getElementById("modalReceiptID").value = receiptUpdateID;
                    document.getElementById("modalreceiptStatus").value = receiptStatus;
                    document.getElementById("ModalReceiptReasonInput").value = receiptReason;
                });
            });


            // Update Exam Confirmation Letter
            examletterUpdateButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const examletterUpdateID = this.getAttribute("data-examletter-id") || "";
                    const examletterStatus = this.getAttribute("data-examletter-status") || "";
                    const examletterReason = this.getAttribute("data-examletter-reason") || "";

                    document.getElementById("modalExamLetterID").value = examletterUpdateID;
                    document.getElementById("modalexamletterStatus").value = examletterStatus;
                    document.getElementById("ModalExamLetterReasonInput").value = examletterReason;
                });
            });

            // Update Certificate
            certificateUpdateButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const certificateUpdateID = this.getAttribute("data-certificate-id") || "";
                    const certificateStatus = this.getAttribute("data-certificate-status") || "";
                    const certificateReason = this.getAttribute("data-certificate-reason") || "";

                    document.getElementById("modalCertificateID").value = certificateUpdateID;
                    document.getElementById("modalcertificateStatus").value = certificateStatus;
                    document.getElementById("ModalCertificateReasonInput").value = certificateReason;
                });
            });
	

            // Transaction Slip
            transactionReuploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const transactionFilePath = this.getAttribute("data-retransaction-filepath") || "";
                    const transactionId = this.getAttribute("data-retransaction-id") || "";
                    const transactionReason = this.getAttribute("data-retransaction-reason") || "";

                    // Extract the valid filename (after the last hyphen)
                    const fileName = transactionFilePath.substring(transactionFilePath.lastIndexOf('/') + 1).substring(24);

                    // Set modal field values (Display)
                    document.getElementById("oldTransactionFilePathModal").value = fileName;
                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalReuploadTransactionId").value = transactionId;
                    document.getElementById("transactionReasonModal").value = transactionReason;
                });
            });

            // Registration Form
            regformReuploadButtons.forEach(button => {
                button.addEventListener("click", function () {
                    const regFormId = this.getAttribute("data-reregform-id") || "";
                    const regFormReason = this.getAttribute("data-reregform-reason") || "";

                    // Set modal field values (For functionality in upload.php files)
                    document.getElementById("modalReuploadRegFormId").value = regFormId;
                    document.getElementById("regformReasonModal").value = regFormReason;
                });
            });


            // drag and drop
            const dropZone = document.getElementById('transaction-slip-zone');
        const fileInput = document.getElementById('transaction-slip-input');
        const preview = document.getElementById('transaction-slip-preview');

        // Click to trigger file input
        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

        // Handle file selection through input
        fileInput.addEventListener('change', (event) => {
            handleFileUpload(event.target.files);
        });

        // Handle drag-over event
        dropZone.addEventListener('dragover', (event) => {
            event.preventDefault();
            dropZone.classList.add('dragover');
        });

        // Handle drag-leave event
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        // Handle drop event
        dropZone.addEventListener('drop', (event) => {
            event.preventDefault();
            dropZone.classList.remove('dragover');
            fileInput.files = event.dataTransfer.files; // Assign dropped files to the input
            handleFileUpload(event.dataTransfer.files);
        });

        function handleFileUpload(files) {
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

        //////////////////////////////// Reupload Files (Store Reason and ID)  ////////////////////////
        $('#invoiceUpdateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var invoiceId = button.data('invoice-id'); 
            var invoiceStatus = button.data('invoice-status'); 
            var invoiceReason = button.data('invoice-reason'); 

            var modal = $(this);
            
            modal.find('#modalInvoiceID').val(invoiceId); 
            modal.find('#ModalinvoiceReasonInput').val(invoiceReason); 

            // Set radio button
            modal.find('input[type=radio][name=invoiceStatusOption]').each(function() {
                if ($(this).val() === invoiceStatus) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });
        });


        $('#receiptUpdateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var invoiceId = button.data('receipt-id'); 
            var receiptStatus = button.data('receipt-status'); 
            var receiptReason = button.data('receipt-reason'); 

            var modal = $(this);
            
            modal.find('#modalReceiptID').val(invoiceId); 
            modal.find('#ModalReceiptReasonInput').val(receiptReason); 

            // Set radio button
            modal.find('input[type=radio][name=receiptStatusOption]').each(function() {
                if ($(this).val() === receiptStatus) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });
        });


        $('#examletterUpdateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var examletterId = button.data('examletter-id'); 
            var examletterStatus = button.data('examletter-status'); 
            var examletterReason = button.data('examletter-reason'); 

            var modal = $(this);
            
            modal.find('#modalExamLetterID').val(examletterId); 
            modal.find('#ModalExamLetterReasonInput').val(examletterReason); 

            // Set radio button
            modal.find('input[type=radio][name=examletterStatusOption]').each(function() {
                if ($(this).val() === examletterStatus) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });
        });

        $('#certificateUpdateModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var certificateId = button.data('certificate-id'); 
            var certificateStatus = button.data('certificate-status'); 
            var certificateReason = button.data('certificate-reason'); 

            var modal = $(this);
            
            modal.find('#modalCertificateID').val(certificateId); 
            modal.find('#ModalCertificateReasonInput').val(certificateReason); 

            // Set radio button
            modal.find('input[type=radio][name=certificateStatusOption]').each(function() {
                if ($(this).val() === certificateStatus) {
                    $(this).prop('checked', true);
                } else {
                    $(this).prop('checked', false);
                }
            });
        });

        // Registration Form
        $('#reuploadRegFormModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var regFormId = button.data('reregform-id'); 
            var regFormReason = button.data('reregform-reason'); 

            var modal = $(this);
            modal.find('#modalReuploadRegFormId').val(regFormId); 
            modal.find('#regformReasonModal').val(regFormReason); 

        });

                

        ////////////////////////////////// Reupload Files (Show Reason Input When Reject) /////////////////////////////////////////
        // JavaScript to toggle reason text box
        $(document).ready(function () {

            /////// Payment Invoice
            $('input[name="invoiceStatusOption"]').change(function () {
                if ($('#invoicereject').is(':checked')) {
                    $('#reasonRow').show();
                } else {
                    $('#reasonRow').hide();
                }
            });

            $('#confirmButton').click(function (event) { 
                const selectedOption = $('input[name="invoiceStatusOption"]:checked').val();
                const reason = $('#ModalinvoiceReasonInput').val();

                // If the status is "invoicereject" and the reason is empty, alert the user and stop form submission
                if (selectedOption === 'reject' && !reason) {
                    alert('Please provide a reason for rejection.');
                    event.preventDefault(); 
                    return;
                }

                // If a reason is provided, show the selection and reason
                alert(`You selected: ${selectedOption}${selectedOption === 'reject' ? ' with reason: ' + reason : ''}`);
                $('#invoiceUpdateModal').modal('hide');
            });


            ////// Payment Receipt
            $('input[name="receiptStatusOption"]').change(function () {
                if ($('#receiptreject').is(':checked')) {
                    $('#receiptReasonRow').show();
                } else {
                    $('#receiptReasonRow').hide();
                }
            });

            $('#confirmReceiptButton').click(function (event) { 
                const selectedOption = $('input[name="receiptStatusOption"]:checked').val();
                const reason = $('#ModalReceiptReasonInput').val();

                // If the status is "receiptreject" and the reason is empty, alert the user and stop form submission
                if (selectedOption === 'reject' && !reason) {
                    alert('Please provide a reason for rejection.');
                    event.preventDefault(); 
                    return;
                }

                // If a reason is provided, show the selection and reason
                alert(`You selected: ${selectedOption}${selectedOption === 'reject' ? ' with reason: ' + reason : ''}`);
                $('#receiptUpdateModal').modal('hide');
            });

            /////// Exam Confirmation Letter
            $('input[name="examletterStatusOption"]').change(function () {
                if ($('#eclreject').is(':checked')) {
                    $('#examletterReasonRow').show();
                } else {
                    $('#examletterReasonRow').hide();
                }
            });

            $('#examletterConfirmButton').click(function (event) { 
                const selectedOption = $('input[name="examletterStatusOption"]:checked').val();
                const reason = $('#ModalExamLetterReasonInput').val();

                // If the status is "eclreject" and the reason is empty, alert the user and stop form submission
                if (selectedOption === 'reject' && !reason) {
                    alert('Please provide a reason for rejection.');
                    event.preventDefault(); 
                    return;
                }

                // If a reason is provided, show the selection and reason
                alert(`You selected: ${selectedOption}${selectedOption === 'reject' ? ' with reason: ' + reason : ''}`);
                $('#examletterUpdateModal').modal('hide');
            });

            /////// Certificate
            $('input[name="certificateStatusOption"]').change(function () {
                if ($('#certificatereject').is(':checked')) {
                    $('#certificateReasonRow').show();
                } else {
                    $('#certificateReasonRow').hide();
                }
            });

            $('#certificateConfirmButton').click(function (event) { 
                const selectedOption = $('input[name="certificateStatusOption"]:checked').val();
                const reason = $('#ModalCertificateReasonInput').val();

                // If the status is "certificatereject" and the reason is empty, alert the user and stop form submission
                if (selectedOption === 'reject' && !reason) {
                    alert('Please provide a reason for rejection.');
                    event.preventDefault(); 
                    return;
                }

                // If a reason is provided, show the selection and reason
                alert(`You selected: ${selectedOption}${selectedOption === 'reject' ? ' with reason: ' + reason : ''}`);
                $('#certificateUpdateModal').modal('hide');
            });


     
        });


        //////////////////////////////////////////// Upload ///////////////////////////////////////
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

        // Transaction Slip
        $('#reuploadTransactionModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); 
            var transactionId = button.data('retransaction-id'); 
            var transactionReason = button.data('retransaction-reason'); 

            var modal = $(this);
            modal.find('#modalReuploadTransactionId').val(transactionId); 
            modal.find('#transactionReasonModal').val(transactionReason); 

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

            // Add custom placeholder text and icon
            $('.dataTables_filter input')
                .attr('placeholder', 'Search...')
                .before('<i class="fas fa-search" style="margin-right: 10px; color: #007BFF;"></i>');
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