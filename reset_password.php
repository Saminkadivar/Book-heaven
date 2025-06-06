<?php
require 'connection2.php'; // Ensure your database connection is included
require 'header.php';

if (!isset($_GET['token'])) {
    die("Invalid token.");
}

$token = $_GET['token'];

// Check if token exists in the database and is not expired
$result = $mysqli->query("SELECT * FROM password_resets WHERE token='$token' AND expiration > " . time());

if (!$result) {
    die("SQL error: " . $mysqli->error);
}

if ($result->num_rows == 0) {
    die("Invalid or expired token.");
}

$row = $result->fetch_assoc();
$userId = $row['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashedPassword = md5($newPassword); // Hashing the password

        // Update password
        $mysqli->query("UPDATE users SET password='$hashedPassword' WHERE id='$userId'");

        // Delete reset token after successful reset
        $mysqli->query("DELETE FROM password_resets WHERE token='$token'");

        echo "<script>alert('Password reset successfully!'); window.location.href='signin.php';</script>";
    }
}
?>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" style="border-radius: 10px; max-width: 400px;">
        <h3 class="text-center text-brown">Reset Password</h3>
        
        <form method="post" onsubmit="return validatePassword();">
            <div class="mb-3">
                <label class="form-label">New Password:</label>
                <div class="input-group">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your new password" required>
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password:</label>
                <div class="input-group">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                </div>
            </div>

            <button type="submit" class="btn btn-brown w-100">Reset Password</button>

            <div class="text-center mt-3">
                <p>Back to Login <a href="signin.php" class="text-brown">Login</a></p>
            </div>
        </form>
    </div>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
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

    <script>
        function validatePassword() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
</body>  
</html>
