<!DOCTYPE html> 
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
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
    <main>
    <?php

    require_once('signature/fpdf.php');
    require_once('signature/FPDI-2.6.1/src/autoload.php');
    $signature = $_POST['signature_textbox']; // Base64 encoded image

    // Remove the data:image/png;base64, part
    $signature = str_replace('data:image/png;base64,', '', $signature);
    $signature = str_replace(' ', '+', $signature);

    // Decode the base64 image
    $signatureData = base64_decode($signature);

    // Save the signature as a PNG file
    $signatureFile = 'signature/signature.png';
    file_put_contents($signatureFile, $signatureData);


    // If someone visited formFiller.php without having filled out the HTML form in index.php, stop rendering the page
    if(empty($_POST)) {
        exit();
    }

    // Configuration
    // Set location for FDF and PDF files
    $outputLocation = "pdf_form_output/";
    // Location of original PDF form
    $pdfLocation = "pdf_form_output/template.pdf";

    // Loop through the $_POST data, creating a new row in the FDF file for each key/value pair
    $fdf = "";
    foreach($_POST as $key => $value) {
        // If the user filled nothing in the field, like a text field, just skip it.
        if($value == "") {
            continue;
        }

        // Figure out what kind of field it is by its name
        // Textbox
        if(stringEndsWith($key, "_textbox")) {
            $key = str_replace("_textbox", "", $key);
            $value = str_replace("\\", "\\\\", $value);
            $value = str_replace("(", "\(", $value);
            $value = str_replace(")", "\)", $value);
            $fdf .= "<< /V (" . $value . ")" . " /T (" . $key . ") >>" . "\r\n";
        }
        // Checkbox
        else if(stringEndsWith($key, "_checkbox")) {
            $key = str_replace("_checkbox", "", $key);
            $fdf .= "<< /V /Yes /T (" . $key . ") >>" . "\r\n";
        }
        // Radio Button
        else if(stringEndsWith($key, "_radio")) {
            $key = str_replace("_radio", "", $key);
            $value = str_replace(" ", "#20", $value);
            $fdf .= "<< /V /" . $value . " /T (" . $key . ") >>" . "\r\n";
        }
        // Dropdown
        else if(stringEndsWith($key, "_dropdown")) {
            $key = str_replace("_dropdown", "", $key);
            $fdf .= "<< /V (" . $value . ") /T (" . $key . ") >>" . "\r\n";
        }
        // Unknown type
        else {
            // You can uncomment the line below if you want to log errors without printing
            // error_log("ERROR: We don't know what field type " . $key . " is, so we can't put it into the FDF file!");
        }
    }

    // Include the header and footer, then write the FDF data to a file
    $fdf = getFDFHeader() . $fdf . getFDFFooter();

    // Dump FDF data to file
    $timestamp = time();
    $outputFDF = $outputLocation . $timestamp . ".fdf";
    $outputPDF = $outputLocation . $timestamp . ".pdf";
    file_put_contents($outputFDF, $fdf);

    // Generate the PDF
    exec("pdftk " . $pdfLocation . " fill_form " . $outputFDF . " output " . $outputPDF ." flatten ");

    


    // SIGNATURE 
    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->setSourceFile($outputPDF);
    for ($pageNo = 1; $pageNo <= 3; $pageNo++) {
        $pdf->AddPage(); // Add a new page for each imported template
        $template = $pdf->importPage($pageNo); // Import the current page
        $pdf->useTemplate($template); // Use the imported page template
    };
    //$template = $pdf->importPage(1);
    $pdf->useTemplate($template);
    $pdf->Image('signature/signature.png', 110, 140, 120, 15);
    $pdf->Output($outputPDF, "F");

    /**
     * Simple "ends with" function, because PHP only included an endsWith() in 8.0
     */
    function stringEndsWith($string, $endsWith) {
        return substr_compare($string, $endsWith, -strlen($endsWith)) === 0;
    }

    /**
     * Get the boilerplate header information for the FDF file
     */
    function getFDFHeader() {
        return "%FDF-1.2" . "\r\n" . "1 0 obj << /FDF << /Fields [" . "\r\n";
    }

    /**
     * Get the boilerplate footer information for the FDF file
     */
    function getFDFFooter() {
        return "] >> >>" . "\r\n" . "endobj" . "\r\n" . "trailer" . "\r\n" . "<</Root 1 0 R>>" . "\r\n" . "%%EOF";
    }

    ////////////////////////////////// Database connection ////////////////////////////////////////////////
    $servername = 'localhost';
    $db = 'cert_reg_management_db';
    $user = 'root';
    $pass = '';
    $conn = new mysqli($servername, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }



///////////////////////////////////////////////// Check if the certification name is provided//////////////////////////////
session_start();

// Check if the certification name is provided
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
        echo "Certification not found.";
        exit; // Exit the script if the certification is not found
    }
}



// To allow new form added
if (isset($_SESSION['student_id']) && isset($_SESSION['certification_id'])) {
    // Retrieve the values from the session
    $studentId = $_SESSION['student_id'];
    $certificationId = $_SESSION['certification_id'];
    $studentname = $_SESSION['student_full_name']; 

    // Step 1: Insert into CertificationRegistrations
    $stmt = $conn->prepare("INSERT INTO CertificationRegistrations (registration_status, student_id, certification_id) VALUES (?, ?, ?)");
    $status = 'form_submitted';
    $stmt->bind_param("sii", $status, $studentId, $certificationId);
    
    // After successfully processing the form and before the closing PHP tag
    if ($stmt->execute()) {
        // Step 2: Get the last inserted ID
        $registrationId = $conn->insert_id; // Get the ID of the newly inserted registration

        // Now insert into reg_registrationform with the last inserted ID
        $stmt = $conn->prepare("INSERT INTO reg_registrationform (filepath, registration_id) VALUES (?, ?)");
        $stmt->bind_param("si", $outputPDF, $registrationId);
        
        if ($stmt->execute()) {
            // Use JavaScript to show an alert and redirect
            echo "<script>alert('Form submitted successfully.'); window.location.href='stu_overview_cert.php';</script>";
            exit; // Ensure the script stops after this
        } else {
            echo "<script>alert('Error inserting record into reg_registrationform: " . $stmt->error . "'); window.location.href='stu_overview_cert.php';</script>";
            exit; // Ensure the script stops after this
        }
    } else {
        echo "<script>alert('Error inserting record into CertificationRegistrations: " . $stmt->error . "'); window.location.href='stu_overview_cert.php';</script>";
        exit; // Ensure the script stops after this
    }
    //To reupload form
} elseif(isset($_SESSION['form_id'])){
    $formID = $_SESSION['form_id'];
    
    $stmt = $conn->prepare("UPDATE reg_RegistrationForm SET filepath = ?, status = ? WHERE form_id = ?");
    $status = 'pending'; 
    $stmt->bind_param("ssi", $outputPDF, $status, $formID);

    // Execute the query and check if it's successful
    if ($stmt->execute()) {
        echo "<script>alert('Form Reupload successfully.'); window.location.href='stu_overview_cert.php';</script>";
        header("Location: stu_overview_reg.php");
        exit();
    } else {
        // Handle any errors (optional)
        echo "Error updating record: " . $stmt->error;
    }
}else {
    // Handle the case where session variables are not set
    echo "<p>No session information available.</p>";
}

// Close the database connection
$conn->close();

    ////////////////////////////////// Database connection ////////////////////////////////////////////////
    ?>


    <!-- Success message after form submission -->
    <section class="stu_overview_cert_formfiller">
    <div class="success-message">
        <p>Form submitted. Please return.</p>
    </div>
    </section>

    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>
