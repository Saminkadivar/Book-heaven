<?php
include 'header.php';
echo "<br>";

$con = mysqli_connect("localhost", "root", "", "mini_project");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (!isset($_SESSION['USER_LOGIN'])) {
    die("Session user_id is not set!");
}

$user_id = $_SESSION['USER_ID'];

if (isset($_GET['order_id']) && isset($_GET['book_id'])) {
    $order_id = intval($_GET['order_id']);
    $book_id = intval($_GET['book_id']);

    // Fetch book and order details
    $query = "SELECT books.name AS book_name, 
                     books.rent AS book_rent, 
                     books.security AS book_security,
                     orders.date AS order_date, 
                     orders.duration,
                     DATE_ADD(orders.date, INTERVAL orders.duration DAY) AS return_date
              FROM orders 
              JOIN order_detail ON orders.id = order_detail.order_id
              JOIN books ON order_detail.book_id = books.id
              WHERE orders.id = '$order_id' 
              AND order_detail.book_id = '$book_id'
              AND orders.user_id = '$user_id'";

    $result = mysqli_query($con, $query);

    if (!$result) {
        die("Query Error: " . mysqli_error($con));
    }

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $book_name = $row['book_name'];
        $order_date = $row['order_date'];
        $return_date = $row['return_date'];
        $book_rent = $row['book_rent'];
        $book_security = $row['book_security'];

        $today = date("Y-m-d");
        $late_fee = 0.00;
        $due_days = 0;

        if (strtotime($today) > strtotime($return_date)) {
            $due_days = (strtotime($today) - strtotime($return_date)) / (60 * 60 * 24);
            $late_fee = $due_days * ($book_rent * 0.1); // 10% per day late fee
        }

        // Default refundable amount
        $refund_amount = max(0, $book_security - $late_fee);
    } else {
        die("Invalid request or unauthorized access.");
    }
} else {
    die("Missing order ID or book ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Return Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function updateRefund() {
            let security = <?php echo $book_security; ?>;
            let lateFee = <?php echo $late_fee; ?>;
            let condition = document.getElementById("condition_status").value;
            let refundAmount = security - lateFee;

            if (condition === "Damaged") {
                refundAmount *= 0.5; // 50% refund for damaged books
            } else if (condition === "Lost") {
                refundAmount = 0; // No refund for lost books
            }

            document.getElementById("refund_display").innerText = "₹" + refundAmount.toFixed(2);
            document.getElementById("refund_amount").value = refundAmount;
        }
    </script>
</head>
<body>
    <div class="container mt-4">
        <h2>Return Book</h2>
        <form action="process_return.php" method="POST">
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
            <input type="hidden" name="late_fee" value="<?php echo $late_fee; ?>">
            <input type="hidden" name="refund_amount" id="refund_amount" value="<?php echo $refund_amount; ?>">

            <div class="mb-3">
                <p><strong>Book Name:</strong> <?php echo htmlspecialchars($book_name); ?></p>
                <p><strong>Order Date:</strong> <?php echo $order_date; ?></p>
                <p><strong>Due Date:</strong> <?php echo $return_date; ?></p>
                <p><strong>Days Late:</strong> <?php echo $due_days; ?> days</p>
                <p><strong>Late Fee:</strong> ₹<?php echo number_format($late_fee, 2); ?></p>
                <p><strong>Refund Amount:</strong> <span id="refund_display">₹<?php echo number_format($refund_amount, 2); ?></span></p>
            </div>

            <div class="mb-3">
                <label><strong>Book Condition:</strong></label>
                <select name="condition_status" id="condition_status" class="form-control" required onchange="updateRefund()">
                    <option value="Good">Good</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Lost">Lost</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Submit Return Request</button>
        </form>
    </div>
</body>
</html>
