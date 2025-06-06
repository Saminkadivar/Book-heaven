<?php
// Check if the constant is already defined
if (!defined('BOOK_IMAGE_SITE_PATH')) {
    define('BOOK_IMAGE_SITE_PATH', '../img/books/');
}

require('topNav.php');

if (isset($_GET['type']) && $_GET['type'] != '') {
    $type = getSafeValue($con, $_GET['type']);
    if ($type == 'best_seller') {
        $operation = getSafeValue($con, $_GET['operation']);
        $id = getSafeValue($con, $_GET['id']);
        $bestSeller = ($operation == 'active') ? '1' : '0';
        $updateStatusSql = "UPDATE books SET best_seller='$bestSeller' WHERE id='$id'";
        mysqli_query($con, $updateStatusSql);
    }

    if ($type == 'delete') {
        $id = getSafeValue($con, $_GET['id']);
        $deleteSql = "DELETE FROM books WHERE id='$id'";
        mysqli_query($con, $deleteSql);
    }
}

$sql = "SELECT books.*, categories.category 
        FROM books 
        LEFT JOIN categories ON books.category_id = categories.id 
        ORDER BY books.id DESC";  // Sorting by ID in descending order to show the newest books first
$res = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/new.css">

</head>
<body>
<div class="scroll-container ">


<main class="main-content">
    <div class="container pt-4">
        <h4 class="fs-2 text-center text-primary">Books Management</h4>
        <hr>
        <div class="text-end">
        <a class="btn btn-success" href="manageBooks.php" 
   data-bs-toggle="tooltip" data-bs-placement="top" title="Add a New Book">
    <i class="fas fa-plus-circle"></i> Add Book
</a>

        </div>
        <div class="table-responsive mt-4">
            <table class="table table-striped align-middle text-center" style="table-layout: fixed;">
                <thead class="table-dark">
                    <tr>
                        <th>ISBN</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Author</th>
                        <th>MRP</th>
                        <th>Security</th>
                        <th>Rent</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Best Seller</th>
                        <th>Edit</th>
                        <th>Delete</th>

                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)) { ?>
                        <tr>
                            <td><?php echo $row['ISBN']; ?></td>
                            <td><?php echo $row['category']; ?></td>
                            <td>
                                <img src="<?php echo BOOK_IMAGE_SITE_PATH . $row['img']; ?>" class="rounded" height="100" width="80">
                            </td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['author']; ?></td>
                            <td>₹<?php echo $row['mrp']; ?></td>
                            <td>₹<?php echo $row['security']; ?></td>
                            <td>₹<?php echo $row['rent']; ?></td>
                            <td>₹<?php echo $row['price']; ?></td>
                            <td><?php echo $row['qty']; ?></td>
                            <td>
                                <?php echo ($row['best_seller'] == 1) ? 
                                    "<a href='?type=best_seller&operation=deactive&id={$row['id']}' class='btn btn-primary btn-sm'>
                                        <i class='fas fa-eye'></i> Most Viewed
                                    </a>" : 
                                    "<a href='?type=best_seller&operation=active&id={$row['id']}' class='btn btn-warning btn-sm'>
                                        <i class='fas fa-eye-slash'></i> Normal
                                    </a>"; 
                                ?>
                            </td>
                            <td>
                                <a href="manageBooks.php?id=<?php echo $row['id']; ?>" class="btn btn-info ">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                            <td>
                                <a href="?type=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Delete    
                                 </a>
                                
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</div>

</body>

</html>
<!-- MDB -->
<script type="text/javascript" src="js/mdb.min.js"></script>
<!-- Custom scripts -->
<script type="text/javascript" src="js/admin.js"></script>
<style>



</style>