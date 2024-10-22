<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>

<body>
<!-- Header  -->
    <?php
        $pageTitle = "Admin Dashboard";
        $pageHeaderClass = "header_image_admin_main";
        $pageHeaderTitle = "Admin Dashboard";
        $pageAdminHomeActive = "pageAdminHomeActive";
        include 'include/admin_main_header.php';
    ?>

<!-- Main Content -->
    <main>
        
        <section class="main_menu_first_main">
            <h1>Welcome to the Certification Management System</h1>
            <p>
                 
                Here’s what you can do on our admin dashboard:
            </p>
            <ul>
                <li>Validate lecturer registration.</li>
                <li>XX.</li>
            </ul>
        </section>
    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



