<?php
require('connection.php');
require('function.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Icon -->
    <link rel="shortcut icon" href="Img/icon1.png" type="image/x-icon" />
    <!-- Default CSS -->
    <link rel="stylesheet" href="css/Style.css" />
    <link rel="stylesheet" href="css/responsive.css" />
    <!-- Bootstrap -->
    <link id="theme" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome Fonts-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" />
    <!-- Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Default JS-->
    <script src="js/script.js"></script>
    <title>Home | Book Heaven</title>
    <style>
    /* Apply the new theme color */
    .navbar {
        background-color: #34495e !important;
    }
    
    /* Navbar links */
    .navbar-nav .nav-link {
        color: white !important;
        transition: color 0.3s ease;
    }

    .navbar-nav .nav-link:hover {
        color: #f1c40f !important; /* Yellow highlight on hover */
    }

    /* Dropdown menu */
    .dropdown-menu {
        background-color: #34495e !important; /* Darker shade */
    }

    .dropdown-menu a {
        color: white !important;
    }

     .dropdown-menu a:hover {
        background-color: #34495e !important; 
        color: #f1c40f !important;
    } 

    /* Search bar */
    .search-btn {
        background-color: #34495e !important; /* Teal button */
        border: none;
    }

    .search-btn:hover {
        background-color: #f1c40f !important; /* Darker teal */
    }

    /* Login button */
    .login-btn {
        background-color: #e74c3c !important; /* Red login button */
        border: none;
    }

    .login-btn:hover {
        background-color: #c0392b !important; /* Darker red */
    }
</style>

</head>

<body>
    <section id="#navbar">
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow lh-1">
            <a class="navbar-brand img-fluid ms-2" href="index.php">
                <img src="Img/icon1.png" alt="logo" height="50vw" /></a>
            <button class="navbar-toggler" title="Menu" type="button" data-bs-toggle="collapse"
                data-bs-target="#mynavbar">
                <span style="font-size: 1.8465rem; color: #fff">
                    <i class="fas fa-bars"></i>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="mynavbar">
                <ul class="navbar-nav navbar me-auto ">
                    <li class="nav-item">
                        <a id="#home" class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link " href="bookCategory.php"> <i class="fas fa-book-open"></i>	Book Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contactUs.php"><i class="fas fa-envelope"></i> Contact Us</a>
                    </li>
                    
                    <?php
                    if (isset($_SESSION['USER_LOGIN'])) {
                        echo '<li class="nav-item">
                        <a class="nav-link" href="myOrder.php"><i class="fas fa-shopping-cart book-icon"></i> My Orders</a>
                </li>';
                    }
                    ?>

                </ul>
                <form method="get" action="search.php" class="d-flex" id="searchBar">
                    <input class="form-control" type="text" name="search" placeholder="Search by Title or Author..." />
                    <button title="Search" class="btn text-white search-btn me-1" type="submit" name="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div class=" navbar-nav navbar ">
                    <?php
                    if (isset($_SESSION['USER_LOGIN'])) {
                        $userName = $_SESSION['USER_NAME'];
                        echo '<ul class="navbar-nav navb me-4">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
                                >' . $userName . '</a>
                                <ul class="dropdown-menu align=left">
                            
                                   <li>
                                        <a class="dropdown-item-text text-white-50 text-decoration-none" href="profile.php" >Edit Profile</a>
                                    </li>
                                    <hr class="bg-white m-2">
                                    <li><a class="dropdown-item-text text-white-50 text-decoration-none" href="logout.php">Logout</a></li>
                                </ul>
                            </li>
                    
                        </ul>';
                    } else {
                        echo '<a class="text-decoration-none me-1 ms-3 text-white  btn btn-outline-light me-2 login-btn"
                           role="button" href="SignIn.php"> Login</a>';
                    }
                    ?>
                </div>
            </div>
        </nav>
    </section>
    <br>
    <br>