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
        session_start();

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

///////////////////////////////////////////////// Check if the certification name is provided//////////////////////////////
if (isset($_GET['cert_name'])) {
    $certName = $_GET['cert_name'];
    // Prepare the SQL statement to retrieve the ID
    $stmt = $conn->prepare("SELECT certification_id FROM certifications WHERE certification_name = ?");
    $stmt->bind_param("s", $certName); // Use "s" for string
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the certification exists
    if ($result->num_rows > 0) {
        // Fetch the certification ID
        $row = $result->fetch_assoc();
        $_SESSION['certification_id'] = $row['certification_id']; // Store the ID in the session
    } else {
        // Handle the case where the certification does not exist
        echo "";
    }
}

// Check if the session variables are set
if (isset($_SESSION['student_id']) && isset($_SESSION['certification_id'])) {
    // Retrieve the values from the session
    $studentId = $_SESSION['student_id'];
    $certificationId = $_SESSION['certification_id'];
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
        <form onsubmit="return validateForm()" action="formfiller.php" method="post">

            <div class="form-section">
                <h2>Certification Examination Type and Fee</h2>
                <label>Select Certification Type:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_radio" value="ctfl" required>
                    <label class="form-check-label">ISTQB Certified Tester Foundation Level (CTFL) - RM 900.00</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_radio" value="cpre">
                    <label class="form-check-label">IREB Certified Professional for Requirements Engineering Foundation Level (CPRE-FL) - RM 900.00</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_radio" value="agile">
                    <label class="form-check-label">IREB RE@Agile Primer - RM 900.00</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_radio" value="advanced">
                    <label class="form-check-label">IREB Advanced Level Requirements Elicitation - Practitioner - RM 1050.00</label>
                </div>
            </div>

            <div class="form-section">
                <h2>Mode/Terms of Payment</h2>
                <label>Mode of Payment:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_radio" value="company">
                    <label class="form-check-label">Company Sponsored</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_radio" value="cash_che">
                    <label class="form-check-label">Cash Cheque</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_radio" value="self">
                    <label class="form-check-label">Self-Sponsored</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_radio" value="bank_che">
                    <label class="form-check-label">Bankers' Cheque</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_radio" value="jom">
                    <label class="form-check-label">JomPAY</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_radio" value="credit">
                    <label class="form-check-label">Credit Card</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="payment_radio" value="online">
                    <label class="form-check-label">Online Payment</label>
                </div>
            </div>

            <div class="form-section">
                <h2>Personal Information</h2>
                <div class="form-group">
                    <label for="full_name_textbox">Full Name (as per IC / passport):</label>
                    <input type="text" class="form-control" name="full_name_textbox" required placeholder="Enter your Full Name"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>
                <div class="form-group">
                    <label for="nationality_textbox">Nationality:</label>
                    <input type="text" class="form-control" name="nationality_textbox" required placeholder="Enter your Nationality"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>
                <div class="form-group">
                    <label for="ic_num_textbox">New IC Number (Malaysian only/Date of Birth):</label>
<<<<<<< HEAD
                    <input type="text" class="form-control" name="ic_num_1_textbox" placeholder="Enter the first 6 digits of your IC Number"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                    <label for="ic_num_textbox">New IC Number (Malaysian only/State):</label>
                    <input type="text" class="form-control" name="ic_num_2_textbox" placeholder="Enter the next 2 digits of you IC Number"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                    <label for="ic_num_textbox">New IC Number (Malaysian only/Unique):</label>
                    <input type="text" class="form-control" name="ic_num_3_textbox" placeholder="Enter the last 4 digits of your IC Number"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
=======
                    <input type="text" class="form-control" name="ic_num_1_textbox">
                    <label for="ic_num_textbox">New IC Number (Malaysian only/State):</label>
                    <input type="text" class="form-control" name="ic_num_2_textbox">
                    <label for="ic_num_textbox">New IC Number (Malaysian only/Unique):</label>
                    <input type="text" class="form-control" name="ic_num_3_textbox">
>>>>>>> dd4d05ac17ba638c180156eafbb0e80b94621afc
                </div>
                <div class="form-group">
                    <label for="pass_num_textbox">Passport Number (Non-Malaysian only):</label>
                    <input type="text" class="form-control" name="pass_num_textbox" placeholder="Enter your Passport Number"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>
                <div class="form-group">
                    <label for="dob_textbox">Date of Birth (Day):</label>
<<<<<<< HEAD
                    <input type="text" class="form-control" name="dob_day_textbox" required placeholder="Enter the DAY (01-31) of your birth"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                    <label for="dob_textbox">Date of Birth (Month):</label>
                    <input type="text" class="form-control" name="dob_month_textbox" required placeholder="Enter the MONTH (01-12) of your birth"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                    <label for="dob_textbox">Date of Birth (Year):</label>
                    <input type="text" class="form-control" name="dob_year_textbox" required placeholder="Enter the YEAR of your birth"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
=======
                    <input type="text" class="form-control" name="dob_day_textbox" required>
                    <label for="dob_textbox">Date of Birth (Month):</label>
                    <input type="text" class="form-control" name="dob_month_textbox" required>
                    <label for="dob_textbox">Date of Birth (Year):</label>
                    <input type="text" class="form-control" name="dob_year_textbox" required>
>>>>>>> dd4d05ac17ba638c180156eafbb0e80b94621afc
                </div>
                <div class="form-group">
                    <label>Gender:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender_radio" value="male"> 
                        <label class="form-check-label">Male</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="gender_radio" value="female"> 
                        <label class="form-check-label">Female</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="pob_textbox">Place of Birth:</label>
                    <input type="text" class="form-control" name="pob_textbox" required placeholder="Enter your Place of Birth"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>
                <div class="form-group">
                    <label>Race:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="race_radio" value="malay"> 
                        <label class="form-check-label">Malay</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="race_radio" value="chinese"> 
                        <label class="form-check-label">Chinese</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="race_radio" value="indian"> 
                        <label class="form-check-label">Indian</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="race_radio" value="other"> 
                        <label class="form-check-label">Other:</label> 
                        <input type="text" class="form-control d-inline-block" style="width: auto;" name="race_other_textbox">
                    </div>
                </div>
                <div class="form-group">
                    <label for="company_name_textbox">Company Name:</label>
                    <input type="text" class="form-control" name="company_name_textbox" required placeholder="Enter your Company Name e.g SWINBURNE"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>
                <div class="form-group">
                    <label for="job_textbox">Job Title:</label>
                    <input type="text" class="form-control" name="job_textbox" required placeholder="Enter your Job Title e.g STUDENT OR LECTURER"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>
                <div class="form-group">
                    <label>Position Level:</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="position_level_radio" value="senior_man"> 
                        <label class="form-check-label">Senior Manager</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="position_level_radio" value="manager"> 
                        <label class="form-check-label">Manager</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="position_level_radio" value="senior_exec"> 
                        <label class="form-check-label">Senior Executive</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="position_level_radio" value="exec"> 
                        <label class="form-check-label">Executive</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="position_level_radio" value="intern"> 
                        <label class="form-check-label">Intern</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address_textbox">Correspondence Address:</label>
                    <input type="text" class="form-control" name="address_textbox" required placeholder="Enter the Address of where you currently stay"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>

                <div class="form-group">
                <label for="city_textbox">City:</label>
                <input type="text" class="form-control" name="city_textbox" required placeholder="Enter your City e.g KUCHING"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>

                <div class="form-group">
                <label for="postcode_textbox">Postcode:</label>
                <input type="text" class="form-control" name="postcode_textbox" required placeholder="Enter your Postcode e.g 93350"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>

                <div class="form-group">
                <label for="state_country_textbox">State/Country:</label>
                <input type="text" class="form-control" name="state_country_textbox" required placeholder="Enter your State or Country"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                </div>

                <div class="form-group">
                <label for="phone_textbox">Mobile Phone Number: +</label>
<<<<<<< HEAD
                <input type="text" class="form-control" name="phone_code_textbox" required placeholder="Enter the country code without the Plus (+) e.g 60"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                    <input type="text" class="form-control" name="phone_textbox" required placeholder="Enter the rest of your Phone Number"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
=======
                <input type="text" class="form-control" name="phone_code_textbox" required>
                    <input type="text" class="form-control" name="phone_textbox" required>
>>>>>>> dd4d05ac17ba638c180156eafbb0e80b94621afc
                </div>

                <div class="form-group">
                <label for="phone_alt_textbox">Alternate Phone Number: (compulsory for online exam) +</label>
<<<<<<< HEAD
                    <input type="text" class="form-control" name="phone_alt_code_textbox" placeholder="Enter the country code without the Plus (+) e.g 60"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                <input type="text" class="form-control" name="phone_alt_textbox" placeholder="Enter the rest of your Alternate Number"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
=======
                    <input type="text" class="form-control" name="phone_alt_code_textbox">
                <input type="text" class="form-control" name="phone_alt_textbox">
>>>>>>> dd4d05ac17ba638c180156eafbb0e80b94621afc
                </div>

            

                <div class="form-group">
                <label for="phone_work_textbox">Work Phone Number: +</label>
<<<<<<< HEAD
                    <input type="text" class="form-control" name="phone_work_code_textbox" placeholder="Enter the country code without the Plus (+) e.g 60"
                    oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                <input type="text" class="form-control" name="phone_work_textbox" placeholder="Enter the rest of your Work Number"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
=======
                    <input type="text" class="form-control" name="phone_work_code_textbox">
                <input type="text" class="form-control" name="phone_work_textbox">
>>>>>>> dd4d05ac17ba638c180156eafbb0e80b94621afc
                </div>

                <div class="form-group">
                <label for="email_pri_textbox">Primary Email Address:</label>
                <input type="text" class="form-control" name="email_pri_textbox" required placeholder="Enter your email address"
                title="Please enter a valid email address, e.g., example@example.com.">
                </div>

                <div class="form-group">
                <label for="email_sec_textbox">Alternate Email Address:</label>
                <input type="text" class="form-control" name="email_sec_textbox" placeholder="Enter your alternate email address"
                title="Please enter a valid email address, e.g., example@example.com.">
                </div>
            </div>
            <div class="form-section">
            <h2>Education Background</h2>
            <div class="form-group">
                <label for="education_radio">Highest Education Level:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="phd" value="phd">
                    <label class="form-check-label" for="phd">Doctorate (PhD)</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="masters" value="masters">
                    <label class="form-check-label" for="masters">Master's Degree</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="bachelor" value="bachelor">
                    <label class="form-check-label" for="bachelor">Bachelor's Degree</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="diploma" value="diploma">
                    <label class="form-check-label" for="diploma">Diploma</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="p_cert" value="p_cert">
                    <label class="form-check-label" for="p_cert">Professional Certificate</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="education_radio" id="edu_other" value="edu_other">
                    <label class="form-check-label" for="edu_other">Other:</label>
                    <input type="text" name="edu_other_textbox" class="form-control" placeholder="Specify other education">
                </div>
            </div>

            <div class="form-group">
                <label for="work_exp_radio">Working Experience:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="work_exp_radio" id="less" value="less">
                    <label class="form-check-label" for="less">&lt; 1 Year</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="work_exp_radio" id="more" value="more">
                    <label class="form-check-label" for="more">&gt; 1 Year (Specify years):</label>
                    <input type="number" name="work_other" class="form-control" placeholder="Number of years">
                </div>
            </div>
</div>
<div class="form-section">
            <h2>Company Information</h2>
            <div class="form-group">
                <label for="comp_add_textbox">Company Address:</label>
                <input type="text" name="comp_add_textbox" class="form-control" required placeholder="Enter your Company Address"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <div class="form-group">
                <label for="comp_city_textbox">City:</label>
                <input type="text" name="comp_city_textbox" class="form-control" required placeholder="Enter the City Name of your Company"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <div class="form-group">
                <label for="comp_post_textbox">Postcode:</label>
                <input type="text" name="comp_post_textbox" class="form-control" required placeholder="Enter your Company Postcode e.g 93350"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <div class="form-group">
                <label for="comp_state_country_textbox">State/Country:</label>
                <input type="text" name="comp_state_country_textbox" class="form-control" required placeholder="Enter your Company State or Country"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <div class="form-group">
                <label for="comp_con_name_textbox">Contact Person (HR):</label>
                <input type="text" name="comp_con_name_textbox" class="form-control" required placeholder="Enter your Company Contact Person"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <div class="form-group">
                <label for="comp_con_num_textbox">Contact Person's Phone Number: +</label>
<<<<<<< HEAD
                <input type="text" name="comp_con_num_code_textbox" class="form-control" placeholder="Enter the country code without the Plus (+) e.g 60"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                <input type="text" name="comp_con_num_textbox" class="form-control" placeholder="Enter the rest of the Phone Number"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
=======
                <input type="text" name="comp_con_num_code_textbox" class="form-control" required>
                <input type="text" name="comp_con_num_textbox" class="form-control" required>
>>>>>>> dd4d05ac17ba638c180156eafbb0e80b94621afc
            </div>

            <div class="form-group">
                <label for="comp_email_textbox">Contact Person's Email:</label>
                <input type="email" name="comp_email_textbox" class="form-control" required placeholder="Enter the Email address of you Contact Person"
                title="Please enter a valid email address, e.g., example@example.com.">
            </div>
</div>
<div class="form-section">
            <h2>Examination Session Information</h2>
            <div class="form-group">
                <label for="exam_type_radio">Exam Type:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="first" value="first">
                    <label class="form-check-label" for="first">First time</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="retake_1" value="retake_1">
                    <label class="form-check-label" for="retake_1">1st Re-take</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="retake_2" value="retake_2">
                    <label class="form-check-label" for="retake_2">2nd Re-take</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_type_radio" id="retake_3" value="retake_3">
                    <label class="form-check-label" for="retake_3">3rd Re-take</label>
                </div>
            </div>

            <div class="form-group">
                <label for="exam_mode_radio">Exam Mode:</label><br>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_mode_radio" id="online" value="online">
                    <label class="form-check-label" for="online">Online</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="exam_mode_radio" id="paper" value="paper">
                    <label class="form-check-label" for="paper">Paper-based</label>
                </div>
            </div>

            <div class="form-group">
                <label for="exam_location_textbox">Examination Location/Center:</label>
                <input type="text" name="exam_location_textbox" class="form-control" placeholder="Enter location of your exam"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <div class="form-group">
                <label for="exam_date_textbox">Exam Date (Day):</label>
                <input type="text" name="exam_date_day_textbox" class="form-control" required placeholder="Enter the DAY (01-31)"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                <label for="exam_date_textbox">Exam Date (Month):</label>
                <input type="text" name="exam_date_month_textbox" class="form-control" required placeholder="Enter the MONTH (01-12)"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
                <label for="exam_date_textbox">Exam Date (Year):</label>
                <input type="text" name="exam_date_year_textbox" class="form-control" required placeholder="Enter the YEAR"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>
</div>
<div class="form-section">
            <h2>Special Assistance</h2>
            <div class="form-group">
                <label for="disability_textbox">Type of Special Needs/Physical Disability (if any):</label>
                <input type="text" class="form-control" name="disability_textbox" placeholder="Enter your Disability e.g Deaf, Blind etc."
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <div class="form-group">
                <label for="assistance_textbox">Assistance Required:</label>
                <input type="text" class="form-control" name="assistance_textbox" placeholder="Enter YES or NO"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

    </div>
<div class="form-section">
            <h2>Language Information</h2>
            <div class="form-group">
                <label for="lang_spoke_radio">Is English your primary/native spoken language?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lang_spoke_radio" value="lang_yes" id="lang_spoke_yes">
                    <label class="form-check-label" for="lang_spoke_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lang_spoke_radio" value="lang_no" id="lang_spoke_no">
                    <label class="form-check-label" for="lang_spoke_no">No (Specify first language):</label>
                    <input type="text" class="form-control-inline" name="lang_no_spoke" style="width: auto; display: inline-block;">
                </div>
            </div>

            <div class="form-group">
                <label for="lang_writ_radio">Is English your primary written language?</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lang_writ_radio" value="lang_writ_yes" id="lang_writ_yes">
                    <label class="form-check-label" for="lang_writ_yes">Yes</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="lang_writ_radio" value="lang_writ_no" id="lang_writ_no">
                    <label class="form-check-label" for="lang_writ_no">No (Specify primary written language):</label>
                    <input type="text" class="form-control-inline" name="lang_no_writ" style="width: auto; display: inline-block;">
                </div>
            </div>

            </div>
<div class="form-section">
            <h2>Publishing Consent</h2>
            <div class="form-group">
                <label for="privacy_radio">Publishing of Successful Candidate's Name in MSTB Portal:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="privacy_radio" value="agree" id="privacy_agree">
                    <label class="form-check-label" for="privacy_agree">I agree</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="privacy_radio" value="no_agree" id="privacy_no_agree">
                    <label class="form-check-label" for="privacy_no_agree">I do not agree</label>
                </div>
            </div>

            </div>
<div class="form-section">
            <h2>Declaration</h2>
            <div class="form-group">
                <label for="app_name_textbox">Name of Applicant:</label>
                <input type="text" class="form-control" name="app_name_textbox" required placeholder="Enter your First Name and Last Name"
                oninput="toUpperCase(this)" title="Please enter in uppercase letters.">
            </div>

            <!-----------------------  SIGNATURE DRAW FUNCTION  ---------------------------------->
            <div class="form-group">
                <label for="signature_textbox">Signature:</label><br>
                <div class="signature-container">
                    <canvas id="signature-pad" width="220" height="100"></canvas>
                </div>
                <button type="button" id="clear-signature" class="btn btn-secondary mt-2">Clear</button>
            </div>
            <input type="hidden" id="signature_textbox" name="signature_textbox">
            <script>
                const canvas = document.getElementById('signature-pad');
                const clearButton = document.getElementById('clear-signature');
                const signatureInput = document.getElementById('signature_textbox');
                const ctx = canvas.getContext('2d');

                let drawing = false;

                // Start drawing
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


            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
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



