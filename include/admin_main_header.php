
<head>
    <link rel="stylesheet" href="style/style.css">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'nothing'; ?></title>
</head>

<header class="main_header">
    <img src="image/logo.png" alt="Logo">
    
    <nav>
        <ul>
            <li><a href="admin_dashboard.php" class="<?php echo isset($pageAdminHomeActive) ? $pageAdminHomeActive : 'nothing'; ?>">Home</a></li>
            <li><a href="admin_request.php" class="<?php echo isset($pageAdminRegActive) ? $pageAdminRegActive : 'nothing'; ?>">Lecturer Verification</a></li>
            <li class="dropdown">
                <a href="#" class="<?php echo isset($pageAdminPassActive) ? $pageAdminPassActive : 'nothing'; ?>">Account Management</a>
                <div class="dropdown_menu_admin">
                    <a href="admin_management_stuacc.php">Student</a>
                    <a href="admin_management_lecacc.php" >Lecturer</a>
                </div>
            </li>     
            <li><a href="logout.php">Logout</a></li>             
        </ul>
    </nav>
</header>

<section class="<?php echo isset($pageHeaderClass) ? $pageHeaderClass : 'nothing'; ?>">
    <h1 class="header_title"><?php echo isset($pageHeaderTitle) ? $pageHeaderTitle : 'nothing'; ?></h1>
</section>


