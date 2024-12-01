<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
    <script>
        // Convert input to uppercase
        function toUpperCase(field) {
            field.value = field.value.toUpperCase();
        }
    </script>
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Available Certifications";
        $pageHeaderClass = "header_image_cert_stu";
        $pageHeaderTitle = "Available Certifications";
        $pageCertStuActive = "pageCertStuActive";
        include 'include/stu_main_header.php';
    ?>

<!-- Main Content -->
    <main class="student_form">
    <?php

        // Database credentials
        $servername = 'localhost';  // Typically 'localhost' for local databases
        $db = 'cert_reg_management_db';  // Replace with your database name
        $user = 'root';  // Replace with your database username
        $pass = '';  // Replace with your database password

        // Create a connection
        $conn = new mysqli($servername, $user, $pass, $db);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
///////////////////////////////////////////////// Reupload Form//////////////////////////////
if (isset($_GET['regform_id'])) {
    $regformID = $_GET['regform_id'];

    // } required

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $regformID = $_POST['regform_id'];

    // Prepare the SQL statement to retrieve the ID
    $stmt = $conn->prepare("SELECT form_id FROM reg_registrationform WHERE form_id = ?");
    $stmt->bind_param("s", $regformID); // Use "s" for string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the record exists
    if ($result->num_rows > 0) {
        // Fetch the form_id
        $row = $result->fetch_assoc();
        $reuploadformID = $row['form_id']; // Store the form_id directly
    } else {
        // Handle the case where the record does not exist
        $reuploadformID = ''; // Set to an empty value if not found
    }

    echo "$regformID";
    
    // Retrieve stored form data
    $formsql = "SELECT form_data FROM form_data WHERE form_id = '$regformID'";
    $formresult = mysqli_query($conn, $formsql);
    
    if ($formresult) {
        $formrow = mysqli_fetch_assoc($formresult);
        $form_data = json_decode($formrow['form_data'], true);
    } else {
        echo "Error: " . mysqli_error($conn);
    }

    
}



///////////////////////////////////////////////// Check if the certification name is provided//////////////////////////////
if (isset($_GET['certificationID'])) {
    $certName =  $conn->real_escape_string(urldecode($_GET['certificationID']));
    // Prepare the SQL statement to retrieve the ID
    $stmt = $conn->prepare("SELECT certification_id FROM certifications WHERE certification_name = ?");
    $stmt->bind_param("s", $certName); // Use "s" for string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the certification exists
    if ($result->num_rows > 0) {
        // Fetch the certification ID
        $row = $result->fetch_assoc();
        $certificationID = $row['certification_id']; // Store the form_id directly
    
    } else {
        // Handle the case where the certification does not exist
        $certificationID = ''; // Set to an empty value if not found
    }
}


    // Check if the session variables are set
    if (isset($_SESSION['student_id'])) {
        // Retrieve the values from the session
        $studentId = $_SESSION['student_id'];
        $studentname = $_SESSION['student_full_name']; 

    } else {
        // Handle the case where session variables are not set
        echo "<p></p>";
    }


     

///////////////////////////////////////////////// Check if the certification name is provided//////////////////////////////

        // Close the connection
        $conn->close();
        ?>
    <div class="container">
        <h1 class="text-center mb-4">Certification Application Form</h1>
        <form onsubmit="return validateForm()" action="stu_overview_cert_formfiller.php" method="post">
            <!---------- Reupload Form ------->
            <input type="hidden" name="reupload_form_id" id="modalReuploadRegFormId" value="<?php echo htmlspecialchars($reuploadformID ?? ''); ?>">
            <input type="hidden" name="new_upload_form_id" id="modalNewuploadRegFormId" value="<?php echo htmlspecialchars($certName ?? ''); ?>">
        
            <div class="form-section">
            <div class="alert alert-info mt-4">
                <p class="font-weight-bold mb-2">Instructions::</p>
                <ol>
                    <li>Please complete ALL the information below. <strong>Please write legibly in CAPITAL LETTERS.</strong></li>
                    <li><strong>MSTB reserves the right to decline the application for failure to complete the form.</strong></li>
                    <li>Select the certification type and remit the fee together with this application. Payment may be made in cash or cheque (payable to “Malaysian Software Testing Board (MSTB)”.). For other arrangements, please contact +603-8076 6100</li>
                    <li>Payment for the exam is to be paid in full at least seven (7) prior to the examination date.</li>
                    <li>Receipt of payment will be issued after confirmation of payment from the relevant parties e.g. bank or MSTB Finance Department.</li>
                </ol>
            </div>
                <h2>Certification Examination Type and Fee</h2>
                <label>Select Certification Type:</label><br>
                <div class="form-check">
                    <input id="cert1" class="form-check-input" type="radio" name="type_radio" <?php echo ($form_data['type_radio'] ?? '') == 'ctfl' ? 'checked' : ''; ?> value="ctfl"  required>
                    <label for="cert1" class="form-check-label">ISTQB Certified Tester Foundation Level (CTFL) - <strong>RM 900.00</strong></label>
                </div>
                <div class="form-check">
                    <input id="cert2" class="form-check-input" type="radio" name="type_radio" <?php echo ($form_data['type_radio'] ?? '') == 'cpre' ? 'checked' : ''; ?> value="cpre">
                    <label for="cert2" class="form-check-label">IREB Certified Professional for Requirements Engineering Foundation Level (CPRE-FL) -  <strong>RM 900.00</strong></label>
                </div>
                <div class="form-check">
                    <input id="cert3" class="form-check-input" type="radio" name="type_radio" <?php echo ($form_data['type_radio'] ?? '') == 'agile' ? 'checked' : ''; ?> value="agile">
                    <label for="cert3" class="form-check-label">IREB RE@Agile Primer -  <strong>RM 900.00</strong></label>
                </div>
                <div class="form-check">
                    <input id="cert4" class="form-check-input" type="radio" name="type_radio" <?php echo ($form_data['type_radio'] ?? '') == 'advanced' ? 'checked' : ''; ?> value="advanced">
                    <label for="cert4" class="form-check-label">IREB Advanced Level Requirements Elicitation - Practitioner -  <strong>RM 1050.00</strong></label>
                </div>
            </div>

            <div class="form-section">
                <h2>Mode/Terms of Payment</h2>
                <label>Mode of Payment:</label><br>
                <div class="form-check">
                    <input id="payment1" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'company' ? 'checked' : ''; ?> value="company">
                    <label for="payment1" class="form-check-label">Company Sponsored</label>
                </div>
                <div class="form-check">
                    <input id="payment2" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'cash_che' ? 'checked' : ''; ?> value="cash_che">
                    <label for="payment2" class="form-check-label">Cash Cheque</label>
                </div>
                <div class="form-check">
                    <input id="payment3" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'self' ? 'checked' : ''; ?> value="self">
                    <label for="payment3" class="form-check-label">Self-Sponsored</label>
                </div>
                <div class="form-check">
                    <input id="payment4" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'bank_che' ? 'checked' : ''; ?> value="bank_che">
                    <label for="payment4" class="form-check-label">Bankers' Cheque</label>
                </div>
                <div class="form-check">
                    <input id="payment5" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'jom' ? 'checked' : ''; ?> value="jom">
                    <label for="payment5" class="form-check-label">JomPAY</label>
                </div>
                <div class="form-check">
                    <input id="payment6" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'credit' ? 'checked' : ''; ?> value="credit">
                    <label for="payment6" class="form-check-label">Credit Card</label>
                </div>
                <div class="form-check">
                    <input id="payment7" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'online' ? 'checked' : ''; ?> value="online">
                    <label for="payment7" class="form-check-label">Online Payment</label>
                </div>
                <div class="form-check">
                    <input id="payment8" class="form-check-input" type="radio" name="payment_radio" <?php echo ($form_data['payment_radio'] ?? '') == 'cash' ? 'checked' : ''; ?> value="cash">
                    <label for="payment8" class="form-check-label">Cash</label>
                </div>
            </div>

            <div class="form-section">
                <h2>Personal Information</h2>
                <div class="form-group">
                    <label for="full_name_textbox">Full Name (as per IC / passport):</label>
                    <input id="full_name_textbox" type="text" class="form-control" name="full_name_textbox" required placeholder="Enter your Full Name"
                    oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
                    pattern="[A-Za-z\s]+" maxlength="82" value="<?php echo htmlspecialchars($form_data['full_name_textbox'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="nationality_textbox">Nationality:</label>
                    <input id="nationality_textbox" type="text" class="form-control" name="nationality_textbox" required placeholder="Enter your Nationality"
                    oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
                        pattern="[A-Za-z\s]+" value="<?php echo htmlspecialchars($form_data['nationality_textbox'] ?? ''); ?>">
                </div>
                <label>New IC Number (Malaysian Only)</label>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="ic_num_1_textbox" class="text-secondary">Date of Birth Code:</label>
                        <input id="ic_num_1_textbox" type="number" class="form-control" name="ic_num_1_textbox" 
                            placeholder="First 6 digits" oninput="toUpperCase(this)" 
                            title="Please enter the first 6 digits of your Date of Birth Code." 
                            pattern="^\d{6}$" min="0" max="999999" value="<?php echo htmlspecialchars($form_data['ic_num_1_textbox'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="ic_num_2_textbox" class="text-secondary">State Code:</label>
                        <input id="ic_num_2_textbox" type="number" class="form-control" name="ic_num_2_textbox" 
                            placeholder="Next 2 digits" oninput="toUpperCase(this)" 
                            title="Please enter the 2 digits of state code after your DOB code." value="<?php echo htmlspecialchars($form_data['ic_num_2_textbox'] ?? ''); ?>"
                            pattern="^\d{2}$" min="0" max="999999">
                    </div>
                    <div class="col-md-4">
                        <label for="ic_num_3_textbox" class="text-secondary">Unique Code:</label>
                        <input id="ic_num_3_textbox" type="number" class="form-control" name="ic_num_3_textbox" 
                            placeholder="Last 4 digits" oninput="toUpperCase(this)" 
                            title="Please enter last 4 digits of your unique code." value="<?php echo htmlspecialchars($form_data['ic_num_3_textbox'] ?? ''); ?>"
                            pattern="^\d{4}$"  min="0" max="9999">
                    </div>
                </div>
                <div class="form-group">
                    <label for="pass_num_textbox">Passport Number (Non-Malaysian only):</label>
                    <input id="pass_num_textbox" type="text" class="form-control" name="pass_num_textbox" placeholder="Enter your Passport Number"
                    oninput="toUpperCase(this)" title="Please enter alphabet and integer only." maxlength="32"
					value="<?php echo htmlspecialchars($form_data['pass_num_textbox'] ?? ''); ?>">
                </div>

                <label>Date of Birth</label>
                <div class="form-group row">
                <div class="col-md-4">
                    <label for="dob_day_textbox" class="text-secondary">Day:</label>
                    <input id="dob_day_textbox" type="number" class="form-control" name="dob_day_textbox" 
                        placeholder="Enter the DAY (01-31) of your birth" oninput="toUpperCase(this)" 
                        title="Please enter the day of your birth (01-31)." 
                        min="1" max="31" 
                        pattern="^(?:[1-9]|[12][0-9]|3[01])$" required value="<?php echo htmlspecialchars($form_data['dob_day_textbox'] ?? ''); ?>">
                </div>
                    <div class="col-md-4">
                        <label for="dob_month_textbox" class="text-secondary">Month:</label>
                        <input id="dob_day_textbox" type="number" class="form-control" name="dob_month_textbox" 
                            placeholder="Enter the MONTH (01-12) of your birth" oninput="toUpperCase(this)" 
                            title="Please enter in uppercase letters." 
                            min="1" max="12" 
                            pattern="^(?:[1-9]|[12][0-9]|3[01])$" required value="<?php echo htmlspecialchars($form_data['dob_month_textbox'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label for="dob_year_textbox" class="text-secondary">Year:</label>
                        <input id="dob_day_textbox" type="number" class="form-control" name="dob_year_textbox" 
                            placeholder="Enter the YEAR of your birth" oninput="toUpperCase(this)" 
                            title="Please enter in uppercase letters." 
                            pattern="^\d{4}$"  min="0" max="9999" required value="<?php echo htmlspecialchars($form_data['dob_year_textbox'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label>Gender:</label><br>
                    <div class="form-check form-check-inline">
                        <input id="gender1" class="form-check-input" type="radio" name="gender_radio" <?php echo ($form_data['gender_radio'] ?? '') == 'male' ? 'checked' : ''; ?> value="male" > 
                        <label for="gender1" class="form-check-label">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="gender2" class="form-check-input" type="radio" name="gender_radio" <?php echo ($form_data['gender_radio'] ?? '') == 'female' ? 'checked' : ''; ?>  value="female"> 
                        <label for="gender2" class="form-check-label">Female</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pob_textbox">Place of Birth:</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="pob_textbox" 
                        id="pob_textbox"
                        required 
                        placeholder="Enter your Place of Birth"
                        title="Please enter alphabetic characters and commas (spaces allowed)."
						value="<?php echo htmlspecialchars($form_data['pob_textbox'] ?? ''); ?>"
                        pattern="[A-Za-z\s,]+" maxlength="35" oninput="toUpperCase(this)" >
                </div>
                <div class="form-group">
                <label>Race:</label><br>
                <div class="form-check form-check-inline">
                    <input id="race1" class="form-check-input" type="radio" name="race_radio" value="malay" 
                        onclick="this.form.race_other_textbox.required = false;" 
						<?php echo ($form_data['race_radio'] ?? '') == 'malay' ? 'checked' : ''; ?> required> 
                    <label for="race1" class="form-check-label">Malay</label>
                </div>
                <div class="form-check form-check-inline">
                    <input id="race2" class="form-check-input" type="radio" name="race_radio" value="chinese" 
                        onclick="this.form.race_other_textbox.required = false;"
						<?php echo ($form_data['race_radio'] ?? '') == 'chinese' ? 'checked' : ''; ?> "> 
                    <label for="race2" class="form-check-label">Chinese</label>
                </div>
                <div class="form-check form-check-inline">
                    <input id="race3" class="form-check-input" type="radio" name="race_radio" value="indian" 
                        onclick="this.form.race_other_textbox.required = false;"
						<?php echo ($form_data['race_radio'] ?? '') == 'indian' ? 'checked' : ''; ?> > 
                    <label for="race3" class="form-check-label">Indian</label>
                </div>
                <div class="form-check form-check-inline">
                    <input id="otherrace" class="form-check-input" type="radio" name="race_radio" value="other" 
                        onclick="this.form.race_other_textbox.required = this.checked;"
						<?php echo ($form_data['race_radio'] ?? '') == 'other' ? 'checked' : ''; ?> >
                    <label for="otherrace" class="form-check-label">Other:</label>
                    <input type="text" class="form-control d-inline-block" style="width: auto;" name="race_other_textbox"
                        oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
						value="<?php echo htmlspecialchars($form_data['race_other_textbox'] ?? ''); ?>"
                        pattern="[A-Za-z\s]+" id="race_other_input">
                </div>
                </div>
                <div class="form-group">
                    <label for="company_name_textbox">Company Name:</label>
                    <input id="company_name_textbox" type="text" class="form-control" name="company_name_textbox" required placeholder="Enter your Company Name e.g SWINBURNE"
                    oninput="toUpperCase(this)"  title="Please enter alphabetic characters only (spaces allowed)." 
					value="<?php echo htmlspecialchars($form_data['company_name_textbox'] ?? ''); ?>"
                    pattern="[A-Za-z\s,]+" maxlength="35">
                </div>
                <div class="form-group">
                    <label for="job_textbox">Job Title:</label>
                    <input id="job_textbox" type="text" class="form-control" name="job_textbox" required placeholder="Enter your Job Title e.g STUDENT OR LECTURER"
                    oninput="toUpperCase(this)"  title="Please enter alphabetic characters only (spaces allowed)." 
					value="<?php echo htmlspecialchars($form_data['job_textbox'] ?? ''); ?>"
                    pattern="[A-Za-z\s,]+" maxlength="35">
                </div>
                <div class="form-group">
                    <label>Position Level:</label><br>
                    <div class="form-check form-check-inline">
                        <input id="position1" class="form-check-input" type="radio" name="position_level_radio" value="senior_man" 
                            onclick="this.form.position_other_textbox.required = false;" required
							<?php echo ($form_data['position_level_radio'] ?? '') == 'senior_man' ? 'checked' : ''; ?> > 
                        <label for="position1" class="form-check-label">Senior Manager</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="position2" class="form-check-input" type="radio" name="position_level_radio" value="manager" 
                            onclick="this.form.position_other_textbox.required = false;"
							<?php echo ($form_data['position_level_radio'] ?? '') == 'manager' ? 'checked' : ''; ?> > 
                        <label for="position2" class="form-check-label">Manager</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="position3" class="form-check-input" type="radio" name="position_level_radio" value="senior_exec" 
                            onclick="this.form.position_other_textbox.required = false;"
							<?php echo ($form_data['position_level_radio'] ?? '') == 'senior_exec' ? 'checked' : ''; ?>> 
                        <label for="position3" class="form-check-label">Senior Executive</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="position4" class="form-check-input" type="radio" name="position_level_radio" value="exec" 
                            onclick="this.form.position_other_textbox.required = false;"
							<?php echo ($form_data['position_level_radio'] ?? '') == 'exec' ? 'checked' : ''; ?>> 
                        <label for="position4" class="form-check-label">Executive</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="position5" class="form-check-input" type="radio" name="position_level_radio" value="fresh_entry" 
                            onclick="this.form.position_other_textbox.required = false;"
							<?php echo ($form_data['position_level_radio'] ?? '') == 'fresh_entry' ? 'checked' : ''; ?>> 
                        <label for="position5" class="form-check-label">Fresh/Entry Level</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="position6" class="form-check-input" type="radio" name="position_level_radio" value="non_exec" 
                            onclick="this.form.position_other_textbox.required = false;"
							<?php echo ($form_data['position_level_radio'] ?? '') == 'non_exec' ? 'checked' : ''; ?>> 
                        <label for="position6" class="form-check-label">Non-Executive</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input id="position7" class="form-check-input" type="radio" name="position_level_radio" 
                            value="pos_other" onclick="this.form.position_other_textbox.required = this.checked;"
							<?php echo ($form_data['position_level_radio'] ?? '') == 'position_level_radio' ? 'checked' : ''; ?>>
                        <label for="position7" class="form-check-label">Other:</label>
                        <input type="text" name="position_other_textbox" id="position_other_input" 
                            class="form-control d-inline-block" style="width: auto;" 
                            oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
                            pattern="[A-Za-z\s]+" value="<?php echo htmlspecialchars($form_data['position_other_textbox'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="address_textbox">Correspondence Address:</label>
                    <input id="address_textbox" type="text" class="form-control" name="address_textbox" required placeholder="Enter the Address of where you currently stay"
                    oninput="toUpperCase(this)" title="The address must within maximum length." maxlength="114" value="<?php echo htmlspecialchars($form_data['address_textbox'] ?? ''); ?>">
                </div>


                <div class="form-group">
                    <label for="city_textbox">City:</label>
                    <input id="city_textbox" type="text" class="form-control" name="city_textbox" required placeholder="Enter your City e.g KUCHING"
                    oninput="toUpperCase(this)"   title="Please enter alphabetic characters only (spaces allowed)." 
                    pattern="[A-Za-z\s]+" value="<?php echo htmlspecialchars($form_data['city_textbox'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="postcode_textbox">Postcode:</label>
                    <input id="postcode_textbox" type="number" class="form-control" name="postcode_textbox" required placeholder="Enter your Postcode e.g 93350"
                    oninput="toUpperCase(this)" title="Please enter a 5-digit postcode." min="10000" max="99999" pattern="\d{5}" 
                    maxlength="5" value="<?php echo htmlspecialchars($form_data['postcode_textbox'] ?? ''); ?>" />
                </div>


                <div class="form-group">
                    <label for="state_country_textbox">State/Country:</label>
                    <input id="state_country_textbox" type="text" class="form-control" name="state_country_textbox" required placeholder="Enter your State or Country"
                    oninput="toUpperCase(this)"  title="Please enter alphabetic characters only (spaces allowed)." 
                    pattern="[A-Za-z\s]+" value="<?php echo htmlspecialchars($form_data['state_country_textbox'] ?? ''); ?>">
                </div>

                <label>Mobile Phone Number:</label>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="phone_code_textbox" class="text-secondary">Country Code (+):</label>
                        <input id="phone_code_textbox" type="number" class="form-control" name="phone_code_textbox" required 
    placeholder="Enter the country code (first 3 digits)" 
    maxlength="3" title="Please enter exactly 3 digits" value="<?php echo htmlspecialchars($form_data['phone_code_textbox'] ?? ''); ?>" />
                    </div>
                    <div class="col-md-8">
                        <label for="phone_textbox" class="text-secondary">Phone Number:</label>
                        <input id="phone_textbox" type="number" class="form-control" name="phone_textbox" required 
                            placeholder="Enter the rest of your Phone Number" 
                            title="Please enter numbers only." inputmode="numeric" value="<?php echo htmlspecialchars($form_data['phone_textbox'] ?? ''); ?>">
                    </div>
                </div>

                <label>Alternate Phone Number: (compulsory for online exam) </label>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="phone_alt_code_textbox" class="text-secondary">Country Code (+):</label>
                        <input id="phone_alt_code_textbox" type="text" class="form-control" name="phone_alt_code_textbox" 
						value="<?php echo htmlspecialchars($form_data['phone_alt_code_textbox'] ?? ''); ?>"
                        placeholder="Enter the country code (first 3 digits)" maxlength="3" title="Please enter exactly 3 digits" />
                    </div>
                    <div class="col-md-8">
                        <label for="phone_alt_textbox" class="text-secondary">Phone Number:</label>
                        <input id="phone_alt_textbox" type="text" class="form-control" name="phone_alt_textbox" placeholder="Enter the rest of your Alternate Number"
                        title="Please enter numbers only." inputmode="numeric" value="<?php echo htmlspecialchars($form_data['phone_alt_textbox'] ?? ''); ?>">
                    </div>
                </div>

                <label>Work Phone Number: </label>
                <div class="form-group row">
                    <div class="col-md-4">
                        <label for="phone_work_code_textbox" class="text-secondary">Country Code (+):</label>
                        <input id="phone_work_code_textbox" type="text" class="form-control" name="phone_work_code_textbox" 
						value="<?php echo htmlspecialchars($form_data['phone_work_code_textbox'] ?? ''); ?>"
                        placeholder="Enter the country code (first 3 digits)" maxlength="3" title="Please enter exactly 3 digits" />
                    </div>
                    <div class="col-md-8">
                        <label for="phone_work_textbox" class="text-secondary">Phone Number:</label>
                        <input id="phone_work_textbox" type="text" class="form-control" name="phone_work_textbox" placeholder="Enter the rest of your Work Number"
                        title="Please enter numbers only." inputmode="numeric" value="<?php echo htmlspecialchars($form_data['phone_work_textbox'] ?? ''); ?>">
                    </div>
                </div>

                <label>Email Address <i>(Exam link (online exam), result notification & e-certificate will be sent to your primary email)</i>: </label>
                <div class="form-group">
                    <label for="email_pri_textbox" class="text-secondary">Primary Email Address:</label>
                    <input id="email_pri_textbox" type="email" class="form-control" name="email_pri_textbox" required placeholder="Enter your email address"
                        title="Please enter a valid email address, e.g., example@example.com." value="<?php echo htmlspecialchars($form_data['email_pri_textbox'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="email_sec_textbox" class="text-secondary">Alternate Email Address:</label>
                    <input id="email_sec_textbox" type="email" class="form-control" name="email_sec_textbox" placeholder="Enter your alternate email address"
                    title="Please enter a valid email address, e.g., example@example.com." value="<?php echo htmlspecialchars($form_data['email_sec_textbox'] ?? ''); ?>">
                </div>

            </div>
            <div class="form-section">
            <h2>Education Background</h2>
            <div class="form-group">
                <label for="education_radio">Highest Education Level:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="phd" value="phd" 
                        onclick="this.form.edu_other_textbox.required = false;" <?php echo ($form_data['education_radio'] ?? '') == 'phd' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="phd">Doctorate (PhD)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="masters" value="masters" 
                        onclick="this.form.edu_other_textbox.required = false;" <?php echo ($form_data['education_radio'] ?? '') == 'masters' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="masters">Master's Degree</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="bachelor" value="bachelor" 
                        onclick="this.form.edu_other_textbox.required = false;" <?php echo ($form_data['education_radio'] ?? '') == 'bachelor' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="bachelor">Bachelor's Degree</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="diploma" value="diploma" 
                        onclick="this.form.edu_other_textbox.required = false;" <?php echo ($form_data['education_radio'] ?? '') == 'diploma' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="diploma">Diploma</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="p_cert" value="p_cert" 
                        onclick="this.form.edu_other_textbox.required = false;" <?php echo ($form_data['education_radio'] ?? '') == 'p_cert' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="p_cert">Professional Certificate</label>
                </div>
                <div class="form-check form-check-inline">
                    <input id="edu_other" class="form-check-input" type="radio" name="education_radio" value="edu_other" 
                        onclick="this.form.edu_other_textbox.required = this.checked;" <?php echo ($form_data['education_radio'] ?? '') == 'edu_other' ? 'checked' : ''; ?> >
                    <label for="edu_other" class="form-check-label">Other:</label>
                    <input type="text" name="edu_other_textbox" id="edu_other_input" 
                        class="form-control d-inline-block" style="width: auto;" 
                        placeholder="Specify other education" title="Please enter alphabetic characters only (spaces allowed)." 
						value="<?php echo htmlspecialchars($form_data['edu_other_textbox'] ?? ''); ?>"
                        pattern="[A-Za-z\s]+">
                </div>
            </div>

            <div class="form-group">
                <label for="work_exp_radio">Working Experience:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="work_exp_radio" id="less" value="less" 
                        onclick="this.form.work_other_textbox.required = false;" <?php echo ($form_data['work_exp_radio'] ?? '') == 'less' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="less">&lt; 1 Year</label>
                </div>
                <div class="form-check form-check-inline">
                    <input id="more" class="form-check-input" type="radio" name="work_exp_radio" value="more" 
                        onclick="this.form.work_other_textbox.required = this.checked;"  <?php echo ($form_data['work_exp_radio'] ?? '') == 'more' ? 'checked' : ''; ?>>
                    <label for="more" class="form-check-label">&gt; 1 Year (Specify years):</label>
                    <input type="number" name="work_other_textbox" id="work_other_input" 
                        class="form-control d-inline-block" style="width: auto;" 
                        placeholder="Number of years" title="Please enter a valid number." min="1">
                </div>
            </div>
        </div>
        <div class="form-section">
            <h2>Company Information</h2>
            <div class="form-group">
                <label for="comp_add_textbox">Company Address:</label>
                <input id="comp_add_textbox" type="text" name="comp_add_textbox" class="form-control" required placeholder="Enter your Company Address"
                oninput="toUpperCase(this)" title="The address must within maximum length." maxlength="114"
				value="<?php echo htmlspecialchars($form_data['comp_add_textbox'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="comp_city_textbox">City:</label>
                <input id="comp_city_textbox" type="text" name="comp_city_textbox" class="form-control" required placeholder="Enter the City Name of your Company"
                oninput="toUpperCase(this)" title="The address must within maximum length." maxlength="35"
				value="<?php echo htmlspecialchars($form_data['comp_city_textbox'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="comp_post_textbox">Postcode:</label>
                <input id="comp_post_textbox" type="number" class="form-control" name="comp_post_textbox" required placeholder="Enter your Company Postcode. e.g 93350"
                    oninput="toUpperCase(this)" title="Please enter a 5-digit postcode." min="10000" max="99999" pattern="\d{5}" 
                    maxlength="5" value="<?php echo htmlspecialchars($form_data['comp_post_textbox'] ?? ''); ?>" />
            </div>

            <div class="form-group">
                <label for="comp_state_country_textbox">State/Country:</label>
                <input id="comp_state_country_textbox" type="text" class="form-control" name="comp_state_country_textbox" required placeholder="Enter your Company State or Country"
                oninput="toUpperCase(this)"  title="Please enter alphabetic characters only (spaces allowed)." 
                pattern="[A-Za-z\s]+" value="<?php echo htmlspecialchars($form_data['comp_state_country_textbox'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="comp_con_name_textbox">Contact Person (HR):</label>
                <input id="comp_con_name_textbox" type="text" name="comp_con_name_textbox" class="form-control" required placeholder="Enter the Name of your Company Contact Person"
                oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
                    pattern="[A-Za-z\s]+" maxlength="82" value="<?php echo htmlspecialchars($form_data['comp_con_name_textbox'] ?? ''); ?>">
            </div>
            
            <label>Contact Person's Phone Number: </label>
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="comp_con_num_code_textbox" class="text-secondary">Country Code (+):</label>
                    <input id="comp_con_num_code_textbox" type="text" class="form-control" name="comp_con_num_code_textbox" 
                    placeholder="Enter the country code (first 3 digits)" maxlength="3" title="Please enter exactly 3 digits" 
					value="<?php echo htmlspecialchars($form_data['comp_con_num_code_textbox'] ?? ''); ?>"/>
                </div>
                <div class="col-md-8">
                    <label for="comp_con_num_textbox" class="text-secondary">Phone Number:</label>
                    <input id="comp_con_num_textbox" type="text" class="form-control" name="comp_con_num_textbox" placeholder="Enter the rest of Phone Numbesr"
                    title="Please enter numbers only." inputmode="numeric" value="<?php echo htmlspecialchars($form_data['comp_con_num_textbox'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="comp_email_textbox">Contact Person's Email Address:</label>
                <input id="comp_email_textbox" type="email" class="form-control" name="comp_email_textbox" required placeholder="Enter the Email address of you Contact Person"
                    title="Please enter a valid email address, e.g., example@example.com." value="<?php echo htmlspecialchars($form_data['comp_email_textbox'] ?? ''); ?>">
            </div>
        </div>
        <div class="form-section">
            <h2>Examination Session Information</h2>
			<div class="alert alert-info mt-4">
				<p class="font-weight-bold mb-2">Terms & conditions:</p>
				<i><ul>
					<li>* Compulsory to fill up the information.</li>
					<li>** Exam link (online exam), result notification & e-certificate will be sent to the primary email address.</li>
					<li>Applicants will be notified of the exam result between three (3) to ten (10) working days from the examination date.</li>
					<li>E-Certificates will be sent to the successful applicant within a week from the date of the results notification.</li>
					<li>E-Certificates will only be issued after clearance of payment dues, if any.</li>
				</ul></i>
			</div>
            <div class="form-group">
                <label for="exam_type_radio">Exam Type:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="first" value="first" <?php echo ($form_data['exam_type_radio'] ?? '') == 'first' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="first">First time</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="retake_1" value="retake_1" 
					 <?php echo ($form_data['exam_type_radio'] ?? '') == 'retake_1' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="retake_1">1st Re-take</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="retake_2" value="retake_2"
					 <?php echo ($form_data['exam_type_radio'] ?? '') == 'retake_2' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="retake_2">2nd Re-take</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="retake_3" value="retake_3"
					 <?php echo ($form_data['exam_type_radio'] ?? '') == 'retake_3' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="retake_3">3rd Re-take</label>
                </div>
            </div>

            <div class="form-group">
                <label for="exam_mode_radio">Exam Mode:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_mode_radio" id="online" value="online"
					<?php echo ($form_data['exam_mode_radio'] ?? '') == 'online' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="online">Online</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_mode_radio" id="paper" value="paper"
					<?php echo ($form_data['exam_mode_radio'] ?? '') == 'paper' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="paper">Paper-based</label>
                </div>
            </div>

            <div class="form-group">
                <label for="exam_location_textbox">Examination Location/Center:</label>
                <input id="exam_location_textbox" type="text" name="exam_location_textbox" class="form-control" placeholder="Enter location of your exam"
                oninput="toUpperCase(this)" 
                title="Please enter alphabetic characters and commas (spaces allowed)."
				value="<?php echo htmlspecialchars($form_data['exam_location_textbox'] ?? ''); ?>"
                pattern="[A-Za-z\s,]+" maxlength="35">
            </div>

            <label>Exam Date</label>
            <div class="form-group row">
                <div class="col-md-4">
                    <label for="exam_date_day_textbox" class="text-secondary">Day:</label>
                    <input id="exam_date_day_textbox" type="number" class="form-control" name="exam_date_day_textbox" 
                        placeholder="Enter the DAY (01-31) of the exam" oninput="toUpperCase(this)" 
                        title="Please enter the day of the exam (01-31)." 
                        min="1" max="31" 
						value="<?php echo htmlspecialchars($form_data['exam_date_day_textbox'] ?? ''); ?>"
                        pattern="^(?:[1-9]|[12][0-9]|3[01])$" required>
                </div>
                <div class="col-md-4">
                    <label for="exam_date_month_textbox" class="text-secondary">Month:</label>
                    <input id="exam_date_month_textbox" type="number" class="form-control" name="exam_date_month_textbox" 
                        placeholder="Enter the MONTH (01-12) of the exam" oninput="toUpperCase(this)" 
                        title="Please enter the month of the exam (01-12)." 
                        min="1" max="12" 
						value="<?php echo htmlspecialchars($form_data['exam_date_month_textbox'] ?? ''); ?>"
                        pattern="^(?:1[0-2]|[1-9])$" required>
                </div>
                <div class="col-md-4">
                    <label for="exam_date_year_textbox" class="text-secondary">Year:</label>
                    <input id="exam_date_year_textbox" type="number" class="form-control" name="exam_date_year_textbox" 
                        placeholder="Enter the YEAR of the exam" oninput="toUpperCase(this)" 
                        title="Please enter the year of the exam in numeric format." 
						value="<?php echo htmlspecialchars($form_data['exam_date_year_textbox'] ?? ''); ?>"
                        pattern="^\d{4}$" min="0" max="9999" required>
                </div>
            </div>
        </div>  
		
        <div class="form-section">
            <h2>Request For Exam Time Extension</h2>
            <!-- Enhanced Note Section with Bootstrap -->
            <div class="alert alert-info mt-4">
                <p class="font-weight-bold mb-2">Note:</p>
                <i><ul>
                    <li>Candidates will only be entitled to additional time if both boxes are ticked ‘No’ below.</li>
                </ul></i>
            </div>
            <div class="form-group">
                <label for="lang_spoke_radio">Is English your primary/native spoken language?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lang_spoke_radio" value="lang_yes" id="lang_spoke_yes"
					<?php echo ($form_data['lang_spoke_radio'] ?? '') == 'lang_yes' ? 'checked' : ''; ?> >
                    <label class="form-check-label" for="lang_spoke_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input id="lang_spoke_no" class="form-check-input" type="radio" name="lang_spoke_radio" value="lang_no" 
					<?php echo ($form_data['lang_spoke_radio'] ?? '') == 'lang_no' ? 'checked' : ''; ?>
                        onclick="this.form.lang_spoke_no_textbox.required = this.checked;">
                    <label for="lang_spoke_no" class="form-check-label">No (Specify first language):</label>
                    <input type="text" class="form-control d-inline-block" style="width: auto;" name="lang_spoke_no_textbox" 
                        oninput="toUpperCase(this)" 
						value="<?php echo htmlspecialchars($form_data['lang_spoke_no_textbox'] ?? ''); ?>"
                         title="Please enter alphabetic characters only (spaces allowed)." 
                        pattern="[A-Za-z\s]+" id="lang_spoke_no_textbox">
                </div>
            </div>

            <div class="form-group">
                <label for="lang_writ_radio">Is English your primary written language?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lang_writ_radio" value="lang_writ_yes" id="lang_writ_yes"
					<?php echo ($form_data['lang_writ_radio'] ?? '') == 'lang_writ_yes' ? 'checked' : ''; ?>
                        onclick="this.form.lang_write_no_textbox.required = false;">
                    <label class="form-check-label" for="lang_writ_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input id="lang_writ_no" class="form-check-input" type="radio" name="lang_writ_radio" value="lang_writ_no" 
					<?php echo ($form_data['lang_writ_radio'] ?? '') == 'lang_writ_no' ? 'checked' : ''; ?>
                        onclick="this.form.lang_write_no_textbox.required = this.checked;">
                    <label for="lang_writ_no" class="form-check-label">No (Specify first language):</label>
                    <input type="text" class="form-control d-inline-block" style="width: auto;" name="lang_write_no_textbox" 
                        oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
						value="<?php echo htmlspecialchars($form_data['lang_write_no_textbox'] ?? ''); ?>"
                        pattern="[A-Za-z\s]+" id="lang_write_no_textbox">
                </div>
            </div>

        </div>

            
        <div class="form-section">
            <h2>Special Assistance</h2>
            <!-- Enhanced Note Section with Bootstrap -->
            <div class="alert alert-info mt-4">
                <p class="font-weight-bold mb-2">Note:</p>
                <i><ul>
                    <li>Applicant with special needs or physical disabilities may request for special assistance, within reason, in advance.</li>
                    <li>Special assistance requested will be subject to our approval and verification, and subject to constraints that may be within or outside our ability to address. Special assistance that could violate the integrity of the examination will not be entertained.</li>
                    <li>Request must be made at least three (3) days in advance from date of exam. We reserve the right to decline last minute requests.</li>
                </ul></i>
            </div>
            <div class="form-group">
                <label for="disability_textbox">Type of Special Needs/Physical Disability (if any):</label>
                <input type="text" class="form-control" id="disability_textbox" name="disability_textbox" placeholder="Enter your Disability e.g Deaf, Blind etc."
                oninput="toUpperCase(this)" 
				value="<?php echo htmlspecialchars($form_data['disability_textbox'] ?? ''); ?>"
                title="Please enter alphabetic characters and commas (spaces allowed)."
                pattern="[A-Za-z\s,]+" maxlength="35">
            </div>
            <div class="form-group">
                <label for="assistance_textbox">Assistance Required:</label>
                <input type="text" id="assistance_textbox" class="form-control" name="assistance_textbox" placeholder="Describe the assistance you required for your Disability."
                oninput="toUpperCase(this)" 
				value="<?php echo htmlspecialchars($form_data['assistance_textbox'] ?? ''); ?>"
                title="Please enter alphabetic characters and commas (spaces allowed)."
                pattern="[A-Za-z\s,]+" maxlength="82">
            </div>
        </div>

        <div class="form-section">
            <h2>Publishing Consent</h2>
            <div class="form-group">
                <label for="privacy_radio">Publishing of Successful Candidate's Name in MSTB Portal:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="privacy_radio" value="agree" id="privacy_agree"
					<?php echo ($form_data['privacy_radio'] ?? '') == 'agree' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="privacy_agree">I agree to my name being published in MSTB portal once I have passed the exam.</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="privacy_radio" value="no_agree" id="privacy_no_agree"
					<?php echo ($form_data['privacy_radio'] ?? '') == 'no_agree' ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="privacy_no_agree">I do not agree to my name being published in MSTB portal once I have passed the exam</label>
                </div>
            </div>
        </div>

        <div class="form-section">

        
            <h2>General Terms of Application and Code of Conduct</h2>
            <div class="alert alert-info" role="alert">
            <strong>By signing and submitting my application for certification, I consent to the general terms of application & Code of Conduct below:</strong><br><br>
            <p>With my application I have reviewed and understood the certification and examination information posted at 
                MSTB’s website of <a href="http://www.qportal.com.my" target="_blank">http://www.qportal.com.my</a>. 
                I have fully reviewed the information on the certification process, the requested qualifications and 
                the general terms of the certification. I agree that I am able to abide to the certification process as stated and that 
                I will notify MSTB on any arising change of circumstances that will affect my ability to do so, to enable timely remedial 
                arrangements prior to the exam.</p>
            
            <p>On request I shall provide further information to prove my qualification to sit the requested exam.</p>
        
            <p><strong>Code of Conduct:</strong> I agree that if and when awarded the requested certification, I shall:</p>
            <p><strong>a)</strong> comply with the relevant provisions and requirements of the ISTQB certification, as publicly stated on the MSTB website and as notified to me from time to time;</p>
            <p><strong>b)</strong> make claims regarding the ISTQB certification only with respect to the scope for which certification has been granted, as publicly stated on MSTB website and as notified to me from time to time;</p>
            <p><strong>c)</strong> not use the certification in such a manner as to bring the MSTB and/or ISTQB into disrepute, and that I shall not make any statement regarding the certification which the MSTB and/or may consider misleading or unauthorized;</p>
            <p><strong>d)</strong> discontinue the use of all claims to certification that contains any reference MSTB and/or ISTQB or certification upon suspension or withdrawal of certification, and to return any certificates issued by the board;</p>
            <p><strong>e)</strong> not use the certificate in a misleading manner and shall adhere to the conditions for use of the certification mark and/or logos;</p>
            <p><strong>f)</strong> On request I shall provide further information to prove my qualification to sit the requested;</p>
            <p><strong>g)</strong> Adhere to the ISTQB Code of Ethics as publicly stated on the ISTQB website and as notified to me from time to time.</p>
            </div>
<!-- 
            <div class="form-group">
                <label for="app_name_textbox">Name of Applicant:</label>
                <input type="text" class="form-control" id="app_name_textbox" name="app_name_textbox" required placeholder="Enter your Full Name"
                oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
                pattern="[A-Za-z\s]+" maxlength="82">
            </div>
            
            <div class="form-group signaturecontainer_main">
            <label for="signature_textbox" class="signature-label">Signature:</label>
                <div class="signature-container">
                    <canvas id="signature-pad" width="220" height="100"></canvas>
                </div>
                <br>
                <button type="button" id="clear-signature" class="btn btn-secondary mt-2  signature-clear-btn">Clear</button>
            </div> -->


<div class="form-group row">
    <label for="app_name_textbox" class="col-sm-3 col-form-label">Name of Applicant:</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" id="app_name_textbox" name="app_name_textbox" required placeholder="Enter your Full Name"
        oninput="toUpperCase(this)" title="Please enter alphabetic characters only (spaces allowed)." 
        pattern="[A-Za-z\s]+" maxlength="82">
    </div>
</div>

<div class="form-group row">
    <label for="signature_textbox" class="col-sm-3 col-form-label signature-label">Signature:</label>
    <div>
        <div class="signature-container col-sm-12">
            <canvas id="signature-pad" width="250" height="100"></canvas>
        </div>
        <button type="button" id="clear-signature" class="btn btn-secondary mt-2 signature-clear-btn col-sm-12">Clear</button>
    </div>
</div>





            <!-----------------------  SIGNATURE DRAW FUNCTION  ---------------------------------->
            <input type="hidden" id="signature_textbox" name="signature_textbox">
            <script>
                const canvas = document.getElementById('signature-pad');
                const clearButton = document.getElementById('clear-signature');
                const signatureInput = document.getElementById('signature_textbox');
                const ctx = canvas.getContext('2d');

                let drawing = false;

                // Start drawin
                canvas.addEventListener('mousedown', () => {
                    drawing = true;
                    ctx.beginPath();
                });

                // Drawing the signature
                canvas.addEventListener('mousemove', (event) => {
                    if (!drawing) return;
                    ctx.lineWidth = 2;
                    ctx.lineCap = 'round';
                    ctx.strokeStyle = 'black';
                    ctx.lineTo(event.offsetX, event.offsetY);
                    ctx.stroke();
                });

                // Stop drawing
                canvas.addEventListener('mouseup', () => {
                    drawing = false;
                });

                // Clear the canvas
                clearButton.addEventListener('click', () => {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                });

                // Convert signature to an image before submitting the form
                document.querySelector('form').addEventListener('submit', () => {
                    signatureInput.value = canvas.toDataURL(); // Convert canvas to a base64 image
                });
            </script>
            <!-----------------------  SIGNATURE DRAW FUNCTION  ---------------------------------->
            <div class="d-flex justify-content-center gap-3 mt-3">
                <button type="submit" class="btn btn-primary formsubmit">Submit</button>
                <button type="reset" class="btn btn-secondary formreset">Reset</button>
            </div>

            </div>
        </form>
    </div>

    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



