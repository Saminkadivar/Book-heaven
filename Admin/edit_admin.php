<?php
require 'connection.php';

if (isset($_GET['id'])) {
    $admin_id = $_GET['id'];
    $query = "SELECT * FROM admin WHERE admin_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_POST['admin_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE admin SET name=?, email=?, role=? WHERE admin_id=?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("sssi", $name, $email, $role, $admin_id);

    if ($stmt->execute()) {
        echo "Admin updated successfully!";
    } else {
        echo "Error: " . $con->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Admin</title>
</head>
<body>
    <h2>Edit Admin</h2>
    <form method="POST">
        <input type="hidden" name="admin_id" value="<?= $admin['admin_id'] ?>">
        <label>Name:</label>
        <input type="text" name="name" value="<?= $admin['name'] ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?= $admin['email'] ?>" required>
        <label>Role:</label>
        <select name="role">
            <option value="Admin" <?= $admin['role'] == 'Admin' ? 'selected' : '' ?>>Admin</option>
            <option value="Super Admin" <?= $admin['role'] == 'Super Admin' ? 'selected' : '' ?>>Super Admin</option>
        </select>
        <button type="submit">Update</button>
    </form>
</body>
</html>
