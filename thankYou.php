<?php
require('header.php');
require('sendOrderConfirmation.php'); 
require_once('connection.php'); 

// Ensure orderId exists in session or GET request
if (!isset($_SESSION['order_data']['orderId']) && !isset($_GET['orderId'])) {
    die("<script>alert('Invalid order! No order ID found.'); window.location.href = 'index.php';</script>");
}

// Get order ID: First try session, then fallback to GET parameter
$orderId = $_SESSION['order_data']['orderId'] ?? $_GET['orderId'];

// Check if payment ID is provided for online payments
$paymentId = isset($_GET['payment_id']) ? mysqli_real_escape_string($con, $_GET['payment_id']) : null;

// Fetch order details from the database
$orderQuery = mysqli_query($con, "SELECT * FROM orders WHERE id='$orderId'");
if (!$orderQuery || mysqli_num_rows($orderQuery) == 0) {
    die("<script>alert('Order not found!'); window.location.href = 'index.php';</script>");
}

$orderData = mysqli_fetch_assoc($orderQuery);
$paymentStatus = $orderData['payment_status'];
$userId = $orderData['user_id'];
$totalAmount = $orderData['total'];
$paymentMethod = trim($orderData['payment_method']); // Trim to avoid extra spaces

// Debugging: Log session and payment details
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

echo "<script>console.log('Order ID: $orderId');</script>";
echo "<script>console.log('Payment Method: $paymentMethod');</script>";
echo "<script>console.log('Payment ID: $paymentId');</script>";

// Prevent duplicate payment updates
if ($paymentStatus == 'success') {
} else {
    if ($paymentMethod == 'payu') {
        $updatePayment = mysqli_query($con, "UPDATE orders SET payment_status='success' WHERE id='$orderId'");
    } else {
        if (!$paymentId) {
            die("<script>alert('Invalid request: Missing Payment ID for Online Payment!');</script>");
        }
        $updatePayment = mysqli_query($con, "UPDATE orders SET 
            payment_status='success', 
            transaction_id='$paymentId' 
            WHERE id='$orderId'");
    }

    if (!$updatePayment) {
        die("Payment Update Failed: " . mysqli_error($con));
    }
}

// Fetch user details
$userQuery = mysqli_query($con, "SELECT * FROM users WHERE id='$userId'");
$userData = mysqli_fetch_assoc($userQuery);
$userName = $userData['name'];
$userEmail = $userData['email'];

// Send order confirmation email
$emailStatus = sendOrderConfirmation($con, $userEmail, $userName, $orderId);

// Clear session only after using order data
unset($_SESSION['order_data']);
?>

<div class="container py-5 text-center">
    <h2>Your Order is Confirmed! <i class="fas fa-check-circle text-success"></i></h2><br>
    <p>Your order ID is: <strong>#<?php echo $orderId; ?></strong></p>
    <p><strong>Thank You, <?php echo $userName; ?></strong> for shopping with us! Your order is being processed.</p>
    <p>We will send you a shipping confirmation email once your items are shipped.</p>
    <p><strong>Amount Paid:</strong> ₹<?php echo number_format($totalAmount, 2); ?></p>
    <p><strong>Payment Method:</strong> <?php echo $paymentMethod; ?></p>

    <?php if ($paymentMethod != 'payu') { ?>
        <p><strong>Transaction ID:</strong> <?php echo $paymentId; ?></p>
    <?php } ?>

    <p>Team Book Heaven</p><br>
    <a href="index.php" class="btn btn-primary">Continue Browsing</a>
</div>

<script>
// Auto-redirect to homepage after 5 seconds
setTimeout(() => {
    window.location.href = 'index.php';
}, 5000);
</script>

<?php require('footer.php'); ?>
