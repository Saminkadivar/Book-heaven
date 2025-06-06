<?php
include('header.php');
 // Start the session

if (isset($_SESSION['USER_LOGIN'])) {
    echo "<script>window.top.location='index.php';</script>";
    exit;
}

$msg = $passwordTemp = '';
if (isset($_POST['submit'])) {
    $email = getSafeValue($con, $_POST['email']);
    $passwordTemp = getSafeValue($con, $_POST['password']);
    $password = md5($passwordTemp);  // Hash the password before comparing

    // Query to fetch user from the database
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    
    $res = mysqli_query($con, $sql);
    
    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);  // Fetch the user record

        // Set session variables
        $_SESSION['USER_LOGIN'] = 'yes';  // Mark user as logged in
        $_SESSION['USER_ID'] = $row['id'];  // Store user ID
        $_SESSION['USER_NAME'] = $row['name'];  // Store user name
        $_SESSION['USER_EMAIL'] = $row['email'];  // Store user email in session

        // Redirect to the appropriate page
        if (isset($_SESSION['BeforeCheckoutLogin'])) {
            $checkoutAfterLogin = $_SESSION['BeforeCheckoutLogin'];
            echo "<script>window.top.location='$checkoutAfterLogin';</script>";
        } else {
            echo "<script>window.top.location='index.php';</script>";
            exit;
        }
    } else {
        $msg = "Invalid Username/Password";
    }
}
?>

<!-- Your login form HTML here -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Book Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .text-brown {
            color:#34495e;
            font-weight: 600;
        }
        .btn-brown {
            background-color:#34495e;
            color: white;
            border: none;
        }
        .btn-brown:hover {
            background-color:rgb(62, 97, 132);
            color: white;

        }
        .card {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
        }
        .input-group .form-control {
            border-right: none;
        }
        .input-group-text {
            background: white;
            border-left: none;
            color:#34495e;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" style="border-radius: 10px; max-width: 400px;">
        <h2 class="text-center text-brown">Sign In</h2>
        
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
            </div>

            <div class="text-danger text-center mb-2">
                <?php echo $msg ?>
            </div>

            <button type="submit" name="submit" class="btn btn-brown w-100">Login</button>

            <div class="text-center mt-3">
                <p>Don't have an account? <a href="register.php" class="text-brown">Sign Up Here</a></p>
                <p>Forgot Password <a href="Forgot_password.php" class="text-brown">Forgot</a></p>
            </div>
        </form>
    </div>
</div>

</body>  
</html>
