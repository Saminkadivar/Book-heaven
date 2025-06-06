<?php require('topNav.php'); ?>

<?php
// Handle form submission to update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['orderId']) && isset($_POST['status_id'])) {
    $orderId = $_POST['orderId'];
    $statusId = $_POST['status_id'];

    // Update query to set the new status
    $updateQuery = "UPDATE orders SET order_status = '$statusId' WHERE id = '$orderId'";

    if (mysqli_query($con, $updateQuery)) {
        echo "<script>alert('Order status updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating order status: " . mysqli_error($con) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/new.css">

</head>
<body>
    
</body>
</html>
<div class="scroll-container ">

<!--Main layout-->
<main class="main-content">
    <div class="container pt-4">
        <!-- Page Title -->
        <h4 class="fs-2 text-center text-primary">Orders Management</h4>
        <hr class="mb-4">
    </div>

    <div class="container">
        <button onclick="printTable()" class="btn btn-black mb-3">Print</button>    
        <div class="table-responsive mt-4">
            <div class="table-body" id="ordersTable">
                <!-- Orders Table -->
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Book Name</th>
                            <th>Book Price</th>
                            <th>Rent Duration</th>
                            <th>Address</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Order Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($con, "SELECT orders.*, name, status_name FROM orders
                                                   LEFT JOIN order_detail ON orders.id = order_detail.order_id
                                                   LEFT JOIN books ON order_detail.book_id = books.id
                                                   LEFT JOIN order_status ON orders.order_status = order_status.id 
                                                   ORDER BY date DESC");

                        if (!$res) {
                            die('Query failed: ' . mysqli_error($con));
                        }

                        if (mysqli_num_rows($res) > 0) {
                            while ($row = mysqli_fetch_assoc($res)) { ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['date']; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['total']; ?></td>
                                    <td><?php echo $row['duration']; ?></td>
                                    <td><?php echo $row['address']; ?>, <?php echo $row['address2']; ?></td>
                                    <td><?php echo $row['payment_method']; ?></td>
                                    <td>
                                        <span class="badge <?php echo $row['payment_status'] === 'Paid' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $row['payment_status']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?php echo $row['status_name']; ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusName = $row['status_name'];
                                        if ($statusName === 'Returned' || $statusName === 'Cancelled') {
                                            echo '<span class="text-muted">No Action</span>';
                                        } else { ?>
                                            <form method="post" class="d-flex">
                                                <input type="hidden" value="<?php echo $row['id']; ?>" name="orderId">
                                                <select class="form-select form-select-sm me-2" name="status_id">
                                                    <option value="" disabled selected>Select Status</option>
                                                    <?php
                                                    $sql = mysqli_query($con, "SELECT * FROM order_status ORDER BY status_name");
                                                    while ($statusRow = mysqli_fetch_assoc($sql)) {
                                                        echo "<option value=" . $statusRow['id'] . ">" . $statusRow['status_name'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                            </form>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php }
                        } else {
                            echo '<tr><td colspan="10" class="text-center">No orders found.</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
                    </div>
<script>
function printTable() {
    var printContents = document.getElementById("ordersTable").innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>

<!-- MDB -->
<script type="text/javascript" src="js/mdb.min.js"></script>
<!-- Custom scripts -->
<script type="text/javascript" src="js/admin.js"></script>
</body>

</html>


<!-- <style>
        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            height: 100%;
            padding-top: 20px;
            position: fixed;
        }

        /* Container Width */
        .container {
            max-width: 1450px;
            width: 100%;
        }

        /* Table Responsive Styling */
        .table-responsive {
            max-height: 550px; /* Adjust height as needed */
            overflow-y: auto;
            border: 1px solid #ddd;
            scrollbar-width: thin; /* Firefox */
            scrollbar-color: #888 #f1f1f1; /* Firefox */
        }

        /* Custom Scrollbar for Webkit Browsers */
        .table-responsive::-webkit-scrollbar {
            width: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Sticky Table Header */
        .table thead {
            position: sticky;
            top: 0;
            background-color: #343a40;
            color: white;
            z-index: 100;
        }

        /* Table Column Styling */
        .table {
            table-layout: auto; /* Dynamic column width */
            width: 100%;
        }

        .table th, .table td {
            word-wrap: break-word;
            white-space: normal;
        }
    </style> -->
