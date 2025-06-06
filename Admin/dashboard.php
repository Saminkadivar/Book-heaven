<?php
require('topNav.php');
function getSingleValue($con, $query) {
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die("Query Error: " . mysqli_error($con) . " - Query: " . $query);
    }

    $row = mysqli_fetch_assoc($result);
    return $row ? $row['total'] : 0;
}

// Fetch total counts
$totalCategories = getSingleValue($con, "SELECT COUNT(*) AS total FROM categories");
$totalBooks = getSingleValue($con, "SELECT COUNT(*) AS total FROM books");
$totalUsers = getSingleValue($con, "SELECT COUNT(*) AS total FROM users");
$totalRentals = getSingleValue($con, "SELECT COUNT(*) AS total FROM orders");
$totalRevenue = getSingleValue($con, "SELECT SUM(total) AS total FROM orders WHERE order_status = '5'");
$totalPendingPayments = getSingleValue($con, "SELECT SUM(total) AS total FROM orders WHERE order_status = '1'");
$totalRefundedDeposits = getSingleValue($con, "SELECT SUM(security_deposit_refund) AS total FROM rental_returns");
$totalOverdueFees = getSingleValue($con, "SELECT SUM(late_fee) AS total FROM rental_returns WHERE late_fee > 0");
// Fetch recent rentals
$recentRentalsQuery = "SELECT o.id, u.name, b.name, o.date 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       JOIN order_detail od ON o.id = od.order_id
                       JOIN books b ON od.book_id = b.id 
                       ORDER BY o.date DESC LIMIT 5";

$recentRentals = mysqli_query($con, $recentRentalsQuery);

// Fetch overdue books
$overdueBooksQuery = "SELECT o.id, u.name, b.name, o.duration, r.late_fee
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      JOIN order_detail od ON o.id = od.order_id
                      JOIN books b ON od.book_id = b.id
                      LEFT JOIN rental_returns r ON o.id = r.order_id
                      WHERE o.duration < CURDATE() AND r.order_id IS NULL
                      ORDER BY o.duration ASC LIMIT 5";
$overdueBooks = mysqli_query($con, $overdueBooksQuery);

// Fetch top borrowed books
$topBooksQuery = "SELECT b.name, COUNT(od.book_id) AS rented_count
                  FROM order_detail od
                  JOIN books b ON od.book_id = b.id
                  GROUP BY od.book_id
                  ORDER BY rented_count DESC LIMIT 5";
$topBooks = mysqli_query($con, $topBooksQuery);

// Fetch recent users
$recentUsersQuery = "SELECT name, email, doj FROM users ORDER BY doj DESC LIMIT 5";
$recentUsers = mysqli_query($con, $recentUsersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/new.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<main class="main-content">
<div class="container pt-4">
    <div class="text-center mb-5">
        <h2 class="text-primary">📊 Admin Dashboard</h2>
        <hr>
    </div>

    <!-- Summary Cards -->
    <div class="row text-center mb-5">
        <?php
        $stats = [
            ["title" => "Total Categories", "count" => $totalCategories, "icon" => "📁", "link" => "categories.php", "btn" => "primary"],
            ["title" => "Total Books", "count" => $totalBooks, "icon" => "📚", "link" => "books.php", "btn" => "success"],
            ["title" => "Total Users", "count" => $totalUsers, "icon" => "👥", "link" => "users.php", "btn" => "info"],
            ["title" => "Total Rentals", "count" => $totalRentals, "icon" => "📦", "link" => "returnDate.php", "btn" => "warning"]
        ];

        foreach ($stats as $stat) { ?>
            <div class="col-md-3">
                <div class="card shadow-lg p-3 bg-white rounded border-0">
                    <h5><?php echo $stat["icon"] . " " . $stat["title"]; ?></h5>
                    <p class="display-4 text-dark fw-bold"><?php echo $stat["count"]; ?></p>
                </div>
                <a href="<?php echo $stat["link"]; ?>" class="btn btn-<?php echo $stat["btn"]; ?> mt-2">Manage</a>
            </div>
        <?php } ?>
    </div>

    <!-- Financial Overview -->
    <div class="row text-center">
        <?php
        $finance = [
            ["title" => "Total Revenue", "amount" => $totalRevenue, "color" => "success"],
            ["title" => "Pending Payments", "amount" => $totalPendingPayments, "color" => "danger"],
            ["title" => "Refunded Deposits", "amount" => $totalRefundedDeposits, "color" => "info"],
            ["title" => "Overdue Fees", "amount" => $totalOverdueFees, "color" => "warning"]
        ];
        foreach ($finance as $item) { ?>
            <div class="col-md-3">
                <div class="card shadow p-3 bg-light border-<?php echo $item["color"]; ?>">
                    <h5><?php echo $item["title"]; ?></h5>
                    <p class="display-6 text-<?php echo $item["color"]; ?>">₹<?php echo number_format($item["amount"], 2); ?></p>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Charts -->
    <div class="row mt-5">
        <div class="col-md-6">
            <canvas id="revenueChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="booksChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <!-- <div class="row mt-5">
        <div class="col-md-6">
            <h5>📌 Recent Rentals</h5>
            <ul class="list-group">
                <?php ///while ($rental = mysqli_fetch_assoc($recentRentals)) { ?>
                    <li class="list-group-item"><?php// echo $rental['name'] . " rented <b>" . $rental['name'] . "</b> on " . date("d M Y", strtotime($rental['date'])); ?></li>
                <?php //s} ?>
            </ul>
        </div>
        <div class="col-md-6">
            <h5>⚠️ Overdue Books</h5>
            <ul class="list-group">
                <?php //while ($overdue = mysqli_fetch_assoc($overdueBooks)) { ?>
                    <li class="list-group-item text-danger"><?php ///echo $overdue['name'] . " - <b>" . $overdue['name'] . "</b> (Due: " . date("d M Y", strtotime($overdue['duration'])) . ")"; ?></li>
                <?php// } ?>
            </ul>
        </div>
    </div> -->

</div>
</main>

<!-- Chart.js Data -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Revenue Chart
    var ctx1 = document.getElementById("revenueChart").getContext("2d");
    new Chart(ctx1, {
        type: "bar",
        data: {
            labels: ["Total Revenue", "Pending Payments", "Refunded Deposits", "Overdue Fees"],
            datasets: [{
                label: "₹ Amount",
                data: [<?php echo $totalRevenue; ?>, <?php echo $totalPendingPayments; ?>, <?php echo $totalRefundedDeposits; ?>, <?php echo $totalOverdueFees; ?>],
                backgroundColor: ["#28a745", "#dc3545", "#17a2b8", "#ffc107"]
            }]
        }
    });

    // Most Rented Books Chart
    var ctx2 = document.getElementById("booksChart").getContext("2d");
    new Chart(ctx2, {
        type: "pie",
        data: {
            labels: [
                <?php while ($book = mysqli_fetch_assoc($topBooks)) { echo '"' . $book["title"] . '", '; } ?>
            ],
            datasets: [{
                label: "Times Borrowed",
                data: [
                    <?php
                    mysqli_data_seek($topBooks, 0);
                    while ($book = mysqli_fetch_assoc($topBooks)) { echo $book["rented_count"] . ", "; }
                    ?>
                ],
                backgroundColor: ["#ff5733", "#33ff57", "#3357ff", "#ff33a8", "#f3ff33"]
            }]
        }
    });
});
</script>

<script src="js/mdb.min.js"></script>
<script src="js/admin.js"></script>
</body>
</html>
