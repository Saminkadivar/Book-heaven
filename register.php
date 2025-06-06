<?php
require('header.php');
if (isset($_SESSION['USER_LOGIN'])) {
    echo "<script>window.top.location='index.php';</script>";
    exit;
}

$msg = $nameErr = $emailErr = $mobileErr = $passwordErr = "";
$nameTemp = $emailTemp = $mobileTemp = $passwordTemp = "";

if (isset($_POST['submit'])) {
    if (empty($_POST["name"])) {
        $nameErr = "Please enter a name";
    } else {
        $nameTemp = getSafeValue($con, $_POST['name']);
        if (preg_match("/^[a-zA-Z-' ]*$/", $nameTemp)) {
            $name = getSafeValue($con, $_POST['name']);
            if (empty($_POST["email"])) {
                $emailErr = "Please enter Email address";
            } else {
                $emailTemp = getSafeValue($con, $_POST['email']);
                if (filter_var($emailTemp, FILTER_VALIDATE_EMAIL)) {
                    $email = getSafeValue($con, $_POST['email']);
                    $mobile = getSafeValue($con, $_POST['mobile']);
                    if (empty($_POST["password"])) {
                        $passwordErr = "Please enter a password";
                    } else {
                        $passwordTemp = getSafeValue($con, $_POST['password']);
                    }
                    $password = md5($passwordTemp);
                    date_default_timezone_set('Asia/Kolkata');
                    $doj = date('Y-m-d H:i:s');
                    $check_user = mysqli_num_rows(mysqli_query($con, "SELECT * FROM users WHERE email='$email'"));
                    if ($check_user > 0) {
                        $msg = "Email ID already exists, please login";
                    } else {
                        $sql = "INSERT INTO users(name, email, mobile, password, doj) VALUES('$name', '$email', '$mobile', '$password', '$doj')";
                        if (mysqli_query($con, $sql)) {
                            echo "<script>window.top.location='SignIn.php';</script>";
                        } else {
                            $msg = "Error occurred!";
                        }
                    }
                } else {
                    $emailErr = "Please enter a valid Email address";
                }
            }
        } else {
            $nameErr = "Only letters and spaces allowed in Name";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Book Heaven</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
body {
    background-color: #f8f9fa;
}

.text-brown {
    color: #34495e;
    font-weight: 600;
}

.btn-brown {
    background-color: #34495e;
    color: white;
    border: none;
    padding: 10px;
    font-size: 16px;
}

.btn-brown:hover {
    background-color:rgb(62, 97, 132);
    color: white;

}

.card {
    background-color: white;
    border-radius: 100px;
    padding: 20px;
}

.input-group .form-control {
    border-right: none;
}

.input-group-text {
    background: white;
    border-left: none;
    color: #34495e;
}


</style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" style="border-radius: 10px; max-width: 400px;">
        <h2 class="text-center text-brown">Register</h2>

        <form method="post">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <div class="input-group">
                    <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <div class="input-group">
                    <input type="tel" name="mobile" class="form-control" placeholder="Enter mobile number" required>
                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
            </div>

            <div class="text-danger text-center mb-2">
                <?php echo $msg . $nameErr . $emailErr . $mobileErr; ?>
            </div>

            <button type="submit" name="submit" class="btn btn-brown w-100">Register</button>

            <div class="text-center mt-3">
                <p>Already have an account? <a href="SignIn.php" class="text-brown">Login Here</a></p>
            </div>
        </form>
    </div>
</div>

</body>
</html>
