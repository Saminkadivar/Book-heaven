<?php
if (!defined('BOOK_IMAGE_SITE_PATH')) {
    define('BOOK_IMAGE_SITE_PATH', 'Img/books/');
}
require('header.php');

if (!isset($_SESSION['USER_LOGIN'])) {
    echo "<script>window.top.location='SignIn.php';</script>";
    exit;
}

// Razorpay API Credentials
$keyId = "rzp_test_529npOnJks99aK";
$keySecret = "jkZfsnHfFOQWJZz1GyJNcDWm";

$bookId = '';
$duration = '';
if (isset($_GET['id'])) {
    $bookId = mysqli_real_escape_string($con, $_GET['id']);
}
if (isset($_GET['duration'])) {
    $duration = mysqli_real_escape_string($con, $_GET['duration']);
}

$getProduct = getProduct($con, '', '', $bookId);
$totalRent = $getProduct['0']['rent'] * $duration;
$totalPrice = $totalRent + $getProduct['0']['security'];

if (isset($_POST['submit'])) {
    $address = getSafeValue($con, $_POST['address']);
    $address2 = getSafeValue($con, $_POST['address2']);
    $pin = getSafeValue($con, $_POST['pin']);
    $paymentMethod = getSafeValue($con, $_POST['paymentMethod']);
    $userId = $_SESSION['USER_ID'];
    $orderStatus = '1'; // Order placed but pending confirmation
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d H:i:s');

    if ($paymentMethod == 'COD') {
        // Clear old session order data
        unset($_SESSION['order_data']); 
    
        // Insert new order into the database
        $sql = "INSERT INTO orders(user_id, book_id, address, address2, pin, payment_method, total, payment_status, order_status, date, duration)
                VALUES ('$userId','$bookId', '$address', '$address2', '$pin','$paymentMethod','$totalPrice','success','$orderStatus','$date','$duration')";
        mysqli_query($con, $sql);
    
        $orderId = mysqli_insert_id($con); // Fetch newly created order ID
    
        // Insert into order details table
        mysqli_query($con, "INSERT INTO order_detail(order_id, book_id, price, time) VALUES ('$orderId', '$bookId', '$totalPrice', '$duration')");
    
        // Reduce book quantity
        $newQty = $getProduct['0']['qty'] - 1;
        mysqli_query($con, "UPDATE books SET qty = '$newQty' WHERE id='$bookId'");
    
        // Store only the latest order details in session
        $_SESSION['order_data'] = [
            'user_id' => $userId,
            'address' => $address,
            'address2' => $address2,
            'pin' => $pin,
            'payment_method' => 'COD',
            'total' => $totalPrice,
            'order_status' => '1',
            'date' => $date,
            'duration' => $duration,
            'book_id' => $bookId,
            'orderId' => $orderId // Store the latest order ID
        ];
    
        echo "<script>window.top.location = 'thankYou.php?orderId=$orderId';</script>";
        exit;
    } else {
        // For Online Payment
        unset($_SESSION['order_data']); // Clear previous session order data
    
        // Insert new order for online payment
        $sql = "INSERT INTO orders(user_id, book_id, address, address2, pin, payment_method, total, payment_status, order_status, date, duration)
                VALUES ('$userId', '$bookId', '$address', '$address2', '$pin', 'Online', '$totalPrice', 'pending', '$orderStatus', '$date', '$duration')";
        mysqli_query($con, $sql);
    
        $orderId = mysqli_insert_id($con); // Fetch newly created order ID
    
        // Insert into order details table
        mysqli_query($con, "INSERT INTO order_detail(order_id, book_id, price, time) VALUES ('$orderId', '$bookId', '$totalPrice', '$duration')");
    
        // Reduce book quantity
        $newQty = $getProduct['0']['qty'] - 1;
        mysqli_query($con, "UPDATE books SET qty = '$newQty' WHERE id='$bookId'");
    
        // Store only the latest order details in session
        $_SESSION['order_data'] = [
            'user_id' => $userId,
            'address' => $address,
            'address2' => $address2,
            'pin' => $pin,
            'payment_method' => 'Online',
            'total' => $totalPrice,
            'order_status' => '1',
            'date' => $date,
            'duration' => $duration,
            'book_id' => $bookId,
            'orderId' => $orderId // Store the latest order ID
        ];
    
        echo "<script>window.top.location = 'paymentRedirect.php';</script>";
        exit;
    }
}
?>    


<script>
document.title = "Checkout | Book Heaven";
</script>
<div class="container">
    <main>
        <div class="py-5 text-center">
            <h2>Checkout</h2>
        </div>
        <div class="row g-5">
            <div class="col-md-5 col-lg-4 order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text">Your book</span>
                </h4>
                <ul class="list-group mb-3 dark-background">
                    <li class="list-group-item d-flex justify-content-center fw-bold lh-sm ">
                        <div>
                            <h2 class="my-0 fs-5 fw-bold"><?php echo $getProduct['0']['name'] ?></h2>
                        </div>
                        <!--                        <strong>₹-->
                        <?php //echo $getProduct['0'] ['price'] 
                        ?>
                        <!--</strong>-->
                    </li>
                    <li class="list-group-item justify-content-start lh-sm">
                        <p><span class="fw-bold">MRP</span> = ₹<span
                                class="text-decoration-line-through"><?php echo $getProduct['0']['mrp'] ?></span>
                        </p>
                        <p><span class="fw-bold">Rent Price</span> = ₹<?php echo $getProduct['0']['rent'] ?> Per Day
                        </p>
                        <p><span class="fw-bold">Duration</span> = <?php echo $duration ?> Days</p>
                        <p><span class="fw-bold">Total Rent</span> = ₹<?php echo $totalRent ?></p>
                        <p><span class="fw-bold">Security Deposit<span style="color: red"><sup>*</sup></span></span> =
                            ₹<?php echo $getProduct['0']['security'] ?></p>
                        <p><span class="fw-bold">Total price</span> = <?php echo $totalPrice ?> </p>
                    </li>
                </ul>
                <ul class="list-group mb-3">
                    <li class="list-group-item d-flex justify-content-center fw-bold lh-sm">
                        <div>
                            <h2 class="my-0 fs-5 fw-bold"><span style="color: red"><sup>*</sup></span>Deposit Terms</h2>
                        </div>
                        <!--                        <strong>₹-->
                        <?php //echo $getProduct['0'] ['price'] 
                        ?>
                        <!--</strong>-->
                    </li>
                    <li class="list-group-item justify-content-start lh-sm">
                        <ol type="1">
                            <li>
                            You must submit a photocopy of your Aadhar Card and show the original to the delivery person for verification.
                            </li>
                            <li>
                            The security deposit is fully refundable once the book is returned in the same condition as received.
                            </li>
                            <li>
                            Books must be returned within the rental period. Late returns may attract penalty fees deducted from the security deposit.
                            </li>
                            <li>
                            The admin will inspect the book's condition before processing the refund.
                            </li>
                            <li>
                            The refund process will take 3-5 business days after the book is successfully returned and verified.
                            </li>
                        </ol>
                    </li>
                </ul>
            </div>
            <div class="col-md-7 col-lg-8">
                <form class="needs-validation" method="post" novalidate>
                    <h4 class="mb-3">Shipping address</h4>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="address" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" name="address" placeholder="address" required>
                            <div class="invalid-feedback">
                                Please enter your shipping address.
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="address2" class="form-label">Address Line 2 </label>
                            <input type="text" class="form-control" name="address2" placeholder="">
                        </div>

                        <div class="col-3">
                            <label for="pin" class="form-label">Pin Code</label>
                            <input type="number" maxlength="6" class="form-control" name="pin" placeholder="000000"
                                required>
                            <div class="invalid-feedback">
                                Pin code required.
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="my-3">
                        <div class="form-check">
                            <input id="cod" name="paymentMethod" type="radio" value="COD" class="form-check-input"
                                checked required>
                            <label class="form-check-label" for="cod">Cash on delivery(COD)</label>
                        </div>
                        <div class="form-check">
                            <input id="payU" name="paymentMethod" type="radio" value="payU" class="form-check-input">
                            <label class="form-check-label" for="payU">Online Payment</label>
                        </div>
                    </div>

                    <hr class="my-4 invisible">

                    <button class="w-100 btn btn-primary btn-lg" type="submit" name="submit">Place Your Order
                    </button>
                </form>
            </div>
        </div>
    </main>


</div>
<script>
(function() {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    let forms = document.querySelectorAll('.needs-validation');

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
})()
</script>
<?php require('footer.php') ?>