
<head>
    <link rel="stylesheet" href="style/style.css">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'nothing'; ?></title>
</head>

<header class="main_header">
    <img src="image/logo.png" alt="Logo">
    
    <nav>
        <ul>
            <li><a href="admin_dashboard.php" class="<?php echo isset($pageAdminHomeActive) ? $pageAdminHomeActive : 'nothing'; ?>">Home</a></li>
            <li><a href="admin_request.php" class="<?php echo isset($pageAdminRegActive) ? $pageAdminRegActive : 'nothing'; ?>">Registration Management</a></li>
            <li><a href="admin_password.php" class="<?php echo isset($pageAdminPassActive) ? $pageAdminPassActive : 'nothing'; ?>">Password Management</a></li>
            <li class="dropdown">
                <a href="#" class="<?php echo isset($pageProfileActive) ? $pageProfileActive : 'nothing'; ?>">Profile</a>
                <div class="dropdown_menu">
                    <a href="admin_profile.php" >My Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>                  
        </ul>
    </nav>
</header>

<section class="<?php echo isset($pageHeaderClass) ? $pageHeaderClass : 'nothing'; ?>">
    <h1 class="header_title"><?php echo isset($pageHeaderTitle) ? $pageHeaderTitle : 'nothing'; ?></h1>
</section>


