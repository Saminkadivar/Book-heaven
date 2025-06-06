<?php
require('topNav.php');
require('connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<main class="main-content">
<div class="container pt-4">
    <h2 class="text-center text-primary">📊 Admin Analytics & Reports</h2>
    <hr>
    
    <!-- Rental Orders Report -->
    <h5>📄 Rental Orders Report</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Book</th>
                <th>Rental Date</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Amount Paid</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT u.name AS user, b.name AS book, o.date AS rental_date, o.date, o.order_status, o.total FROM orders o
                      JOIN users u ON o.user_id = u.id
                      JOIN books b ON o.book_id = b.id";
            $result = mysqli_query($con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['user']}</td>
                        <td>{$row['book']}</td>
                        <td>{$row['rental_date']}</td>
                        <td>{$row['date']}</td>
                        <td>{$row['order_status']}</td>
                        <td>₹{$row['total']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Overdue Rentals Report -->
    <h5>⏳ Overdue Rentals Report</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Book</th>
                <th>Due Date</th>
                <th>Days Overdue</th>
                <th>Penalty</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT u.name AS user, b.name AS book, o.date, DATEDIFF(NOW(), o.date) AS overdue_days,
                      (DATEDIFF(NOW(), o.date) * 10) AS penalty FROM orders o
                      JOIN users u ON o.user_id = u.id
                      JOIN books b ON o.book_id = b.id WHERE o.date < NOW()";
            $result = mysqli_query($con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['user']}</td>
                        <td>{$row['book']}</td>
                        <td>{$row['date']}</td>
                        <td>{$row['overdue_days']}</td>
                        <td>₹{$row['penalty']}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Print & Export Options -->
    <button onclick="window.print()" class="btn btn-primary">🖨 Print Report</button>
    <a href="export_csv.php" class="btn btn-success">📥 Export CSV</a>
</div>
</main>
<script src="js/mdb.min.js"></script>
</body>
</html>
