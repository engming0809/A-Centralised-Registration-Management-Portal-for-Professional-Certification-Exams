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
        $pageTitle = "Student Dashboard";
        $pageHeaderClass = "header_image_home_stu";
        $pageHeaderTitle = "Student Dashboard";
        $pageHomeStuActive = "pageHomeStuActive";
        include 'include/stu_main_header.php';
    ?>

<!-- Main Content -->
    <main>
        
    <section class="main_menu_first_main">
        <h1>Welcome to the Certification Registration Portal</h1>
        <p>
            Join us in simplifying your journey toward professional certification exams. 
            Our platform provides essential resources to help you navigate the registration process, 
            manage payments, and access your exam results and certificates.
            Here's what you can do on our platform:
        </p>
        <ul>
            <li>Register for certification exams with an intuitive online form.</li>
            <li>Upload your payment transaction slips for easy processing.</li>
            <li>Receive notifications about your exam results and certificate availability.</li>
            <li>Access important documents like invoices and confirmation letters.</li>
            <li>View your exam schedule and important deadlines in one place.</li>
        </ul>
    </section>




    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



