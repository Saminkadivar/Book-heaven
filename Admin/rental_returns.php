<?php
include('connection.php');
include('topnav.php'); // Admin Header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental Returns Management</title>
    <link rel="stylesheet" href="css/new.css">
    <style>
        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table thead {
            background: #343a40;
            color: white;
        }
        .table th, .table td {
            padding: 12px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* Status Badges */
        .badge {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .badge-success { color: green; }
        .badge-danger { color: red; }
        .badge-warning {color:#ffc107; }

        /* Buttons */
        .btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
        .btn-approve:hover { background: #218838; }
        .btn-reject:hover { background: #c82333; }

        /* Dropdown & Input */
        select, input[type="number"] {
            padding: 5px;
            width: 100px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
    <script>
        function updateRefund(condition, baseDeposit, returnId) {
            let refundField = document.getElementById("refund_" + returnId);
            if (condition === "Lost") {
                refundField.value = 0;
            } else if (condition === "Damaged") {
                refundField.value = baseDeposit * 0.5; // 50% refund for damaged books
            } else {
                refundField.value = baseDeposit; // Full refund for good condition
            }
        }
    </script>
</head>
<body>

<main class="main-content">
<div class="scroll-container ">

    <div class="container pt-4">
        <h2 class="text-center text-primary">Rental Returns Management</h2>
        <div class="table-responsive">
            <table id="rentalsTable" class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Book</th>
                        <th>Return Date</th>
                        <th>Condition</th>
                        <th>Late Fee</th>
                        <th>Refund</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                // Fetch all rental return requests
                $sql = "SELECT rr.id, rr.order_id, u.name AS user_name, b.name AS book_name, rr.return_date, 
                               rr.condition_status, rr.late_fee, rr.security_deposit_refund, rr.status, b.security
                        FROM rental_returns rr
                        JOIN users u ON rr.user_id = u.id
                        JOIN books b ON rr.book_id = b.id
                        ORDER BY FIELD(rr.status, 'Pending', 'Approved', 'Rejected'), rr.return_date DESC";

                $result = mysqli_query($con, $sql);
                
                while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['order_id']); ?></td>
                        <td><?= htmlspecialchars($row['user_name']); ?></td>
                        <td><?= htmlspecialchars($row['book_name']); ?></td>
                        <td><?= htmlspecialchars($row['return_date']); ?></td>
                        <td>
                            <?php if ($row['status'] == 'Pending') { ?>
                                <form action="process_return.php" method="POST">
                                    <input type="hidden" name="return_id" value="<?= $row['id']; ?>">
                                    <select name="condition_status" onchange="updateRefund(this.value, <?= $row['security']; ?>, <?= $row['id']; ?>)">
                                        <option value="Good" <?= ($row['condition_status'] == 'Good') ? 'selected' : ''; ?>>Good</option>
                                        <option value="Damaged" <?= ($row['condition_status'] == 'Damaged') ? 'selected' : ''; ?>>Damaged</option>
                                        <option value="Lost" <?= ($row['condition_status'] == 'Lost') ? 'selected' : ''; ?>>Lost</option>
                                    </select>
                            <?php } else { ?>
                                <?= htmlspecialchars($row['condition_status']); ?>
                            <?php } ?>
                        </td>
                        <td>₹<?= number_format($row['late_fee'], 2); ?></td>
                        <td>
                            <?php if ($row['status'] == 'Pending') { ?>
                                <input type="number" id="refund_<?= $row['id']; ?>" name="adjusted_refund" value="<?= $row['security_deposit_refund']; ?>" step="0.01" required readonly>
                            <?php } else { ?>
                                ₹<?= number_format($row['security_deposit_refund'], 2); ?>
                            <?php } ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Approved') { ?>
                                <span class="badge badge-success">✔ Approved</span>
                            <?php } elseif ($row['status'] == 'Rejected') { ?>
                                <span class="badge badge-danger">❌ Rejected</span>
                            <?php } else { ?>
                                <span class="badge badge-warning">⏳ Pending</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <?php if ($row['status'] == 'Pending') { ?>
                                    <button type="submit" name="action" value="approve" class="btn btn-approve">✅ Approve</button>
                                    <button type="submit" name="action" value="reject" class="btn btn-reject">❌ Reject</button>
                                <?php } else { ?>
                                    <span>-</span>
                                <?php } ?>
                            </div>
                            <?php if ($row['status'] == 'Pending') { ?>
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
