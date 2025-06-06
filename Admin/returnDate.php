<?php require('topNav.php'); ?>
<?php
if (isset($_POST['status_id'])) {
    $order_Id = $_POST['orderId'];
    $status_id = $_POST['status_id'];
    $penaltyPerDay = 10; // Penalty charge in rupees per day

    if ($status_id === 6 || $status_id === 4) {
        $qtyRes = mysqli_query($con, "SELECT books.qty, books.id FROM orders
                                        JOIN order_detail ON orders.id = order_detail.order_id
                                        JOIN books ON order_detail.book_id = books.id
                                        WHERE order_detail.order_id='$order_Id'");
        if (!$qtyRes) {
            die("Error fetching quantity: " . mysqli_error($con));
        }
        $qtyRow = mysqli_fetch_assoc($qtyRes);
        if ($qtyRow) {
            $newQty = $qtyRow['qty'] + 1;
            $bookId = $qtyRow['id'];
            mysqli_query($con, "UPDATE books SET qty = '$newQty' WHERE id='$bookId'");
        }
    }

    // Check for late return and apply penalty
    $orderRes = mysqli_query($con, "SELECT date, duration FROM orders WHERE id='$order_Id'");
    $orderRow = mysqli_fetch_assoc($orderRes);
    $returnDate = date('Y-m-d', strtotime($orderRow['date'] . ' + ' . $orderRow['duration'] . ' days'));
    $currentDate = date('Y-m-d');

    if ($currentDate > $returnDate) {
        $daysLate = (strtotime($currentDate) - strtotime($returnDate)) / (60 * 60 * 24);
        $penaltyAmount = $daysLate * $penaltyPerDay;
        mysqli_query($con, "UPDATE orders SET penalty_amount='$penaltyAmount' WHERE id='$order_Id'");
    }

    mysqli_query($con, "UPDATE orders SET order_status='$status_id' WHERE id='$order_Id'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rentals Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/new.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .main-content {
            padding: 20px;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #ddd;
        }

        .table-responsive::-webkit-scrollbar {
            width: 8px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background-color: #888;
            border-radius: 4px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background-color: #555;
        }

        .table {
            width: 100%;
            table-layout: auto;
        }

        .table thead th {
            position: sticky;
            top: 0;
            background-color: #343a40;
            color: white;
            z-index: 1;
        }

        .table th, .table td {
            padding: 8px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .btn-black {
            background-color: #343a40;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-black:hover {
            background-color: #23272b;
        }

        .text-primary {
            color: #007bff;
        }
    </style>
</head>
<body>

<main class="main-content">
    <div class="container pt-4">
        <h2 class="text-center text-primary">Rentals Management</h2>
        <hr><br>

        <button onclick="printTable()" class="btn btn-black mb-3">Print</button>

        <div class="table-responsive">
            <table id="rentalsTable" class="table table-striped table-hover text-center shadow-lg">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Return Date</th>
                        <th>Book Name</th>
                        <th>Book Price</th>
                        <th>Rent Duration</th>
                        <th>Address</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT orders.*, books.name, order_status.status_name FROM orders
                            JOIN order_detail ON orders.id = order_detail.order_id
                            JOIN books ON order_detail.book_id = books.id
                            JOIN order_status ON orders.order_status = order_status.id
                            ORDER BY orders.date DESC";
                    $res = mysqli_query($con, $sql);
                    if (!$res) {
                        die("Error fetching orders: " . mysqli_error($con));
                    }
                    while ($row = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($row['date'] . ' + ' . $row['duration'] . ' days')); ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['total']; ?></td>
                            <td><?php echo $row['duration']; ?></td>
                            <td><?php echo $row['address'] . ', ' . $row['address2']; ?></td>
                            <td><?php echo $row['status_name']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function printTable() {
    const printContents = document.getElementById("rentalsTable").outerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>

<script type="text/javascript" src="js/mdb.min.js"></script>
<script type="text/javascript" src="js/admin.js"></script>
</body>
</html>
