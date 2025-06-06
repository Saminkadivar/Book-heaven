<?php
require('topNav.php');
require 'connection.php';


// Initialize variables
$id = '';  
$category_id = '';
$ISBN = '';
$name = '';
$author = '';
$mrp = '';
$security = '';
$rent = '';
$qty = '';
$img = '';
$short_desc = '';
$description = '';
$error = '';

if (isset($_GET['id']) && $_GET['id'] != '') {
    $id = getSafeValue($con, $_GET['id']);
    $res = mysqli_query($con, "SELECT * FROM books WHERE id='$id'");
    if ($row = mysqli_fetch_assoc($res)) {
        $category_id = $row['category_id'];
        $ISBN = $row['ISBN'];
        $name = $row['name'];
        $author = $row['author'];
        $mrp = $row['mrp'];
        $security = $row['security'];
        $rent = $row['rent'];
        $qty = $row['qty'];
        $img = $row['img'];
        $short_desc = $row['short_desc'];
        $description = $row['description'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = getSafeValue($con, $_POST['category_id']);
    $ISBN = getSafeValue($con, $_POST['ISBN']);
    $name = getSafeValue($con, $_POST['name']);
    $author = getSafeValue($con, $_POST['author']);
    $mrp = getSafeValue($con, $_POST['mrp']);
    $security = getSafeValue($con, $_POST['security']);
    $rent = getSafeValue($con, $_POST['rent']);
    $qty = getSafeValue($con, $_POST['qty']);
    $short_desc = getSafeValue($con, $_POST['short_desc']);
    $description = getSafeValue($con, $_POST['description']);

    // Handle image upload
    if (!empty($_FILES['img']['name'])) {
        $imgName = $_FILES['img']['name'];
        $imgPath = "../img/books/" . $imgName;
        move_uploaded_file($_FILES['img']['tmp_name'], $imgPath);
        $img = $imgName;
    }

    if ($id != '') {
        // Update existing record
        $sql = "UPDATE books SET 
                  category_id='$category_id', ISBN='$ISBN', name='$name', author='$author',
                  mrp='$mrp', security='$security', rent='$rent', qty='$qty', img='$img', short_desc='$short_desc', description='$description'
                WHERE id='$id'";
    } else {
        // Insert new record
        $sql = "INSERT INTO books (category_id, ISBN, name, author, mrp, security, rent, qty, img, short_desc, description, status) 
                VALUES ('$category_id', '$ISBN', '$name', '$author', '$mrp', '$security', '$rent', '$qty', '$img', '$short_desc', '$description', 1)";
    }

    if (mysqli_query($con, $sql)) {
        echo "<script>window.location.href='books.php';</script>";
    } else {
        $error = "Error in adding/updating book.";
    }
}
?>

<style>
 
    body {
        display: flex;
        min-height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        overflow-y: auto;
 
    }

    main {
        flex: 1;
        margin-left: 250px;
        padding: 20px;
        display: 100%;
        justify-content: center;
        align-items: center;
    }

    .container {
        max-width: 800px;
        width: 100%;
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }

    label { font-weight: bold; }
    .form-control { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px; }
    .btn-primary { background-color: #34495e; border: none; padding: 10px 20px; border-radius: 5px; }
    .btn-primary:hover { background-color: #2c3e50; }
    img { margin-top: 10px; max-width: 100px; border-radius: 5px; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2); }
</style>

</style>

<body>


<!-- Main Content -->
<main>
    <div class="container">
        <h4 class="text-center">Manage Book</h4>
        <hr>
        <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <div class="form-container">
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" class="form-control">
                        <option value="">Select Category</option>
                        <?php
                        $categories = mysqli_query($con, "SELECT * FROM categories ORDER BY category ASC");
                        while ($cat = mysqli_fetch_assoc($categories)) {
                            $selected = ($cat['id'] == $category_id) ? 'selected' : '';
                            echo "<option value='{$cat['id']}' $selected>{$cat['category']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>ISBN</label>
                    <input type="text" name="ISBN" class="form-control" value="<?php echo $ISBN; ?>" required>
                </div>
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                </div>
                <div class="form-group">
                    <label>Author</label>
                    <input type="text" name="author" class="form-control" value="<?php echo $author; ?>" required>
                </div>
                <div class="form-group">
                    <label>MRP</label>
                    <input type="number" step="0.01" name="mrp" class="form-control" value="<?php echo $mrp; ?>" required>
                </div>
                <div class="form-group">
                    <label>Security</label>
                    <input type="number" step="0.01" name="security" class="form-control" value="<?php echo $security; ?>" required>
                </div>
                <div class="form-group">
                    <label>Rent</label>
                    <input type="number" step="0.01" name="rent" class="form-control" value="<?php echo $rent; ?>" required>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" name="qty" class="form-control" value="<?php echo $qty; ?>" required>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="img" class="form-control">
                    <?php if ($img) echo "<img src='../img/books/$img' alt='Book Image'>"; ?>
                </div>
                <div class="form-group">
                    <label>Short Description</label>
                    <input type="text" name="short_desc" class="form-control" value="<?php echo $short_desc; ?>" required>
                </div>  

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" required><?php echo $description; ?></textarea>
                </div> 
                
                <div class="text-center">
                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</main>

</body>
