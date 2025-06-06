<?php
require 'connection2.php';
require 'header.php';
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists in database
    $result = $mysqli->query("SELECT * FROM users WHERE email='$email'");
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['id'];
        $userName = $row['name'];

        // Generate secure token
        $token = bin2hex(random_bytes(50));
        $expiration = time() + 3600; // Token valid for 1 hour

        // Store token in database
        $mysqli->query("INSERT INTO password_resets (user_id, token, expiration) VALUES ('$userId', '$token', '$expiration')");

        // Send reset email
        if (sendPasswordResetEmail($email, $userName, $token)) {
            echo "Password reset link has been sent to your email.";
        } else {
            echo "Error sending email.";
        }
    } else {
        echo "Email not found.";
    }
}

// Function to send reset email
function sendPasswordResetEmail($userEmail, $userName, $token) {
    $mail = new PHPMailer(true);
    try {
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Google's SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'saminkadivar2911@gmail.com';  // Your Gmail email address
        $mail->Password = 'baeq zzsa ofmq mkop';  // Your Gmail email password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Email sender & recipient
        $mail->setFrom('your-email@gmail.com', 'Book Heaven');
        $mail->addAddress($userEmail, $userName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = '
        <html>
            <body>
                <h2>Password Reset Request</h2>
                <p>Hello ' . $userName . ',</p>
                <p>Click the link below to reset your password:</p>
                <a href="http://localhost/sk/reset_password.php?token=' . $token . '">Reset Password</a>
                <p>If you did not request a password reset, please ignore this email.</p>
                <p>Best regards,<br>Book Heaven Team</p>
            </body>
        </html>';

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
?>


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
            color:rgb(0, 0, 0);
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg p-4" style="border-radius: 10px; max-width: 400px;">
        <h3 class="text-center text-brown">Forgot Password</h3>
        
        <form method="post">
            <div class="mb-3">
        
                <label class="form-label">Email</label>
                <div class="input-group">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                </div>
            </div>
            
            <button type="submit" name="submit" class="btn btn-brown w-100">Send Reset Link</button>


            <div class="text-center mt-3">
                <p>Back to Login <a href="signin.php" class="text-brown">Login</a></p>
            </div>
        </form>
    </div>
</div>

</body>  
</html>
