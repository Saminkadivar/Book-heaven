<?php
require('topNav.php');

// Redirect to login if user is not logged in
if (!isset($_SESSION['ADMIN_LOGIN'])) {
    header('Location: login.php');
    exit();
}

include('connection.php'); // Ensure database connection

$sql = "SELECT * FROM admin ORDER BY id DESC";
$res = mysqli_query($con, $sql);

$adminRole = $_SESSION['ADMIN_ROLE']; // Get logged-in admin role
$adminEmail = $_SESSION['ADMIN_EMAIL']; // Get logged-in admin email
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
    <link rel="stylesheet" href="css/new.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<main class="main-content">
    <div class="container pt-4">
        <h2 class="text-center text-primary">Admin Management</h2>
        <hr>

        <!-- Show Add Admin Button Only for Super Admin -->
        <div class="text-end mb-4">
            <?php if ($adminRole == 'Super Admin') { ?>
                <a class="btn btn-success" href="manage_admin.php">
                    <i class="fas fa-plus-circle"></i> Add Admin
                </a>
            <?php } ?>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover text-center shadow-lg">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td class="align-middle"><?php echo $row['id']; ?></td>
                            <td class="align-middle"><?php echo $row['name']; ?></td>
                            <td class="align-middle"><?php echo $row['email']; ?></td>
                            <td class="align-middle"><?php echo $row['role']; ?></td>
                            <td class="align-middle"><?php echo $row['created_at']; ?></td>
                            <td class="align-middle">
                                <?php 
                                $targetRole = $row['role'];
                                $targetEmail = $row['email'];
                                ?>

                                <!-- Edit Button -->
                                <?php if (
                                    $adminRole == 'Super Admin' || 
                                    ($adminRole == 'Admin' && $targetRole == 'manager') || 
                                    ($adminEmail == $targetEmail) // Can edit self
                                ) { ?>
                                    <a class="btn btn-warning px-3 py-1" href="manage_admin.php?action=edit&id=<?php echo $row['id']; ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                <?php } ?>

                                <!-- Delete Button -->
                                <?php if (
                                    $adminRole == 'Super Admin' || 
                                    ($adminRole == 'Admin' && $targetRole == 'manager') // Admin can delete only Managers
                                ) { ?>
                                    <a class="btn btn-danger px-3 py-1" href="manage_admin.php?action=delete&id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this manager?');">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                <?php } ?>
                                
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

</body>
</html>
