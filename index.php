<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style/style.css">
</head>

<body>

<main class="landing_main">
<!-- Header  -->
    <?php
        $pageTitle = "Certification Registration Portal";
        $pageHeaderTitle = "Certification Registration Portal";
        include 'include/landing_header.php';
    ?>

<!-- Main Content -->
    <section class="landing_main_container container col-md-12 col-lg-10 mx-auto ">
        <div class="row align-items-center landingcontain">
            <div class="col-md-6 text-center p-0">
                <img src="image/main_land_container.jpg" alt="Dragon's Football" class="indeximage active img-fluid"> 
                <!-- https://www.google.com/url?sa=i&url=https%3A%2F%2Fprojectwidgets.com%2Fproject-management-certification%2F&psig=AOvVaw1xg4nG_dPKFSLD3X0nK_7Y&ust=1729269467750000&source=images&cd=vfe&opi=89978449&ved=0CBgQ3YkBahcKEwjw2rn07JWJAxUAAAAAHQAAAAAQPA -->
            </div>

            <!-- Description and Buttons -->
            <div class="col-md-6">
                <div class="description">
                    <h2>Welcome to the Centralised Registration Portal</h2>
                    <p>Choose your role below to manage your professional certification journey. Whether you're a student looking to register for exams or a lecturer overseeing the certification process, our platform simplifies every step. Log in or register to get started!</p>

                    <div class="home_button_container">
                        <button onclick="window.location.href='stu_account_login.php';" class="btn btn-primary">
                            <i class="fas fa-user-graduate"></i> Student
                        </button>
                        <button onclick="window.location.href='lec_account_login.php';" class="btn btn-primary">
                            <i class="fas fa-chalkboard-teacher"></i> Lecturer
                        </button>
                    </div>

                    <a href="main_menu.php" class="main_menu_link d-block">Go to Main Menu</a>
                </div>
            </div>
        </div>
    </section>
</main>


    <!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>
</body>
</html>
