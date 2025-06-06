<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "mini_project");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['USER_LOGIN'])) {
    echo "<script>window.top.location='SignIn.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $book_id = $_POST['book_id'];
    $order_id = $_POST['order_id'];
    $return_date = date("Y-m-d");

    // Fetch order details
    $order_query = "SELECT duration, date FROM orders WHERE id = '$order_id' AND user_id = '$user_id'";
    $order_result = mysqli_query($conn, $order_query);
    $order = mysqli_fetch_assoc($order_result);

    if (!$order) {
        die("Invalid order selection.");
    }

    $due_date = date("Y-m-d", strtotime($order['date'] . " +{$order['duration']} days"));
    $late_fee = (strtotime($return_date) > strtotime($due_date)) ? 50.00 : 0.00; // Example late fee
    
    // Insert return request
    $sql = "INSERT INTO rental_returns (user_id, book_id, order_id, return_date, late_fee, status) 
            VALUES ('$user_id', '$book_id', '$order_id', '$return_date', '$late_fee', 'Pending')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Return request submitted successfully.";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Return a Book</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Return a Book</h2>
        <form action="user_return.php" method="POST" onsubmit="return validateForm()">
            <label>Select Book:</label>
            <select id="book_id" name="book_id" class="form-control">
                <option value="">-- Select a Book --</option>
                <?php
                $books = mysqli_query($conn, "SELECT b.id, b.name FROM books b JOIN orders o ON b.id = o.book_id WHERE o.user_id = '$user_id'");
                while ($row = mysqli_fetch_assoc($books)) {
                    echo "<option value='{$row['id']}'>{$row['name']}</option>";
                }
                ?>
            </select>
            
            <label>Select Order:</label>
            <select id="order_id" name="order_id" class="form-control">
                <option value="">-- Select an Order --</option>
                <?php
                $orders = mysqli_query($conn, "SELECT id FROM orders WHERE user_id = '$user_id'");
                while ($row = mysqli_fetch_assoc($orders)) {
                    echo "<option value='{$row['id']}'>Order #{$row['id']}</option>";
                }
                ?>
            </select>

            <button type="submit" class="btn btn-primary mt-3">Submit Return Request</button>
        </form>
    </div>

    <script>
    function validateForm() {
        let bookId = document.getElementById("book_id").value;
        let orderId = document.getElementById("order_id").value;
        
        if (bookId === "" || orderId === "") {
            alert("Please select a book and order.");
            return false;
        }
        return true;
    }
    </script>
</body>
</html>
