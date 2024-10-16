<!DOCTYPE html>
<html lang="en">
<!-- File Header Comments -->
<!-- Decsription: FYP : Add Certification -->
<!-- Author: Bongani Fuzwayo -->
<!-- Date: 15 October 2024 -->
<!-- Validated: 10 October 2022 -->
<!-- Start of head -->
<head>
	<meta charset="utf-8">
	<meta name="author" content="Bongani Fuzwayo">
	<meta name="description" content="Lecturer">
	<meta name="keywords" content="Add_cert">
	<!--<link rel="stylesheet" type="text/css" href="../style.css">
	<link href="https://fonts.googleapis.com/css2?family=Audiowide&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Aldrich&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Quantico&display=swap" rel="stylesheet"> -->
	<title>Add New Certification</title>
</head>
	<body>
<?php
$cert_nameErr = $descriptionErr = $requirementsErr = $scheduleErr = $costErr = "";//Empty error variables
$cert_name = $description = $requirements = $schedule = $cost = "";//Empty input variables


if ($_SERVER["REQUEST_METHOD"] == "POST") {//Input validation process
  if (empty($_POST["cert_name"])) {
    $cert_nameErr = "<p style='color:red;'>A name of this certification is required</p>";
  } else {
    $cert_name = $_POST["cert_name"];

}
  
if (empty($_POST["description"])) {// Validation process
	$descriptionErr = "<p style='color:red;'>A description is required</p>";
	}else{
		$description = $_POST["description"];
	
	}
		

  
  if (empty($_POST["requirements"])) {//Email validation process
    $requirementsErr = "<p style='color:red;'>This field is required</p>";
  } else {
    $requirements = $_POST["requirements"];

}
if (empty($_POST["schedule"])) {// Validation process
	$scheduleErr = "<p style='color:red;'>A schedule is required</p>";
	}else{
		$schedule = $_POST["schedule"];
	
	}

    if (empty($_POST["cost"])) {// Validation process
        $costErr = "<p style='color:red;'>A cost is required</p>";
        }else{
            $cost = $_POST["cost"];
        
        }
}
if (!empty($_POST["cert_name"]) && !empty($_POST["description"])) {
	$record = "<p style='color:green;'>Record is saved</p>";
    $overview ="<a href='CertOver.php'>Back To Overview</a>"; //Message if all inputs are valid
}else{
	$record = "";
    $overview = "";
}
if (!empty($_POST["cert_name"]) && !empty($_POST["description"]) ){
	$conn=connectDB();
	insertRecord($cert_name,$description,$requirements,$schedule,$cost,$conn);//Inserts the input from the user
}

function insertRecord($cert_name,$description,$requirements,$schedule,$cost,$conn){//Function to insert the inputs from the user
	$sql = "INSERT INTO certifications (certification_name,description,requirements,schedule,cost) VALUES ('$cert_name','$description','$requirements','$schedule','$cost')";//SQL statement that inserts into the staff_table
	
	
	if (mysqli_query($conn,$sql)){
		echo "";
	}else{
		echo "ERROR: Could not execute SQL".mysqli_error($conn);
	}
}

function connectDB(){//Function to connect to database
	$servername = "localhost";
	$username = "root";
	$password = "";
	$db= "cert_reg_management_db";
	
	$conn = mysqli_connect($servername,$username,$password,$db);
	
	if(!$conn){
		die('Connection Failed: '.mysqli_connect_error());
	}
	return $conn;
}
 ?>
	<h1>Add New Certification</h1>
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<fieldset>
			<legend>Information</legend>
				<p style="color:red;"><span class="error">* required field</span></p>
					<label for="cert_name">Certification Name: <input type="text" id="cert_name" name="cert_name"><span style="color:red;" class="error">* <?php echo $cert_nameErr;?></span></label><br>
					<label for="description">Description: <input type="text" id="description" name="description"><span style="color:red;" class="error">* <?php echo $descriptionErr;?></span></label><br>
					<label for="requirements">Requirement: <input type="text" id="requirements" name="requirements"><span style="color:red;" class="error">* <?php echo $requirementsErr;?></span></label><br>
					<label for="schedule">Schedule Date: <input type="date" id="schedule" name="schedule"><span style="color:red;" class="error">* <?php echo $scheduleErr;?></span></label><br>
					<label for="cost">Cost: <input type="text" id="cost" name="cost"><span style="color:red;" class="error">* <?php echo $costErr;?></span></label><br>	
	
			<p><input type="submit" name="confirm" value="Add Certification"></p>
			<?= $record ;
            echo $overview?>
		</fieldset>
	</form>
	</body>
</html>