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
        $pageTitle = "Main Menu";
        $pageHeaderClass = "header_image_home";
        $pageHeaderTitle = "Main Menu";
        $pageHomeActive = "pageHomeActive";
        include 'include/admin_main_header.php';
    ?>

<!-- Main Content -->
    <main>
        
        <section class="main_menu_first_main">
            <h1>Welcome to the Herbarium Project</h1>
            <p>
                Join us in exploring and preserving plant biodiversity through herbarium specimens. 
                Our project offers resources to help you learn about plant classification, master preservation techniques, 
                and even contribute your discoveries.
                Here's what you can do on our platform:
                <ul>
                    <li>View summarized information about plant classification.</li>
                    <li>Learn how to preserve herbarium specimens in real life.</li>
                    <li>Identify plants using photos you upload.</li>
                    <li>Contribute your findings to support this project.</li>
                </ul>
            </p>
        </section>

        <section id="trulylearnmore" class="main_menu_second_main">
            <h1>Discover More</h1>
            <section class="main_menu_second_allcard">
                <section class="main_menu_second_card">
                    <div class="icon"><i class="fas fa-leaf"></i></div>
                    <h2>Plant Classification</h2>
                    <p>Learn about how plants are classified into families, genera, and species.</p>
                    <a href="classify.php" class="main_menu_second_card_button">Learn More</a>
                </section>

                <section class="main_menu_second_card">
                    <div class="icon"> <i class="fas fa-book"></i></div>
                    <h2>Tutorial</h2>
                    <p>Discover the process of creating herbarium specimens from fresh leaves.</p>
                    <a href="tutorial.php" class="main_menu_second_card_button">Get Started</a>
                </section>

                <section class="main_menu_second_card">
                    <div class="icon"><i class="fas fa-search"></i></div>
                    <h2>Identify</h2>
                    <p>Upload a photo to identify the plant type.</p>
                    <a href="identify.php" class="main_menu_second_card_button">Identify Now</a>
                </section>

                <section class="main_menu_second_card">
                    <div class="icon"><i class="fas fa-plus"></i></div>
                    <h2>Contribution</h2>
                    <p>Contribute your own data by uploading photos of fresh leaves.</p>
                    <a href="contribute.php" class="main_menu_second_card_button">Contribute</a>
                </section>
            </section>
        </section>

        <!-- https://www.weforum.org/agenda/2024/02/underground-tree-new-plant-species-biodiversity-loss/ -->
        <section class="main_menu_end_main">
            <h1>Did you know that:</h1>
            <h2>There are up to 400,000 plant species being documented and named up until 2024.</h2>
            <section class="main_menu_end_allcolumn">
                <section class="main_menu_end_column">
                    <!-- https://www.wallpaperflare.com/shallow-focus-photography-of-green-leaf-plant-spikes-macro-wallpaper-mtmyi/download/1920x1080 -->
                    <img src="image/main_img_1.jpg" alt="Undocumented Species">
                    <h3>Undocumented Species</h3>
                    <p>It's been estimated that there are 100,000 more plant species waiting to be documented and named.</p>
                </section>
                <section class="main_menu_end_column">
                    <!-- blob:https://in.pinterest.com/d5878c80-360c-4115-9b35-f2f89fe7bed2 -->
                    <img src="image/main_img_2.jpeg" alt="Annual Discoveries">
                    <h3>Annual Discoveries</h3>
                    <p>Every year, an average of 2500 new species of plants and fungi are being identified.</p>
                </section>
                <section class="main_menu_end_column">
                    <!-- https://emission-index.b-cdn.net/wp-content/uploads/2024/02/Beginning-of-transformation-in-agroforestry.png -->
                    <img src="image/main_img_3.png" alt="Biodiversity Loss">
                    <h3>Biodiversity Loss</h3>
                    <p>Biodiversity loss is the third biggest threat the world faces over the coming decade.</p>
                </section>
            </section>
            
        </section>


        <section class="main_menu_profile_main">
            <h1>View and Update Your Profile Information</h1>
            <a href="view_profile.php" class="updated_profile_button">View Profile</a>
        </section>

        <section class="main_menu_learnmore_main">

            <h4 class="main_menu_learnmore_button">Get Started Before It's Too Late! <a href="#trulylearnmore">Learn More</a></h4>

        </section>


    </main>

<!-- Footer -->
    <?php
        include 'include/footer.php';
    ?>

</body>
</html>



