<?php
require('connection.php');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=overdue_rentals.csv');

$output = fopen('php://output', 'w');

// Column Headers
fputcsv($output, ['User', 'Book', 'Due Date', 'Days Overdue', 'Penalty']);

// Fetch Overdue Rentals
$query = "SELECT u.name AS user, b.name AS book, o.date AS due_date, 
                 DATEDIFF(NOW(), o.date) AS overdue_days,
                 (DATEDIFF(NOW(), o.date) * 10) AS penalty
          FROM orders o
          JOIN users u ON o.user_id = u.id
          JOIN books b ON o.book_id = b.id
          WHERE o.date < NOW()";
$result = mysqli_query($con, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
