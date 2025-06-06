<?php
session_start();
include('connection.php');
require '../vendor/autoload.php';        

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['ADMIN_LOGIN'])) {
    die("<script>alert('Unauthorized access. Please log in as admin.'); window.location='login.php';</script>");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("<script>alert('Invalid request method.'); window.location='rental_returns.php';</script>");
}

// Retrieve data
$return_id = intval($_POST['return_id']);
$action = $_POST['action'];
$condition_status = $_POST['condition_status'];
$adjusted_refund = floatval($_POST['adjusted_refund']);

// Fetch return request details with user email
$query = "SELECT rr.*, u.email, b.name AS book_name, b.author 
          FROM rental_returns rr 
          JOIN users u ON rr.user_id = u.id 
          JOIN books b ON rr.book_id = b.id 
          WHERE rr.id = '$return_id'";
$result = mysqli_query($con, $query);
$return_data = mysqli_fetch_assoc($result);

if (!$return_data) {
    die("<script>alert('Return request not found!'); window.location='rental_returns.php';</script>");
}

$order_id = $return_data['order_id'];
$book_id = $return_data['book_id'];
$user_email = $return_data['email'];
$book_name = $return_data['book_name'];
$book_author = $return_data['author'];

mysqli_begin_transaction($con);

try {
    if ($action == 'approve') {
        $update_return = "UPDATE rental_returns SET security_deposit_refund = '$adjusted_refund', 
                          condition_status = '$condition_status', status = 'Approved' 
                          WHERE id = '$return_id'";
        if (!mysqli_query($con, $update_return)) {
            throw new Exception("Failed to update return status: " . mysqli_error($con));
        }

        if ($condition_status != 'Lost') {
            $update_book = "UPDATE books SET qty = qty + 1 WHERE id = '$book_id'";
            if (!mysqli_query($con, $update_book)) {
                throw new Exception("Failed to update book stock: " . mysqli_error($con));
            }
        }

        $status_query = mysqli_query($con, "SELECT id FROM order_status WHERE status_name = 'Returned'");
        $status_row = mysqli_fetch_assoc($status_query);
        $returned_status_id = $status_row['id'];

        $update_order = "UPDATE orders SET order_status = '$returned_status_id' WHERE id = '$order_id'";
        if (!mysqli_query($con, $update_order)) {
            throw new Exception("Failed to update order status: " . mysqli_error($con));
        }

        mysqli_commit($con);

        // Send email notification
        sendMail($user_email, "Return Request Approved", $order_id, $book_name, $book_author, $adjusted_refund, $condition_status, "Approved");

        echo "<script>alert('Return request approved and email sent!'); window.location='rental_returns.php';</script>";

    } elseif ($action == 'reject') {
        $update_return = "UPDATE rental_returns SET status = 'Rejected' WHERE id = '$return_id'";
        if (!mysqli_query($con, $update_return)) {
            throw new Exception("Failed to update return status: " . mysqli_error($con));
        }

        mysqli_commit($con);

        // Send email notification
        sendMail($user_email, "Return Request Rejected", $order_id, $book_name, $book_author, $adjusted_refund, $condition_status, "Rejected");

        echo "<script>alert('Return request rejected and email sent!'); window.location='rental_returns.php';</script>";
    }

} catch (Exception $e) {
    mysqli_rollback($con);
    die("<script>alert('Error: " . $e->getMessage() . "'); window.location='rental_returns.php';</script>");
}

mysqli_close($con);

// Function to send email using PHPMailer
function sendMail($to, $subject, $order_id, $book_name, $book_author, $adjusted_refund, $condition_status, $action) {
    $mail = new PHPMailer(true);
    
    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Google's SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'saminkadivar2911@gmail.com';  // Your Gmail email address
        $mail->Password = 'baeq zzsa ofmq mkop';  // Your Gmail email password or app-specific password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; 

        // Email Headers
        $mail->setFrom('no-reply@bookheaven.com', 'Book Heaven');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->isHTML(true);

        // Email Message with HTML
        $message = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; background-color: #f4f4f4; padding: 20px; }
                    .container { max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px #ccc; }
                    .header { background: #34495e; color: #fff; text-align: center; padding: 10px 0; font-size: 22px; font-weight: bold; }
                    .details { margin: 20px 0; }
                    .book-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                    .book-table th, .book-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
                    .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
                    .contact { background: #34495e; color: #fff; padding: 10px; text-align: center; display: inline-block; text-decoration: none; border-radius: 5px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>Book Return Request - $action</div>
                    
                    <p>Dear Customer,</p>
                    <p>Your return request for Order ID <strong>$order_id</strong> has been <strong>$action</strong>.</p>
                    
                    <p><strong>Book Name:</strong> $book_name</p>
                    <p><strong>Author:</strong> $book_author</p>
                    <p><strong>Refund Amount:</strong> ₹$adjusted_refund</p>
                    <p><strong>Book Condition:</strong> $condition_status</p>
                    
                    <p>If you have any concerns, feel free to </p>
               
                    <a href='mailto:boookheaven@gmail.com' class='contact'>Contact Us</a>

                    <div class='footer'>
                        <p>Thank you for using our service!</p>
                        <p>&copy; " . date('Y') . "Book Heaven</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $mail->Body = $message;
        $mail->send();
    } catch (Exception $e) {
        error_log("Mail Error: " . $mail->ErrorInfo);
    }
}
?>
