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
        $pageHeaderTitle = "Profile";
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
            echo "<div class='alert alert-success'>Password changed successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error updating password.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Old password is incorrect.</div>";
    }
}

    
    ?>

    <!-- Main Content -->
    <h2>Lecturer Profile Information</h2>

    <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="view_profile_img">
    

    <table class="table">
    <tr>
        <th>Full Name</th>
        <td id="fullNameDisplay"><?php echo htmlspecialchars($full_name); ?></td>
        <td>
            <button id="editButton" class="btn btn-primary" onclick="editFullName()">
                <i class="fas fa-edit"></i> Edit
            </button>
        </td>
    </tr>
    <tr id="editButtonsRow" style="display: none;">
        <td colspan="3" class="text-center">
            <button id="saveButton" class="btn btn-success" onclick="saveFullName()">
                <i class="fas fa-check"></i> Save
            </button>
            <button id="cancelButton" class="btn btn-danger" onclick="cancelEdit()">
                <i class="fas fa-times"></i> Cancel
            </button>
        </td>
    </tr>

    <tr class="profilerow">
        <th>Email</th>
        <td><?php echo htmlspecialchars($email); ?></td>
    </tr>
    <tr>
        <th>Password</th>
        <td><a class="btn btn-primary" href="#" data-toggle="modal" data-target="#changePasswordModal">Change Password</a></td>
    </tr>
    <tr>
        <th>Biography</th>
        <td><?php echo htmlspecialchars($biography); ?></td>
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
var originalName; // Declare a variable to hold the original full name

// Function to enable editing the full name
function editFullName() {
    var fullNameDisplay = document.getElementById('fullNameDisplay');
    originalName = fullNameDisplay.innerText;  // Store the original name

    // Change the display to an input field with current full name
    fullNameDisplay.innerHTML = '<input type="text" id="fullNameInput" value="' + originalName + '" class="form-control" />';

    // Hide the Edit button
    document.getElementById('editButton').style.display = 'none';

    // Show the Save and Cancel buttons row
    document.getElementById('editButtonsRow').style.display = 'table-row';
}

// Function to save the full name after editing
function saveFullName() {
    var fullNameInput = document.getElementById('fullNameInput').value;

    // Make an AJAX request to save the updated name to the database
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "save_full_name.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText == 'success') {
                // Update the name in the display area and hide the buttons
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
    xhr.send("full_name=" + fullNameInput); // Send the edited name to the server
}

// Function to cancel the edit
function cancelEdit() {
    var fullNameDisplay = document.getElementById('fullNameDisplay');

    // Revert to the original full name display
    fullNameDisplay.innerText = originalName;  // Use the stored original name

    // Show the Edit button again and remove Save/Cancel buttons
    document.getElementById('editButton').style.display = 'inline-block';

    // Hide the Save and Cancel buttons row
    document.getElementById('editButtonsRow').style.display = 'none';
}
</script>

</body>
</html>






