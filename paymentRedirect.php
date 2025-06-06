<?php
require('vendor/autoload.php');
require('connection.php');

use Razorpay\Api\Api;

// Razorpay API Keys
$keyId = "rzp_test_529npOnJks99aK";
$keySecret = "jkZfsnHfFOQWJZz1GyJNcDWm";

$api = new Api($keyId, $keySecret);

$userId = $_SESSION['USER_ID'];

// Fetch user details including phone number
$userQuery = mysqli_query($con, "SELECT mobile FROM users WHERE id='$userId'");

if ($userQuery && mysqli_num_rows($userQuery) > 0) {
    $userData = mysqli_fetch_assoc($userQuery);
    $userPhone = $userData['mobile']; // Fetch contact number directly
} 

// Validate session data
if (!isset($_SESSION['order_data']['total']) || !isset($_SESSION['USER_ID'])) {
    die("Error: Missing payment details. Please try again.");
}
// $orderQuery = mysqli_query($con, "SELECT * FROM orders WHERE id=''");

$orderId = $_SESSION['order_data']['orderId'];
// mysqli_real_escape_string($con, $_GET['orderId']); 

$orderAmount = $_SESSION['order_data']['total'] * 100; // Convert to paise

$orderData = [
    'receipt' => strval($orderId),
    'amount' => $orderAmount,
    'currency' => 'INR',
    'payment_capture' => 1
];

try {
    $razorpayOrder = $api->order->create($orderData);

    if (!isset($razorpayOrder['id'])) {
        throw new Exception("Razorpay API Error: " . json_encode($razorpayOrder));
    }

    // ✅ Store Order ID and Payment ID in Session
    $_SESSION['razorpay_order_id'] = $razorpayOrder['id'];
} catch (\Razorpay\Api\Errors\BadRequestError $e) {
    die("Razorpay Error: " . $e->getMessage());
} catch (\Exception $e) {
    die("Error: " . $e->getMessage());
}

?>


<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo $keyId; ?>",
    "amount": "<?php echo $orderAmount; ?>",
    "currency": "INR",
    "name": "Book Heaven",
    "order_id": "<?php echo $_SESSION['razorpay_order_id']; ?>",
    "handler": function (response) {
        // ✅ Redirect with both order ID and payment ID
        window.location.href = "thankyou.php?payment_id=" + response.razorpay_payment_id;
    },
    "prefill": {
        "name": "<?php echo $_SESSION['USER_NAME']; ?>",
        "email": "<?php echo $_SESSION['USER_EMAIL']; ?>",
        "contact": "<?php echo $userPhone; ?>" // Contact number fetched from database
    },
    "theme": {
        "color": "#34495e"
    }
};


var rzp1 = new Razorpay(options);
rzp1.open();
</script>
