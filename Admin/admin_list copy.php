<?php
require('topNav.php');
$sql = "SELECT * FROM admin ORDER BY id DESC";
$res = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/new.css">

    <title>Document</title>
    <!-- <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
        }
        /* Sidebar styles */
        .sidebar {
            width: 250px;
            background-color: #333;
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            margin: 1px 0;
        }
        /* Main content styles */
        .main-content {
            margin-left: 250px;
            align-items: center;
            padding: 20px;
            flex: 1;
            overflow-y: auto;
        }
    </style> -->
</head>
<body>
    
</body>
</html>

<main class="main-content">
<div class="scroll-container ">

    <div class="container pt-4">

        <div class="table-responsive">
            <table class="table table-striped table-hover text-center shadow-lg">
        <h2 class="text-center text-primary">Admin Management</h2>

            <hr>
        <div class="text-end mb-4">
            <a class="btn btn-success" href="manage_admin.php">    <i class="fas fa-plus-circle"></i> Add Admin</a>
        </div>

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

                                <a class="btn btn-warning px-3 py-1" href="manage_admin.php?action=edit&id=<?php echo $row['id']; ?>">   <i class="fas fa-edit"></i> EditEdit</a>
                            <a class="btn btn-danger px-3 py-1" href="manage_admin.php?action=delete&id=<?php echo $row['id']; ?>" 
   onclick="return confirm('Are you sure you want to delete this admin?');"> <i class="fas fa-trash-alt"></i> Delete</a>
 </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
                    </div>
</main>
                        