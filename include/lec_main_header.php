
<head>
    <link rel="stylesheet" href="style/style.css">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'nothing'; ?></title>
</head>

<header class="main_header">
    <img src="image/logo.png" alt="Logo">
    
    <nav>
        <ul>
            <li><a href="lec_dashboard.php" class="<?php echo isset($pageHomeLecActive) ? $pageHomeLecActive : 'nothing'; ?>">Home</a></li>
            <li><a href="lec_overview_cert.php" class="<?php echo isset($pageCertLecActive) ? $pageCertLecActive : 'nothing'; ?>">Certification Overview</a></li>
            <li><a href="lec_overview_reg.php" class="<?php echo isset($pageRegLecActive) ? $pageRegLecActive : 'nothing'; ?>">Registration Overview</a></li> 
            <li class="dropdown">
                <a href="#" class="<?php echo isset($pageProfileActive) ? $pageProfileActive : 'nothing'; ?>">Profile</a>
                <div class="dropdown_menu">
                    <a href="lec_profile.php" >My Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>                  
        </ul>
    </nav>
</header>

<section class="<?php echo isset($pageHeaderClass) ? $pageHeaderClass : 'nothing'; ?>">
    <h1 class="header_title"><?php echo isset($pageHeaderTitle) ? $pageHeaderTitle : 'nothing'; ?></h1>
</section>


