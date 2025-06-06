<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';        

function sendOrderConfirmation($con, $userEmail, $userName, $orderId) {
    // Fetch order details from the database
    $orderQuery = "SELECT b.name, b.author, od.price, od.time 
                   FROM order_detail od
                   JOIN books b ON od.book_id = b.id
                   WHERE od.order_id = ?";

    // Prepare the statement
    $stmt = $con->prepare($orderQuery);
    if (!$stmt) {
        die("Prepare failed: " . $con->error);  // Debugging: check if query preparation failed
    }

    $stmt->bind_param("i", $orderId);

    // Execute the query
    if (!$stmt->execute()) {
        die("Execution failed: " . $stmt->error);  // Debugging: check execution error
    }

    $orderResult = $stmt->get_result();

    if ($orderResult->num_rows == 0) {
        return false; // No order found
    }

    // Proceed with email sending logic...



    $emailContent = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Order Confirmation - BOOK HEAVEN</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            .container {
                max-width: 600px;
                margin: 20px auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            }
            .header {
                background: #34495e;
                color: white;
                text-align: center;
                padding: 15px 0;
                font-size: 24px;
                font-weight: bold;
                border-radius: 8px 8px 0 0;
            }
            .content {
                padding: 20px;
                color: #333;
            }
            .content p {
                font-size: 16px;
                line-height: 1.5;
            }
            .order-details {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }
            .order-details th, .order-details td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }
            .order-details th {
                background-color: #34495e;
                color: white;
            }
            .footer {
                text-align: center;
                padding: 15px;
                font-size: 14px;
                background: #eee;
                border-radius: 0 0 8px 8px;
            }
            .footer a {
                color: #34495e;
                text-decoration: none;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>📚 Thank You for Your Order! 📚</div>
            <div class='content'>
                <p>Dear <strong>$userName</strong>,</p>
                <p>Your order <strong>#$orderId</strong> has been successfully placed. Below are your order details:</p>
                <table class='order-details'>
                    <thead>
                        <tr>
                            <th>Book Name</th>
                            <th>Author</th>
                            <th>Total Price</th>
                            <th>Duration (Days)</th>
                        </tr>
                    </thead>
                    <tbody>";
    
    while ($row = $orderResult->fetch_assoc()) {
        $emailContent .= "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['author']}</td>
                            <td>₹" . number_format($row['price'], 2) . "</td>
                            <td>{$row['time']}</td>
                          </tr>";
    }
    
    $emailContent .= "  </tbody>
                </table>
                <p>If you have any questions, feel free to <a href='contact.php'>contact us</a>.</p>
            </div>
            <div class='footer'>
                <p>📖 Happy Reading! | <a href='localhost/sk'>Visit BOOK HEAVEN</a></p>
            </div>
        </div>
    </body>
    </html>";
    
    // Initialize PHPMailer
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

        // Sender & Recipient
        $mail->setFrom('bookheaven@gmail.com', 'BOOK HEAVEN');
        $mail->addAddress($userEmail, $userName);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - #$orderId";
        $mail->Body = $emailContent;

        // Send email
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Order confirmation email failed: " . $mail->ErrorInfo);
        return false;
    }
}
?>
