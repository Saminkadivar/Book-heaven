<?php
// Include database connection
require 'connection.php';
require 'topNav.php';

if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch the role of the admin being deleted
    $sql = "SELECT role FROM admin WHERE id = $id";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['role'] == 'Super Admin') {
        // Prevent deletion
        echo "<script>alert('Super Admin cannot be deleted!'); window.location.href='admin_list.php';</script>";
        exit;
    } else {
        // Proceed with deletion
        $delete_sql = "DELETE FROM admin WHERE id = $id";
        if (mysqli_query($con, $delete_sql)) {
            echo "<script>alert('Admin deleted successfully!'); window.location.href='admin_list.php';</script>";
        } else {
            echo "<script>alert('Error deleting admin.'); window.location.href='admin_list.php';</script>";
        }
    }
}

$message = "";

// Check if we're adding or editing
$action = isset($_GET['action']) ? $_GET['action'] : '';
$editMode = ($action == 'edit' && isset($_GET['id']));
$adminData = null;

// Fetch Admin Data for Edit Mode
if ($editMode) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM admin WHERE id = $id";
    $result = mysqli_query($con, $sql);
    $adminData = mysqli_fetch_assoc($result);
}

// Handle Add Admin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    if ($_POST['action'] == 'add') {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing
        $sql = "INSERT INTO admin (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        if (mysqli_query($con, $sql)) {
            header("Location: admin_list.php?message=Admin added successfully");
            exit();
        } else {
            $message = "Error: " . mysqli_error($con);
        }
    }

    // Handle Edit Admin
    if ($_POST['action'] == 'edit') {
        $id = $_POST['id'];
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $sql = "UPDATE admin SET name='$name', email='$email', password='$password', role='$role' WHERE id=$id";
        } else {
            $sql = "UPDATE admin SET name='$name', email='$email', role='$role' WHERE id=$id";
        }

        if (mysqli_query($con, $sql)) {
            header("Location: admin_list.php?message=Admin updated successfully");
            exit();
        } else {
            $message = "Error: " . mysqli_error($con);
        }
    }
}

?>
<?php


// Handle Delete Admin
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete Query
    $sql = "DELETE FROM admin WHERE id=$id";
    
    if (mysqli_query($con, $sql)) {
        header("Location: admin_list.php?message=Admin deleted successfully");
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? "Edit Admin" : "Add Admin"; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
       body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .form-container h2 {
            text-align: center;
            color: #34495e;
            margin-bottom: 20px;
        }

        .form-container label {
            font-weight: bold;
            margin-top: 10px;
        }

        .form-control {
            border-radius: 5px;
            box-shadow: none;
            border: 1px solid #ced4da;
        }

        .btn-success {
            background-color: #34495e;
            border: none;
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .btn-success:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2><?php echo $editMode ? "Edit Admin" : "Add Admin"; ?></h2>
    <form method="POST">
        <input type="hidden" name="action" value="<?php echo $editMode ? 'edit' : 'add'; ?>">
        <?php if ($editMode) { ?>
            <input type="hidden" name="id" value="<?php echo $adminData['id']; ?>">
        <?php } ?>
        
        <label>Name:</label>
        <input type="text" name="name" class="form-control" value="<?php echo $editMode ? $adminData['name'] : ''; ?>" required>
        
        <label>Email:</label>
        <input type="email" name="email" class="form-control" value="<?php echo $editMode ? $adminData['email'] : ''; ?>" required>
        
        <label>Password (<?php echo $editMode ? "Leave blank to keep existing" : "Required"; ?>):</label>
        <input type="password" name="password" class="form-control" <?php echo $editMode ? "" : "required"; ?>>
        <label>Role:</label>
<?php if ($_SESSION['ADMIN_ROLE'] == 'Super Admin') { ?>
    <select name="role" class="form-control" required>
        <option value="Super Admin" <?php echo ($editMode && isset($adminData['role']) && $adminData['role'] == 'Super Admin') ? 'selected' : ''; ?>>Super Admin</option>
        <option value="Admin" <?php echo ($editMode && isset($adminData['role']) && $adminData['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
        <option value="manager" <?php echo ($editMode && isset($adminData['role']) && $adminData['role'] == 'manager') ? 'selected' : ''; ?>>Manager</option>
    </select>
<?php } else { ?>
    <input type="text" class="form-control" value="<?php echo $editMode && isset($adminData['role']) ? $adminData['role'] : 'manager'; ?>" readonly>
    <input type="hidden" name="role" value="<?php echo $editMode && isset($adminData['role']) ? $adminData['role'] : 'manager'; ?>">
<?php } ?>

        <br>
        <button type="submit" class="btn btn-success"><?php echo $editMode ? "Update Admin" : "Add Admin"; ?></button>
    </form>
</div>


</body>
</html>



