
<head>
    <link rel="stylesheet" href="style/style.css">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'nothing'; ?></title>
</head>

<header class="main_header">
<?php
         try {
            $host = '127.0.0.1';
            $db = 'cert_reg_management_db';
            $user = 'root';
            $pass = '';
    
            $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $countno="";
            // INITIALISE retrieve the query from database 
            $certStmt = $pdo->query("SELECT count(*) as noti FROM `certificationregistrations` WHERE registration_status in('invoice_submitted','receipt_submitted','examletter_submitted','certificate_submitted') and notification = 1");
            $countnotification = $certStmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($countnotification)){
                foreach ($countnotification as $key ) {
                    $countno= $key['noti'];
                }
            }
         
            
           
    
         } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
                exit();
         }
    ?>
    <img src="image/logo.png" alt="Logo">
    
    <nav>
        <ul>
            <li><a href="stu_dashboard.php" class="<?php echo isset($pageHomeStuActive) ? $pageHomeStuActive : 'nothing'; ?>">Home</a></li>
            <li><a href="stu_overview_cert.php" class="<?php echo isset($pageCertStuActive) ? $pageCertStuActive : 'nothing'; ?>">Available Certification</a></li>
            <li><a href="stu_overview_reg.php" class="<?php echo isset($pageRegStuActive) ? $pageRegStuActive : 'nothing'; ?>">My Registration<?php if ($countno > 0): ?>
        <span class="notification-badge"><?php echo $countno; ?></span>
    <?php endif; ?></a></li> 
            <li class="dropdown">
                <a href="#" class="<?php echo isset($pageProfileActive) ? $pageProfileActive : 'nothing'; ?>">Profile</a>
                <div class="dropdown_menu">
                    <a href="stu_profile.php" >My Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>                  
        </ul>
    </nav>
</header>

<section class="<?php echo isset($pageHeaderClass) ? $pageHeaderClass : 'nothing'; ?>">
    <h1 class="header_title"><?php echo isset($pageHeaderTitle) ? $pageHeaderTitle : 'nothing'; ?></h1>
</section>


