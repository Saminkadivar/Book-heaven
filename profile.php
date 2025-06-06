<?php
require('header.php');
if (!isset($_SESSION['USER_LOGIN'])) {
    echo "<script>window.top.location='SignIn.php';</script>";
    exit;
}

$userId = $_SESSION['USER_ID'];
$res = mysqli_query($con, "SELECT * FROM users WHERE id='$userId'");
$row = mysqli_fetch_assoc($res);
$nameAuto = $row['name'];
$emailAuto = $row['email'];
$mobileAuto = $row['mobile'];
$passwordCheck = $row['password'];

$msg = '';
$nameErr = $emailErr = $mobileErr = $passwordErr = "";

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $passwordTemp = trim($_POST['password']);
    
    if ($passwordCheck == md5($passwordTemp)) {
        $sql = "UPDATE users SET name='$name', email='$email', mobile='$mobile' WHERE id='$userId'";
        if (mysqli_query($con, $sql)) {
            $msg = 'Updated Successfully → Changes will be visible next time you log in.';
        } else {
            $msg = "Error updating profile.";
        }
    } else {
        $msg = 'Please enter the correct password to update your details.';
    }
}
?>

<style>
/* Match the login page styles */
body {
    background-color: #f8f9fa;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;

}

.card {
    width: 400px;
    padding: 20px;
    background: #fff;
    align: center;

    border-radius: 10px;
    box-shadow: 0px 4px 8px #34495e;
}

.card h2 {
    text-align: center;
    color:#34495e;
    font-weight: bold;
}

.form-control {
    padding-left: 20px;
    color:#34495e;
    align-items:right;


}

.input-group-text {
    background: none;
    border: none;
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
      
        .input-group {
            border-right: none;
        }
        .input-group-text {
            background: white;
            border-left: none;
            color:#34495e;
        }
</style>

<div class="container-center">
    <div class="card">
        <h2>Edit Profile</h2>
        <div align="right">
        <form method="post" autocomplete="off">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" class="form-control" name="name" value="<?php echo $nameAuto ?>" required placeholder="Enter your name">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" class="form-control" name="email" value="<?php echo $emailAuto ?>" required placeholder="Enter your email">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                <input type="number" class="form-control" name="mobile" value="<?php echo $mobileAuto ?>" required placeholder="Enter your mobile">
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" class="form-control" name="password" required placeholder="Enter current password">
            </div>

            <p style="color: red; text-align: center;"><?php echo $msg ?></p>

            <button type="submit" name="submit" class="btn btn-brown w-100">Update</button>
        </form>
    </div>
</div>
