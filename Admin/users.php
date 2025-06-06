<?php
require('topNav.php');

if (isset($_GET['type']) && $_GET['type'] != '') {
    $type = getSafeValue($con, $_GET['type']);

    if ($type == 'delete') {
        $id = getSafeValue($con, $_GET['id']);
        $deleteSql = "DELETE FROM users WHERE id='$id'";
        mysqli_query($con, $deleteSql);
    }
}

$sql = "SELECT * FROM users ORDER BY id DESC";
$res = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/new.css" />

</head>
<body>
    
</body>
</html>
<!-- Main layout -->
<main class="main-content"><div class="scroll-container ">

    <div class="container pt-4">
        <h2 class="text-center text-primary">Users Management</h2>
        <hr>
        <br>

        <!-- Scrollable Table Wrapper -->
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center shadow-lg">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Date of Joining</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td class="align-middle"><?php echo $row['id']; ?></td>
                            <td class="align-middle"><?php echo $row['name']; ?></td>
                            <td class="align-middle"><?php echo $row['email']; ?></td>
                            <td class="align-middle"><?php echo $row['mobile']; ?></td>
                            <td class="align-middle"><?php echo $row['doj']; ?></td>
                            <td class="align-middle">
                                <a class="btn btn-danger px-3 py-1" href="?type=delete&id=<?php echo $row['id']; ?>"> <i class="fas fa-trash-alt"></i> DELETE</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
                    </div>
</main>

<!-- MDB -->
<script type="text/javascript" src="js/mdb.min.js"></script>
<!-- Custom scripts -->
<script type="text/javascript" src="js/admin.js"></script>


</body>
</html>
