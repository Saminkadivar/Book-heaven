<?php
require('connection.php');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=rental_orders_report.csv');

$output = fopen('php://output', 'w');

// Column Headers
fputcsv($output, ['User', 'Book', 'Rental Date', 'Due Date', 'Status', 'Amount Paid']);

// Fetch Data
$query = "SELECT u.name AS user, b.name AS book, o.date AS rental_date, o.date AS due_date, 
                 o.order_status, o.total 
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN books b ON o.book_id = b.id";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
