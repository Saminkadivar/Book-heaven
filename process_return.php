<?php
session_start();
$con = mysqli_connect("localhost", "root", "", "mini_project");

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Ensure user is logged in
if (!isset($_SESSION['USER_ID'])) {
    die("<script>alert('Unauthorized access. Please log in.'); window.location='login.php';</script>");
}

// Ensure request method is POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("<script>alert('Invalid request method.'); window.location='myOrder.php';</script>");
}

// Retrieve and sanitize POST data
$order_id = intval($_POST['order_id']);
$book_id = intval($_POST['book_id']);
$late_fee = floatval($_POST['late_fee']);
$refund_amount = floatval($_POST['refund_amount']);
$condition_status = mysqli_real_escape_string($con, $_POST['condition_status']);
$user_id = $_SESSION['USER_ID'];

// Check if the book has already been returned
$check_return = "SELECT * FROM rental_returns WHERE order_id='$order_id' AND book_id='$book_id'";
$return_result = mysqli_query($con, $check_return);

$status_query = mysqli_query($con, "SELECT id FROM order_status WHERE status_name = 'Returned'");
$status_row = mysqli_fetch_assoc($status_query);
$returned_status_id = $status_row['id'];

if (mysqli_num_rows($return_result) > 0) {
    die("<script>alert('This book has already been returned!'); window.location='myOrder.php';</script>");
}

// Validate order details
$query = "SELECT * FROM orders 
          JOIN order_detail ON orders.id = order_detail.order_id
          WHERE orders.id = '$order_id' 
          AND order_detail.book_id = '$book_id'
          AND orders.user_id = '$user_id'";

$result = mysqli_query($con, $query);

if (!$result) {
    die("<script>alert('Query Error: " . mysqli_error($con) . "'); window.location='myOrder.php';</script>");
}

if (mysqli_num_rows($result) == 0) {
    die("<script>alert('Invalid request or unauthorized access.'); window.location='myOrder.php';</script>");
}

// Start Transaction
mysqli_begin_transaction($con);

try {
    // Update order status to 'Returned'
    $update_order = "UPDATE orders SET order_status = '$returned_status_id' WHERE id = '$order_id'";
    if (!mysqli_query($con, $update_order)) {
        throw new Exception("Order update failed: " . mysqli_error($con));
    }

    // Update book stock (increase quantity by 1)
    $update_book = "UPDATE books SET qty = qty + 1 WHERE id = '$book_id'";
    if (!mysqli_query($con, $update_book)) {
        throw new Exception("Book stock update failed: " . mysqli_error($con));
    }

    // Insert return details
    $insert_return = "INSERT INTO rental_returns (order_id, book_id, user_id, late_fee, security_deposit_refund, condition_status, return_date)
                      VALUES ('$order_id', '$book_id', '$user_id', '$late_fee', '$refund_amount', '$condition_status', NOW())";
    if (!mysqli_query($con, $insert_return)) {
        throw new Exception("Return log insert failed: " . mysqli_error($con));
    }

    // Commit Transaction
    mysqli_commit($con);
    
    echo "<script>alert('Book return processed successfully!'); window.location='myOrder.php';</script>";
    exit; // Prevent further execution after redirect

} catch (Exception $e) {
    // Rollback Transaction on Error
    mysqli_rollback($con);
    die("<script>alert('" . $e->getMessage() . "'); window.location='myOrder.php';</script>");
}

// Close connection
mysqli_close($con);
?>
