<?php
require('topNav.php');

if (isset($_GET['type']) && $_GET['type'] != ' ') {
    $type = getSafeValue($con, $_GET['type']);
    $id = getSafeValue($con, $_GET['id']);

    if ($type == 'status') {
        $operation = getSafeValue($con, $_GET['operation']);
        $status = ($operation == 'active') ? '1' : '0';
        $updateStatusSql = "UPDATE categories SET status='$status' WHERE id='$id'";
        mysqli_query($con, $updateStatusSql);
    }

    if ($type == 'delete') {
        $deleteSql = "DELETE FROM categories WHERE id='$id'";
        mysqli_query($con, $deleteSql);
    }
}

$sql = "SELECT * FROM categories ORDER BY category ASC";
$res = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="css/mdb.min.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/new.css">


    <style>

    </style>
</head>
<body>
<!-- Main Layout -->
<main class="main-content">
<div class="scroll-container ">

    <div class="container pt-4">
        <h2 class="text-center text-primary">Book Categories Management</h2>
        <hr>
        <div class="text-end mb-4">
        <a class="btn btn-success" href="manageCategories.php">
    <i class="fas fa-plus-circle"></i> Add Category
</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover text-center shadow-lg">
                <thead class="table-dark">
                    <tr>
                        <th>Category Name</th>
                        <th>Status</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td class="align-middle"><?php echo $row['category'] ?></td>
                            <td class="align-middle">
    <?php if ($row['status'] == 1) { ?>
        <a class='btn btn-success' href='?type=status&operation=deactive&id=<?php echo $row['id']; ?>'>
            <i class="fas fa-check-circle"></i> Active
        </a>
    <?php } else { ?>
        <a class='btn btn-warning' href='?type=status&operation=active&id=<?php echo $row['id']; ?>'>
            <i class="fas fa-times-circle"></i> Inactive
        </a>
    <?php } ?>
</td>
<td class="align-middle">
    <a class="btn btn-primary" href="manageCategories.php?id=<?php echo $row['id']; ?>">
        <i class="fas fa-edit"></i> Edit
    </a>
</td>
<td class="align-middle">
    <a class="btn btn-danger" href="?type=delete&id=<?php echo $row['id']; ?>">
        <i class="fas fa-trash-alt"></i> Delete
    </a>
</td>

                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div> <!-- End of table-responsive -->
    </div>
    </div>

</main>

<!-- MDB Scripts -->
<script src="js/mdb.min.js"></script>
<script src="js/admin.js"></script>
</body>
</html>
