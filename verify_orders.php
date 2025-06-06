<?php
require('connection.php');

// Fetch only pending UPI and Card payments
$orders = mysqli_query($con, "SELECT * FROM orders WHERE payment_status='pending' AND (payment_method='UPI' OR payment_method='Card')");

?>

<table border="1">
    <tr>
        <th>Order ID</th>
        <th>User ID</th>
        <th>Amount</th>
        <th>Txn ID</th>
        <th>Proof</th>
        <th>Action</th>
    </tr>
    <?php while ($order = mysqli_fetch_assoc($orders)) { ?>
    <tr>
        <td><?php echo $order['id']; ?></td>
        <td><?php echo $order['user_id']; ?></td>
        <td><?php echo $order['total']; ?></td>
        <td><?php echo $order['txn_id']; ?></td>
        <td>
            <?php if (!empty($order['proof'])) { ?>
                <a href="uploads/<?php echo $order['proof']; ?>" target="_blank">View Proof</a>
            <?php } else { ?>
                No proof uploaded
            <?php } ?>
        </td>
        <td>
            <form method="post">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <button type="submit" name="approve">Approve</button>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>

<?php
// Handle Order Approval
if (isset($_POST['approve'])) {
    $orderId = mysqli_real_escape_string($con, $_POST['order_id']);

    // Verify if the order is still pending
    $checkOrder = mysqli_query($con, "SELECT * FROM orders WHERE id='$orderId' AND payment_status='pending'");
    if (mysqli_num_rows($checkOrder) > 0) {
        // Update order status
        $update = mysqli_query($con, "UPDATE orders SET payment_status='success' WHERE id='$orderId'");
        if ($update) {
            echo "<script>alert('Order Approved Successfully!'); window.location.reload();</script>";
        } else {
            echo "<script>alert('Error approving order.');</script>";
        }
    } else {
        echo "<script>alert('Order already approved or does not exist.');</script>";
    }
}
?>
