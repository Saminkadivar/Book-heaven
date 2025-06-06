<?php
require('connection.php');
require('function.php');
if (isset($_SESSION['ADMIN_LOGIN']) && $_SESSION['ADMIN_LOGIN'] != ' ') {
} else {
    header('location:login.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body, html {
            background-color: rgb(255, 255, 255);
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            width: 250px;
            background-color: #34495e;
            color: #fff;
            text-align: left;
            padding: 20px;
            /* overflow-y: fixed; */
        }

        .sidebar .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .sidebar .logo img {
            max-width: 120px;
            border-radius: 50%;
        }

        .sidebar .nav-link {
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            display: flex;
            align-items: center;
            padding: 12px;
            transition: 0.3s;
            border-radius: 8px;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar .nav-link:hover {
            background-color: #2c3e50;
            color: #f1c40f;
        }

        .sidebar .nav-link.active {
            background-color: #f1c40f;
            color: #2c3e50;
        }

        .logout-btn {
            position: absolute;
            margin: 0;
            padding: 40px;
            bottom: 20px;
            width: 100%;
            text-align: left;
        }

        .logout-btn a {
            font-size: 16px;
            color: #fff;
            background-color: #e74c3c;
            padding: 10px 20px;
            border-radius: 8px;
            display: inline-block;
            text-decoration: none;
            transition: 0.3s;
        }

        .logout-btn a:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<nav class="sidebar">
    <div class="logo">
        <img src="../Img/logo1.png" alt="Logo">
    </div>
    <ul class="nav flex-column">
        <li><a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
        <li><a class="nav-link" href="categories.php"><i class="bi bi-list-check"></i> Categories</a></li>
        <li><a class="nav-link" href="books.php"><i class="bi bi-book"></i> Books List</a></li>
        <li><a class="nav-link" href="users.php"><i class="bi bi-people"></i> Users</a></li>
        <li><a class="nav-link" href="rental_returns.php"><i class="bi bi-arrow-repeat"></i> Rentals</a></li>
        <li><a class="nav-link" href="orders.php"><i class="bi bi-bag-check"></i> Orders</a></li>
        <li><a class="nav-link" href="feedback.php"><i class="bi bi-chat-dots"></i> Feedback</a></li>
        <li><a class="nav-link" href="admin_list.php"><i class="bi bi-shield-lock"></i> Admin</a></li>
        <li><a class="nav-link" href="report.php"><i class="bi bi-bar-chart"></i> Report</a></li>
        </ul>
    
    <div class="logout-btn">
        <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</nav>

<script src="js/mdb.min.js"></script>
</body>
</html>
