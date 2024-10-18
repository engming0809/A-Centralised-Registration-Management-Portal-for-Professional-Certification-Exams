<?php



// If someone visited formFiller.php without having filled out the HTML form in index.php, stop rendering the page
if(empty($_POST)) {
    echo "<p>You've visited this page in error.</p>";
    exit();
}

// Debugging information, can be deleted
echo "<h1>POST Data</h1>";
echo "<div style='border-style: solid;'><pre>";
print_r($_POST);
echo "</pre></div>";

// Configuration
// Set location for FDF and PDF files
$outputLocation = "output/";
// Location of original PDF form
$pdfLocation = "template.pdf";


// Loop through the $_POST data, creating a new row in the FDF file for each key/value pair
$fdf = "";
foreach($_POST as $key => $value) {
    // If the user filled nothing in the field, like a text field, just skip it.
    // Note that if the PDF you provide already has text in it by default, doing this will leave the text as-is.
    // If you prefer to remove the text, you should remove the lines below so you overwrite the text with nothing.
    if($value == "") {
        continue;
    }

    // Figure out what kind of field it is by its name, which should be in the format name_fieldtype.

    // Textbox
    if(stringEndsWith($key, "_textbox")) {
        $key = str_replace("_textbox", "", $key);
        // Format:
        // << /V (Text) /T (Fieldname) >> 

        // Backslashes in the value are encoded as double backslashes
        $value = str_replace("\\", "\\\\", $value);
        // Parenthesis are encoded using \'s in front
        $value = str_replace("(", "\(", $value);
        $value = str_replace(")", "\)", $value);

        $fdf .= "<< /V (" . $value . ")" . " /T (" . $key . ") >>" . "\r\n";
    }

    // Checkbox
   else if(stringEndsWith($key, "_checkbox")) {
        $key = str_replace("_checkbox", "", $key);
        // Format:
        // << /V /On /T (Fieldname) >>

        // If the data was present in $_POST, that's because it was checked, so we can hardcode "/Yes" here
        $fdf .= "<< /V /Yes /T (" . $key . ") >>" . "\r\n";
    }

    // Radio Button
    else if(stringEndsWith($key, "_radio")) {
        $key = str_replace("_radio", "", $key);
        // Format:
        // << /V /Test#20Value /T (Fieldname) >>

        // Spaces are encoded as #20
        $value = str_replace(" ", "#20", $value);
        
        $fdf .= "<< /V /" . $value . " /T (" . $key . ") >>" . "\r\n";
    }

    // Dropdown
    else if(stringEndsWith($key, "_dropdown")) {
        $key = str_replace("_dropdown", "", $key);
        // Format:
        // << /V (Option 2) /T (Dropdown) >>

        $fdf .= "<< /V (" . $value . ") /T (" . $key . ") >>" . "\r\n";
    }
    
    // Unknown type
    else {
        echo "ERROR: We don't know what field type " . $key . " is, so we can't put it into the FDF file!";
    }
}


// Include the header and footer, then write the FDF data to a file
$fdf = getFDFHeader() . $fdf . getFDFFooter();

// Debugging information, can be deleted
echo "<h1>FDF Data</h1>";
echo "<div style='border-style: solid;'><pre>";
print_r(htmlspecialchars($fdf));
echo "</pre></div>";

// Dump FDF data to file
$timestamp = time();
$outputFDF = $outputLocation . $timestamp . ".fdf";
$outputPDF = $outputLocation . $timestamp . ".pdf";
file_put_contents($outputFDF, $fdf);

// Generate the PDF
// Format:
// exec("pdftk originalForm.pdf fill_form formData.fdf output filledFormWithData.pdf");
exec("pdftk " . $pdfLocation . " fill_form " . $outputFDF . " output " . $outputPDF);

echo "<p>Done! Your application will be reviewed shortly.</p>";
echo "<p>It is stored in: " . $outputPDF . "</p>";
echo "<p><br/><a href='/'>Home</a></p>";
echo "<iframe src='" . $outputPDF . "' width='100%' height='100%'></iframe>";
$servername = 'localhost';
$db   = 'cert_reg_management_db';
$user = 'root';
$pass = '';

// Create a connection
$conn = new mysqli($servername, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function insertRecord($outputPDF,$conn){//Function to insert the inputs from the user
    $sql = "INSERT INTO reg_registrationform (filepath) VALUES ('$outputPDF')";//SQL statement that inserts into the staff_table
    
    
    if (mysqli_query($conn,$sql)){
        echo "";
    }else{
        echo "ERROR: Could not execute SQL".mysqli_error($conn);
    }
}

function insertRecord2(,$conn){
    $sql2 = "INSERT INTO "
}
insertRecord($outputPDF,$conn);

/**
 * Simple "ends with" function, because PHP only included an endsWith() in 8.0
 * From: https://www.tutorialkart.com/php/php-check-if-string-ends-with-substring/
 */
function stringEndsWith($string, $endsWith) {
    if(substr_compare($string, $endsWith, -strlen($endsWith)) === 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Get the boilerplate header information for the FDF file
 */
function getFDFHeader() {
    $fdfHeader = "%FDF-1.2" . "\r\n";
    $fdfHeader .= "1 0 obj << /FDF << /Fields [" . "\r\n";
    return $fdfHeader;
}

/**
 * Get the boilerplate footer information for the FDF file
 */
function getFDFFooter() {
    $fdfFooter = "] >> >>" . "\r\n";
    $fdfFooter .= "endobj" . "\r\n";
    $fdfFooter .= "trailer" . "\r\n";
    $fdfFooter .= "<</Root 1 0 R>>" . "\r\n";
    $fdfFooter .= "%%EOF";
    
    return $fdfFooter;
}

?>