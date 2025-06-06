<?php
require('topNav.php');
require('connection.php');

function getSingleValue($con, $query) {
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Query Error: " . mysqli_error($con) . " - Query: " . $query);
    }
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['total'] : 0;
}

// Fetch Revenue for last 6 months
$revenueData = [];
$labels = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date("Y-m", strtotime("-$i months"));
    $labels[] = date("M Y", strtotime("-$i months"));
    
    $query = "SELECT SUM(total) AS total FROM orders WHERE order_status = '5' AND DATE_FORMAT(date, '%Y-%m') = '$month'";
    $revenueData[] = getSingleValue($con, $query) ?: 0;
}

// Fetch top 5 rented books
$topBooksQuery = "SELECT b.name, COUNT(od.book_id) AS rented_count
                  FROM order_detail od
                  JOIN books b ON od.book_id = b.id
                  GROUP BY od.book_id
                  ORDER BY rented_count DESC LIMIT 5";
$topBooksResult = mysqli_query($con, $topBooksQuery);
if (!$topBooksResult) {
    die("Query Error: " . mysqli_error($con) . " - Query: " . $topBooksQuery);
}
$topBooks = [];
while ($book = mysqli_fetch_assoc($topBooksResult)) {
    $topBooks[] = $book;
}

// Fetch user registrations per month
$userGrowthQuery = "SELECT DATE_FORMAT(doj, '%Y-%m') AS month, COUNT(id) AS count
                    FROM users GROUP BY month ORDER BY month ASC";
$userGrowthResult = mysqli_query($con, $userGrowthQuery);
if (!$userGrowthResult) {
    die("Query Error: " . mysqli_error($con) . " - Query: " . $userGrowthQuery);
}
$userGrowth = [];
while ($user = mysqli_fetch_assoc($userGrowthResult)) {
    $userGrowth[] = $user;
}

// Fetch rental order status breakdown
$rentalStatusQuery = "SELECT order_status, COUNT(id) AS count
                      FROM orders GROUP BY order_status";
$rentalStatusResult = mysqli_query($con, $rentalStatusQuery);
if (!$rentalStatusResult) {
    die("Query Error: " . mysqli_error($con) . " - Query: " . $rentalStatusQuery);
}
$rentalStatus = [];
while ($status = mysqli_fetch_assoc($rentalStatusResult)) {
    $rentalStatus[$status['order_status']] = $status['count'];
}
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
    <style>
    body {
    background-color: #fff;
   
}
        #booksChart {
    max-width: 300px !important;
    max-height: 300px !important;
    margin: auto;
}
.scroll-container {
    max-height: calc(110vh - 150px); /* Adjust height to fit the screen */
    overflow-y: auto;
    padding-right: 10px;
}

/* Custom Scrollbar Styling */
.scroll-container::-webkit-scrollbar {
    width: 0px;
}

.scroll-container::-webkit-scrollbar-thumb {
    background-color:#f1f1f1; /* Match your theme */
    border-radius: 4px;
}

.scroll-container::-webkit-scrollbar-track {
    background: #f1f1f1; /* Light track */
}


    #rentalStatusChart{
        max-width: 300px !important;
    max-height: 300px !important;
    margin: auto;

    }
</style>
</head>
<body>

<main class="main-content">
    <div class="container pt-4">
        <h2 class="text-center text-primary">📊 Admin Analytics Report</h2>
        <hr>

        <div class="scroll-container">  <!-- Scrollable Container -->
            <!-- Revenue Trend -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <h5>📈 Revenue Trend (Last 6 Months)</h5>
                    <canvas id="revenueChart"></canvas>
                </div>

                <!-- Most Borrowed Books -->
                <div class="col-md-6">
                    <h5>📚 Top 5 Borrowed Books</h5>
                    <canvas id="booksChart"></canvas>
                </div>
            </div>

            <!-- User Growth & Rental Status -->
            <div class="row mb-5">
                <div class="col-md-6">
                    <h5>👥 User Growth Over Time</h5>
                    <canvas id="userGrowthChart"></canvas>
                </div>

                <div class="col-md-6">
                    <h5>📦 Rental Status Breakdown</h5>
                    <canvas id="rentalStatusChart"></canvas>
                </div>
            </div>

<div class="container pt-4">
      <hr>
           <!-- Print & Export Options -->
           <a href="export_overdue_csv.php" class="btn btn-success">📥 Export Overdue</a>

<!-- Print Button -->
<button onclick="printReport()" class="btn btn-primary">🖨 Print Report</button>

<!-- Rental Orders Report -->
<div id="printArea">  <!-- Start of Print Area -->
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
<!-- Overdue Rentals Report -->
<div id="overduePrintArea">  <!-- New Print Area for Overdue Report -->
    <h5>⏳ Overdue Rentals Report</h5>

    <a href="export_csv.php" class="btn btn-success">📥 Export CSV</a>
<button onclick="printReport('overduePrintArea')" class="btn btn-primary">🖨 Print Overdue Rentals Report</button>

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
            $query = "SELECT u.name AS user, b.name AS book, o.date, 
                      DATEDIFF(NOW(), o.date) AS overdue_days,
                      (DATEDIFF(NOW(), o.date) * 10) AS penalty 
                      FROM orders o
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
</div>  <!-- End of Overdue Print Area -->

        </div> <!-- End of Scrollable Container -->
    </div>
</main>


<script>
document.addEventListener("DOMContentLoaded", function() {
    // Revenue Trend
    var revenueCtx = document.getElementById("revenueChart").getContext("2d");
    new Chart(revenueCtx, {
        type: "bar",
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: "₹ Revenue",
                data: <?php echo json_encode($revenueData); ?>,
                backgroundColor: "#3aafa9"
            }]
        }
    });

    // Most Borrowed Books
    var booksCtx = document.getElementById("booksChart").getContext("2d");
    new Chart(booksCtx, {
        type: "pie",
        data: {
            labels: <?php echo json_encode(array_column($topBooks, 'name')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($topBooks, 'rented_count')); ?>,
                backgroundColor: ["#ff5733", "#33ff57", "#3357ff", "#ff33a8", "#f3ff33"]
            }]
        }
    });

    // User Growth
    var userGrowthCtx = document.getElementById("userGrowthChart").getContext("2d");
    new Chart(userGrowthCtx, {
        type: "line",
        data: {
            labels: <?php echo json_encode(array_column($userGrowth, 'month')); ?>,
            datasets: [{
                label: "New Users",
                data: <?php echo json_encode(array_column($userGrowth, 'count')); ?>,
                borderColor: "#007bff",
                fill: false
            }]
        }
    });

    // Rental Order Status
    var rentalStatusCtx = document.getElementById("rentalStatusChart").getContext("2d");
    new Chart(rentalStatusCtx, {
        type: "doughnut",
        data: {
            labels: ["Completed", "Pending", "Cancelled", "Processing"],
            datasets: [{
                data: [
                    <?php echo $rentalStatus[5] ?? 0; ?>, 
                    <?php echo $rentalStatus[1] ?? 0; ?>, 
                    <?php echo $rentalStatus[3] ?? 0; ?>, 
                    <?php echo $rentalStatus[2] ?? 0; ?>
                ],
                backgroundColor: ["#28a745", "#ffc107", "#dc3545", "#17a2b8"]
            }]
        }
    });
});
</script>

<script src="js/mdb.min.js"></script>

<pre>
<?php
echo "<strong>Revenue Labels:</strong> ";
print_r($labels);

echo "<strong>Revenue Data:</strong> ";
print_r($revenueData);

echo "<strong>Top 5 Borrowed Books:</strong> ";
print_r($topBooks);

echo "<strong>User Growth Data:</strong> ";
print_r($userGrowth);

echo "<strong>Rental Status Breakdown:</strong> ";
print_r($rentalStatus);
?>
</pre>
<script>
function printReport(printAreaId) {
    var printContents = document.getElementById(printAreaId).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = "<html><head><title>Print Report</title></head><body>" + printContents + "</body></html>";

    window.print();

    document.body.innerHTML = originalContents;
    location.reload(); // Reload to restore page after printing
}
</script>


</main>
</body>
</html>
