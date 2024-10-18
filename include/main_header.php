
<head>
    <link rel="stylesheet" href="style/style.css">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'nothing'; ?></title>
</head>

<header class="main_header">
    <img src="image/logo.png" alt="Logo">
    
    <nav>
        <ul>
            <li><a href="main_menu.php" class="<?php echo isset($pageHomeActive) ? $pageHomeActive : 'nothing'; ?>">Home</a></li>
            <li><a href="classify.php" class="<?php echo isset($pageClassifyActive) ? $pageClassifyActive : 'nothing'; ?>">Plant Classification</a></li>
            <li><a href="tutorial.php" class="<?php echo isset($pageTutorialActive) ? $pageTutorialActive : 'nothing'; ?>">Tutorial</a></li>
            <li><a href="identify.php" class="<?php echo isset($pageIdentifyActive) ? $pageIdentifyActive : 'nothing'; ?>">Identify</a></li>
            <li><a href="contribute.php" class="<?php echo isset($pageContributeActive) ? $pageContributeActive : 'nothing'; ?>">Contribution</a></li> 
            <li class="dropdown">
                <a href="#" class="<?php echo isset($pageProfileActive) ? $pageProfileActive : 'nothing'; ?>">Profile</a>
                <div class="dropdown_menu">
                    <a href="view_profile.php" >My Profile</a>
                    <a href="logout.php">Logout</a>
                </div>
            </li>                  
        </ul>
    </nav>
</header>

<section class="<?php echo isset($pageHeaderClass) ? $pageHeaderClass : 'nothing'; ?>">
    <h1 class="header_title"><?php echo isset($pageHeaderTitle) ? $pageHeaderTitle : 'nothing'; ?></h1>
</section>


