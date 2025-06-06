<?php

require 'connection.php';

$user_id = $_SESSION['USER_LOGIN']; // User must be logged in
$query = "SELECT * FROM orders WHERE user_id = ? AND status = 'Rented'";
$stmt = $con->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Return Book</title>
</head>
<body>
    <h2>Return a Book</h2>
    <form action="return_book.php" method="POST">
        <label>Select a book to return:</label>
        <select name="rental_id" required>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <option value="<?= $row['rental_id'] ?>">
                    <?= "Book ID: " . $row['book_id'] . " - Due: " . $row['due_date'] ?>
                </option>
            <?php } ?>
        </select>
        <button type="submit" name="return_book">Return</button>
    </form>
</body>
</html>
