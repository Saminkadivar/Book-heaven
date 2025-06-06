<?php require('header.php') ?>
<?php
if (!isset($_SESSION['USER_LOGIN'])) {
    echo "<script>window.top.location='SignIn.php';</script>";
    exit;
}

if (isset($_GET['type']) && $_GET['type'] != ' ') {
    $type = getSafeValue($con, $_GET['type']);
    if ($type == 'cancel') {
        $id = getSafeValue($con, $_GET['id']);
        $deleteSql = "UPDATE orders SET order_status='4' WHERE id='$id'";
        mysqli_query($con, $deleteSql);

        $qtyRes = mysqli_query($con, "SELECT books.qty, books.id FROM orders
                                      JOIN order_detail ON orders.id=order_detail.order_id
                                      JOIN books ON order_detail.book_id=books.id
                                      WHERE order_detail.order_id='$id'");
        $qtyRow = mysqli_fetch_assoc($qtyRes);
        $newQty = $qtyRow['qty'] + 1;
        $bookId = $qtyRow['id'];
        mysqli_query($con, "UPDATE books SET qty = '$newQty' WHERE id='$bookId';");
    }
}
?>

<script>
document.title = "My Orders | Book Heaven";
</script>

<main class="main-content">
    <div class="container pt-5">
        <h2 align=center>My Orders</h2>
        <div class="table-responsive mt-5">
            <table class="table table-striped align-middle text-center" style="table-layout: fixed;">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Book Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Return Date</th>
                        <th>Address</th>
                        <th>Payment Method</th>
                        <th>Payment Status</th>
                        <th>Order Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $userId = $_SESSION['USER_ID'];
                    $res = mysqli_query($con, "SELECT orders.*, order_detail.book_id, books.name, order_status.status_name, 
                                               DATE_ADD(orders.date, INTERVAL orders.duration DAY) AS return_date 
                                               FROM orders
                                               JOIN order_detail ON orders.id = order_detail.order_id
                                               JOIN books ON order_detail.book_id = books.id
                                               JOIN order_status ON orders.order_status = order_status.id
                                               WHERE user_id = $userId ORDER BY orders.id DESC");

                    while ($row = mysqli_fetch_assoc($res)) {
                        $orderId = $row['id'];
                        $bookId = $row['book_id'];

                        // Check if this book has already been returned
                        $returnCheck = mysqli_query($con, "SELECT * FROM rental_returns WHERE order_id='$orderId' AND book_id='$bookId'");
                        $isReturned = mysqli_num_rows($returnCheck) > 0;
                    ?>
                    <tr>
                        <td>#<?php echo $orderId ?></td>
                        <td><?php echo $row['date'] ?></td>
                        <td><?php echo $row['name'] ?></td>
                        <td><?php echo $row['total'] ?></td>
                        <td><?php echo $row['duration'] ?></td>
                        <td><?php echo $row['return_date'] ?></td>
                        <td><?php echo $row['address'] ?>, <?php echo $row['address2'] ?></td>
                        <td><?php echo $row['payment_method'] ?></td>
                        <td><?php echo $row['payment_status'] ?></td>
                        <td><?php echo $row['status_name'] ?></td>
                      <td>
                            <?php 
                            if ($row['status_name'] === 'Cancelled' || $row['status_name'] === 'Returned' || $row['status_name'] === 'Shipped') {
                                echo "<span>-</span>";
                            } elseif ($row['status_name'] === 'Pending' || $row['status_name'] === 'Processing') {
                                echo "<a class='btn btn-danger px-2 py-1' href='?type=cancel&id=" . $orderId . "'>Cancel</a> ";
                            } elseif ($row['status_name'] === 'Delivered' && !$isReturned) {
                                echo "<a class='btn btn-success px-2 py-1' href='return_book.php?order_id=" . $orderId . "&book_id=" . $bookId . "'>Return</a>";
                            }
                            ?>
                        </td>

                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div id="scrollBtn">
            <button onclick="topFunction()" id="ScrollUpBtn" title="Go to top">
                <span> <i class="fas fa-chevron-up text-white"></i></span>
            </button>
        </div>

        <script>
        let mybutton = document.getElementById("ScrollUpBtn");

        window.onscroll = function() {
            scrollFunction();
        };

        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                mybutton.style.display = "block";
            } else {
                mybutton.style.display = "none";
            }
        }

        function topFunction() {
            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;
        }
        </script>

    </div>
</main>
</body>
</html>
