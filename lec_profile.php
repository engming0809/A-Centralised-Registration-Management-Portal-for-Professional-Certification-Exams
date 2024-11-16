<!DOCTYPE html>
<html lang="en">

<?php
// Start the session
session_start();

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['lecturer_full_name'])) {
    header("Location: index.php");
    exit();
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Lecturer Profile";
        $pageHeaderClass = "header_image_home_lec";
        $pageHeaderTitle = "My Profile";
        $pageProfileActive = "pageProfileActive";
        include 'include/lec_main_header.php';
    ?>

<!-- Main Content -->
    <main class="lec_profile">
        
    <!-- example -->
	<!-- Display session info -->
	<!-- example -->
		

    <section class="view_profile_main">
    <?php

    // Database credentials
    $servername = 'localhost';
    $db = 'cert_reg_management_db';
    $user = 'root';
    $pass = '';

    // Create connection
    $conn = new mysqli($servername, $user, $pass, $db);

    // Fetch lecturer information from the database
    $email = $_SESSION['lecturer_email'];
    $query = "SELECT full_name, email, profileimg, password, biography, status FROM Lecturer WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $lecturer_info = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='alert alert-danger'>Lecturer not found.</div>";
        exit();
    }

    // Assign lecturer information to variables
    $full_name = $lecturer_info['full_name'];
    $email = $lecturer_info['email'];
    $status = $lecturer_info['status'];
    $biography = $lecturer_info['biography'];
    $hashed_password = $lecturer_info['password'];

    // Handle the form submission for updating the name
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_name'])) {
        $new_full_name = $_POST['full_name'];
        $update_query = "UPDATE Lecturer SET full_name = '$new_full_name' WHERE email = '$email'";
        if (mysqli_query($conn, $update_query)) {
            $full_name = $new_full_name;  // Update the full name variable
            echo "<div class='alert alert-success'>Full name updated successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating full name.</div>";
        }
    }
    

    
    // Check if the lecturer has a profile image, otherwise use default
    $profile_image = !empty($lecturer_info['profileimg']) ? $lecturer_info['profileimg'] : 'image_user/default.jpg';// https://www.vecteezy.com/free-vector/default-profile-picture



// Password change logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    // Get the old and new passwords from the form
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    
    // Check if the old password is correct
    if (password_verify($old_password, $hashed_password)) {
        // Hash the new password
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update the password in the database
        $update_query = "UPDATE Lecturer SET password = '$new_hashed_password' WHERE email = '$email'";
        if (mysqli_query($conn, $update_query)) {
            // Set a session variable to indicate success
            $_SESSION['password_change_message'] = 'Password changed successfully!';
            // Redirect to the same page to prevent reloading the message
            header("Location: " . $_SERVER['PHP_SELF']);
            exit(); // Always call exit after header redirection
        } else {
            // Set error message
            $_SESSION['password_change_message'] = 'Error updating password.';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        // Set incorrect password error message
        $_SESSION['password_change_message'] = 'Password Does Not Match.';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Check and display message after redirection
if (isset($_SESSION['password_change_message'])) {
    // Display the message using JavaScript alert
    echo "<script>alert('" . $_SESSION['password_change_message'] . "');</script>";
    // Clear the message after it's shown
    unset($_SESSION['password_change_message']);
}
    
    ?>

    <!-- Main Content -->
        <h2>Lecturer Profile Information</h2>
    <!-- Profile Image Section -->
    <div class="profile-image-container">
    <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="view_profile_img">
    <button id="lecEditImageButton" class="btn btn-info btn-sm" data-toggle="modal" data-target="#uploadImageModal">
        <i class="fas fa-edit"></i> Edit
    </button>
</div>

<!-- Image Upload Modal -->
<div class="modal fade" id="uploadImageModal" tabindex="-1" role="dialog" aria-labelledby="uploadImageModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadImageModalLabel">Upload Profile Picture (Max file size: 10MB)<</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="uploadImageForm" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="profileImage">Choose Image</label>
                        <input type="file" class="form-control-file" id="profileImage" name="profile_image" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" form="uploadImageForm" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
    

    <table class="table">
    <!-- Full Name -->
    <tr>
        <th>Full Name:</th>
        <td id="fullNameDisplay"><?php echo htmlspecialchars($full_name); ?></td>
        <td>
            <button id="editButton" class="btn btn-info btn-sm" onclick="editFullName()">
                <i class="fas fa-edit"></i> Edit
            </button>
        </td>
    </tr>
    <tr id="editButtonsRow" style="display: none;">
        <td colspan="3" class="text-center">
            <button id="saveButton" class="btn btn-success btn-sm" onclick="saveFullName()">
                <i class="fas fa-check"></i> Save
            </button>
            <button id="cancelButton" class="btn btn-danger btn-sm" onclick="cancelEditFullName()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </td>
    </tr>

    <!-- Email -->
    <tr>
        <th>Email:</th>
        <td id="emailDisplay"><?php echo htmlspecialchars($email); ?></td>
        <td>
            <button id="emailEditButton" class="btn btn-info btn-sm" onclick="editEmail()">
                <i class="fas fa-edit"></i> Edit
            </button>
        </td>
    </tr>
    <tr id="emailEditButtonsRow" style="display: none;">
        <td colspan="3" class="text-center">
            <button id="saveButton" class="btn btn-success btn-sm" onclick="saveEmail()">
                <i class="fas fa-check"></i> Save
            </button>
            <button id="cancelButton" class="btn btn-danger btn-sm" onclick="cancelEditEmail()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </td>
    </tr>

    <tr>
        <th>Password</th>
        <td class="profilepasswordColumn">
    <a class="profilepasswordbtn btn btn-primary" href="#" data-toggle="modal" data-target="#changePasswordModal">
        <i class="fas fa-key"></i> Change Password
    </a>
</td>

    </tr>
    
    <!-- Biography -->
    <tr>
        <th>Biography:</th>
        <td id="bioDisplay"><?php echo htmlspecialchars($biography); ?></td>
        <td>
            <button id="bioEditButton" class="btn btn-info btn-sm" onclick="editBiography()">
                <i class="fas fa-edit"></i> Edit
            </button>
        </td>
    </tr>
    <tr id="bioEditButtonsRow" style="display: none;">
        <td colspan="3" class="text-center">
            <button id="saveButton" class="btn btn-success btn-sm" onclick="saveBiography()">
                <i class="fas fa-check"></i> Save
            </button>
            <button id="cancelButton" class="btn btn-danger btn-sm" onclick="cancelEditBiography()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </td>
    </tr>

</table>

</section>



<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="old_password">Old Password</label>
                            <input type="password" class="form-control" id="old_password" name="old_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>



<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>


<script>
var originalName; // Declare variables to hold original value
var originalEmail; 
var originalBiography; 


//////// Full Name
// Function to enable editing
function editFullName() {
    var fullNameDisplay = document.getElementById('fullNameDisplay');
    originalName = fullNameDisplay.innerText; 

    // Change the display to an input field with current value
    fullNameDisplay.innerHTML = '<input type="text" id="fullNameInput" value="' + originalName + '" class="form-control" />';

    // Hide the Edit button
    document.getElementById('editButton').style.display = 'none';

    // Show the Save and Cancel buttons row
    document.getElementById('editButtonsRow').style.display = 'table-row';
}

// Function to save 
function saveFullName() {
    var fullNameInput = document.getElementById('fullNameInput').value;

    // Make an AJAX request to save to the database
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "lec_profile_edit.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == 'success') {
                // Update the display area and hide the buttons
                document.getElementById('fullNameDisplay').innerText = fullNameInput;
                document.getElementById('editButton').style.display = 'inline-block';
                alert('Full name updated successfully!');

                // Hide the Save and Cancel buttons row
                document.getElementById('editButtonsRow').style.display = 'none';
            } else {
                alert('Error saving full name!');
            }
        }
    };
    xhr.send("full_name=" + fullNameInput); 
}

// Function to cancel the edit
function cancelEditFullName() {
    var fullNameDisplay = document.getElementById('fullNameDisplay');

    // Revert to the original display
    fullNameDisplay.innerText = originalName;  

    // Show the Edit button again and remove Save/Cancel buttons
    document.getElementById('editButton').style.display = 'inline-block';

    // Hide the Save and Cancel buttons row
    document.getElementById('editButtonsRow').style.display = 'none';
}



/////// Email
// Function to edit
function editEmail() {
    var emailDisplay = document.getElementById('emailDisplay');
    originalEmail = emailDisplay.innerText;  // Store the original 

    // Change the display to an input field with current value
    emailDisplay.innerHTML = '<input type="text" id="emailInput" value="' + originalEmail + '" class="form-control" />';

    // Hide the Edit button
    document.getElementById('emailEditButton').style.display = 'none';

    // Show the Save and Cancel buttons row
    document.getElementById('emailEditButtonsRow').style.display = 'table-row';
}

// Function to save 
function saveEmail() {
    var emailInput = document.getElementById('emailInput').value;

    // Make an AJAX request to save to the database
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "lec_profile_edit.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == 'success') {
                // Update the display area and hide the buttons
                document.getElementById('emailDisplay').innerText = emailInput;
                document.getElementById('emailEditButton').style.display = 'inline-block';
                alert('Email updated successfully!');

                // Hide the Save and Cancel buttons row
                document.getElementById('emailEditButtonsRow').style.display = 'none';
            } else {
                alert('Error saving email');
            }
        }
    };
    xhr.send("email=" + emailInput); 
}

// Function to cancel 
function cancelEditEmail() {
    var emailDisplay = document.getElementById('emailDisplay');

    // Revert to the original display
    emailDisplay.innerText = originalEmail; 

    // Show the Edit button again and remove Save/Cancel buttons
    document.getElementById('emailEditButton').style.display = 'inline-block';

    // Hide the Save and Cancel buttons row
    document.getElementById('emailEditButtonsRow').style.display = 'none';
}



/////// Biography
// Function to edit
function editBiography() {
    var bioDisplay = document.getElementById('bioDisplay');
    originalBiography = bioDisplay.innerText;  // Store the original 

    // Change the display to an input field with current value
    bioDisplay.innerHTML = '<input type="text" id="bioInput" value="' + originalBiography + '" class="form-control" />';

    // Hide the Edit button
    document.getElementById('bioEditButton').style.display = 'none';

    // Show the Save and Cancel buttons row
    document.getElementById('bioEditButtonsRow').style.display = 'table-row';
}

// Function to save 
function saveBiography() {
    var bioInput = document.getElementById('bioInput').value;

    // Make an AJAX request to save to the database
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "lec_profile_edit.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == 'success') {
                // Update the display area and hide the buttons
                document.getElementById('bioDisplay').innerText = bioInput;
                document.getElementById('bioEditButton').style.display = 'inline-block';
                alert('Biography updated successfully!');

                // Hide the Save and Cancel buttons row
                document.getElementById('bioEditButtonsRow').style.display = 'none';
            } else {
                alert('Error saving biography');
            }
        }
    };
    xhr.send("biography=" + bioInput); 
}

// Function to cancel 
function cancelEditBiography() {
    var bioDisplay = document.getElementById('bioDisplay');

    // Revert to the original display
    bioDisplay.innerText = originalBiography; 

    // Show the Edit button again and remove Save/Cancel buttons
    document.getElementById('bioEditButton').style.display = 'inline-block';

    // Hide the Save and Cancel buttons row
    document.getElementById('bioEditButtonsRow').style.display = 'none';
}


// Profile image
// JavaScript to handle the image upload (AJAX)
$(document).ready(function() {
    $('#uploadImageForm').on('submit', function(event) {
        event.preventDefault(); // Prevent the form from submitting the normal way

        var formData = new FormData(this);

        // Send the image to the server via AJAX
        $.ajax({
            url: 'upload_image_lec.php', // PHP script to handle the upload
            type: 'POST',
            data: formData,
            contentType: false, // Tell jQuery not to process the data
            processData: false, // Tell jQuery not to set content type
            success: function(response) {
                if (response == 'success') {
                    // Update the profile image on the page after successful upload
                    $('#uploadImageModal').modal('hide'); // Hide the modal
                    alert('Profile image updated successfully!');
                    location.reload(); // Reload the page to show the updated image
                } else {
                    alert('Error uploading image. Please try again.');
                }
            }
        });
    });
});
</script>

</body>
</html>






