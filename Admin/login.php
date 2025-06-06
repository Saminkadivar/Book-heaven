<!-- <?php
// require('connection.php');
// require('function.php');
// $msg = '';
// if (isset($_POST['submit'])) {
//     $email = getSafeValue($con, $_POST['email']);
//     $password = md5(getSafeValue($con, $_POST['password']));
//     $sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
//     $res = mysqli_query($con, $sql);
//     // if (mysqli_num_rows($res) > 0) {
//     //     $_SESSION['ADMIN_LOGIN'] = 'yes';
//     //     $_SESSION['ADMIN_email'] = $email;
//     //     header('location:dashboard.php');
//     //     die();
//     // } else {
//     //     $msg = "Invalid Username/Password";
//     // }
//       // Verify password using password_hash() and password_verify()
//     //  if (mysqli_num_rows($res) > 0) {
//     //     $_SESSION['ADMIN_LOGIN'] = 'yes';
//     //     $_SESSION['ADMIN_EMAIL'] = $email;
//     //     $_SESSION['ADMIN_ROLE'] = $row['role']; // Store role in session    
    
//     //     header('location:dashboard.php');
//     //     die();
//     // } else {
//     //     $msg = "Invalid Username/Password";
//     // }



    
// }

?> -->

<?php
require('connection.php');
require('function.php');

$msg = '';

if (isset($_POST['submit'])) {
    $email = getSafeValue($con, $_POST['email']);
    $password = md5(getSafeValue($con, $_POST['password']));

    // Fetch admin details from database
    $sql = "SELECT * FROM admin WHERE email='$email'";
    $res = mysqli_query($con, $sql);

    if ($res && mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        
        // Verify the password
    if (mysqli_num_rows($res) > 0) {
            $_SESSION['ADMIN_LOGIN'] = 'yes';
            $_SESSION['ADMIN_EMAIL'] = $row['email'];
            $_SESSION['ADMIN_NAME'] = $row['name'];
            $_SESSION['ADMIN_ROLE'] = $row['role']; // Store role in session
            
            // Redirect based on role
            if ($row['role'] == 'Super Admin') {
                header('location: dashboard.php'); // Redirect Super Admin
            } else {
                header('location: dashboard.php'); // Redirect Regular Admin
            }
            exit();
        } else {
            $msg = "Invalid Password!";
        }
    } else {
        $msg = "Invalid Email!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin.css">

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
    background-color:#34495e;
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
         <h2 class="text-center text-brown">Admin</h1>
        <h3 class="text-center text-brown">Sign In</h3>
        
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

           
        </form>
    </div>
</div>

</body>
</html>

   