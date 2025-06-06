<?php require('header.php'); ?>
<?php
if (!isset($_SESSION['USER_LOGIN'])) {
    echo "<script>window.location.href='SignIn.php';</script>";
    exit;
}

$userId = $_SESSION['USER_ID'];

// Cancel order
if (isset($_GET['type']) && $_GET['type'] == 'cancel') {
    $orderId = getSafeValue($con, $_GET['id']);
    mysqli_query($con, "UPDATE orders SET order_status='4' WHERE id='$orderId'");
    echo "<script>alert('Order canceled successfully!'); window.location.href='myOrders.php';</script>";
}
?>

<script>
document.title = "My Orders | Book Heaven";
</script>

<main class="main-content">
    <div class="container pt-5">
        <h2 align="center">My Orders</h2>

        <!-- Active Orders: Pending / Shipped / Processing / Delivered -->
        <h3 class="mt-4">📌 Active Orders (Pending, Shipped, Processing, Delivered)</h3>
        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Book Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Return Date</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($con, "SELECT orders.*, order_detail.book_id, books.name, order_status.status_name, 
                                               DATE_ADD(orders.date, INTERVAL orders.duration DAY) AS return_date 
                                               FROM orders
                                               JOIN order_detail ON orders.id = order_detail.order_id
                                               JOIN books ON order_detail.book_id = books.id
                                               JOIN order_status ON orders.order_status = order_status.id
                                               WHERE orders.user_id = $userId 
                                               AND order_status.id IN (1, 2, 3, 5) -- Only Pending, Shipped, Processing, Delivered
                                               ORDER BY orders.id DESC");

                    while ($row = mysqli_fetch_assoc($res)) {
                        $orderId = $row['id'];
                        $bookId = $row['book_id'];

                        // Check if this book has been returned
                        $returnCheck = mysqli_query($con, "SELECT * FROM rental_returns WHERE order_id='$orderId' AND book_id='$bookId'");
                        $isReturned = mysqli_num_rows($returnCheck) > 0;
                    ?>
                    <tr>
                        <td>#<?php echo $orderId ?></td>
                        <td><?php echo $row['date'] ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['total'] ?></td>
                        <td><?php echo $row['duration'] ?></td>
                        <td><?php echo $row['return_date'] ?></td>
                        <td><?php echo $row['payment_status'] ?></td>
                        <td><?php echo $row['status_name'] ?></td>
                        <td>
                            <?php 
                            if ($row['status_name'] === 'Pending' || $row['status_name'] === 'Processing') {
                                echo "<a class='btn btn-danger btn-sm' href='?type=cancel&id=" . $orderId . "'>Cancel</a>";
                            } elseif ($row['status_name'] === 'Delivered' && !$isReturned) {
                                echo "<a class='btn btn-success btn-sm' href='return_book.php?order_id=" . $orderId . "&book_id=" . $bookId . "'>Return</a>";
                            } else {
                                echo "<span>-</span>";
                            }
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php
        // Fetch Cancelled & Returned Orders
        $prevOrdersQuery = "SELECT orders.id, order_detail.book_id, books.name, order_status.status_name, orders.date, 
                            DATE_ADD(orders.date, INTERVAL orders.duration DAY) AS return_date, 
                            IFNULL(rental_returns.security_deposit_refund, 0.00) AS refund_amount
                            FROM orders
                            JOIN order_detail ON orders.id = order_detail.order_id
                            JOIN books ON order_detail.book_id = books.id
                            JOIN order_status ON orders.order_status = order_status.id
                            LEFT JOIN rental_returns ON orders.id = rental_returns.order_id 
                            AND order_detail.book_id = rental_returns.book_id
                            WHERE orders.user_id = $userId  -- Explicitly specify 'orders.user_id'
                            AND order_status.id IN (4,6) -- Only Cancelled, Returned
                            ORDER BY orders.id DESC";

        // Run the query
        $prevOrders = mysqli_query($con, $prevOrdersQuery);

        // Check for errors
        if (!$prevOrders) {
            die("Query Failed: " . mysqli_error($con)); // Debugging error message
        }
        ?>

        <!-- Previous Orders: Cancelled & Returned -->
        <h3 class="mt-5">📖 Cancelled / Returned Orders</h3>
        <div class="table-responsive">
            <table class="table table-striped align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Book Name</th>
                        <th>Order Date</th>
                        <th>Return Date</th>
                        <th>Order Status</th>
                        <th>Refund Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($prevRow = mysqli_fetch_assoc($prevOrders)) { ?>
                        <tr>
                            <td>#<?php echo $prevRow['id']; ?></td>
                            <td><?php echo htmlspecialchars($prevRow['name']); ?></td>
                            <td><?php echo $prevRow['date']; ?></td>
                            <td><?php echo $prevRow['return_date']; ?></td>
                            <td><?php echo $prevRow['status_name']; ?></td>
                            <td>
                                <?php 
                                if ($prevRow['status_name'] === 'Returned') {
                                    echo "<span class='badge bg-info'>₹{$prevRow['refund_amount']}</span>";
                                } else {
                                    echo "<span>-</span>";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

</body>
</html>
