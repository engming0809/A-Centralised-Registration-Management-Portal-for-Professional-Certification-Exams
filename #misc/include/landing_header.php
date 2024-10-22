<head>
    <link rel="stylesheet" href="style/style.css">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Main'; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css">
</head>



<header class="landing_header">
    <nav class="d-flex justify-content-between align-items-center">
            <a href="index.php">
                <img src="image/logo.png" alt="Logo">
            </a>
        <h1 class="mx-auto"><?php echo isset($pageHeaderTitle) ? $pageHeaderTitle : 'nothing'; ?></h1> 
    </nav>
</header>