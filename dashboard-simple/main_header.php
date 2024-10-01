<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
          crossorigin="anonymous">
    <link rel="stylesheet" href="style/style.css">
    <head>
    <title><?php echo isset($pageTitle) ? $pageTitle : 'My Website'; ?></title>
</head>
</head>

<body>

<!-- Header Content -->
<div class="container-fluid headerbootstrap p-0 m-0">
    <div class="row">
        <!-- Logo -->
        <div class="col-md-2 align-items-center d-flex p-0">
            <img src="image/logo.png" alt="Logo" class="logo img-fluid">
        </div>
        <!-- Navigation Bar -->
        <div class="col-md-10 align-items-center d-flex">
            <nav>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Services</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
