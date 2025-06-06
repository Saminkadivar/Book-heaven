<?php
include 'connection.php'; // Database connection



$sql = "SELECT rr.id, u.name AS user_name, b.name AS book_name, rr.return_date, 
               rr.condition_status, rr.late_fee, rr.security_deposit_refund, rr.status
        FROM rental_returns rr
        JOIN users u ON rr.user_id = u.id
        JOIN books b ON rr.book_id = b.id
        ORDER BY rr.created_at DESC";

        $result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Return Status</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Your Rental Returns</h2>
        <table class="table table-bordered mt-4">
            <thead class="table-dark">
                <tr>
                    <th>Book</th>
                    <th>Return Date</th>
                    <th>Condition</th>
                    <th>Late Fee ($)</th>
                    <th>Refund ($)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['book_name']; ?></td>
                    <td><?php echo $row['return_date']; ?></td>
                    <td><?php echo ucfirst($row['condition_status']); ?></td>
                    <td>$<?php echo number_format($row['late_fee'], 2); ?></td>
                    <td>$<?php echo number_format($row['security_deposit_refund'], 2); ?></td>
                    <td>
                        <span class="badge bg-<?php echo ($row['status'] == 'Approved') ? 'success' : 'warning'; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php mysqli_close($con); ?>
