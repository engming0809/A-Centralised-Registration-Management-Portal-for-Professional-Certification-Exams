<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the registration logic here (e.g., input validation, database insertion)
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <main class="landing_main">
    <!-- Header  -->
    <?php
        $pageTitle = "Lecturer Registration";
        $pageHeaderTitle = "Lecturer Registration";
        include 'include/landing_header.php';
    ?>
    <!-- Main Content -->
    <section class="container registration_container">
        <!-- Form Section -->
        <section>
            <h2>Registration Form</h2>
            <form method="POST" action="lec_account_process_register.php">
                <div class="form-group row">
                    <div class="col-sm-12">
                        <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
                    </div>
                </div>

                <div class="registration_button_container">
                    <input type="reset" class="btn btn-primary" value="Reset">
                    <input type="submit" class="btn btn-primary" value="Submit">
                </div>
                <a href="lec_account_login.php" class="main_menu_link d-block mt-3">Back to Login</a>
            </form>
        </section>
    </section>

    </main>

    <!-- Footer -->
    <?php
    include 'include/footer.php';
    ?>
</body>
</html>
