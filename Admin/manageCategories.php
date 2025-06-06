<?php
require('topNav.php');
$res = '';
$categories = '';
$msg = '';

if (isset($_GET['id']) && $_GET['id'] != '') {
  $id = getSafeValue($con, $_GET['id']);
  $sql = mysqli_query($con, "select * from categories where id='$id'");
  $check = mysqli_num_rows($sql);
  if ($check > 0) {
    $row = mysqli_fetch_assoc($sql);
    $categories = $row['category'];
  } else {
    echo "<script>window.location.href='categories.php';</script>";
    exit;
  }
}

if (isset($_POST['submit'])) {
  $category = getSafeValue($con, $_POST['category']);
  $sql = mysqli_query($con, "select * from categories where category='$category'");
  $check = mysqli_num_rows($sql);
  if ($check > 0) {
    if (isset($_GET['id']) && $_GET['id'] != '') {
      $getData = mysqli_fetch_assoc($sql);
      if ($id == $getData['id']) {
      } else {
        $msg = "Category already exist";
      }
    } else {
      $msg = "Category already exist";
    }
  }
  if ($msg == '') {
    if (isset($_GET['id']) && $_GET['id'] != '') {
      $sql = "update categories set category='$category' where id='$id' ";
    } else {
      $sql = "insert into categories(category, status) values('$category', '1')";
    }
    if (mysqli_query($con, $sql)) {
      echo "<script>window.location.href='categories.php';</script>";
      exit;
    } else {
      $res = "Error";
    }
  }
}

?>
<style>
    body {
        display: flex;
        min-height: 100vh;
        margin: 0;
    }
 

    /* Main Content */
    main {
        flex: 1;
        margin-left: 250px; /* Adjust based on sidebar width */
        padding: 20px;
        display: flex;
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

    .btn-primary {
        background-color: #34495e;
        border: none;
    }

    .btn-primary:hover {
        background-color: #34495e;
    }
</style>

<body>



<!-- Main Content -->
<main>
    <div class="container">
        <h4 class="fs-2 text-center">Manage Category</h4>
        <hr>
        <form method="post">
            <div class="form-outline mb-4">
                <input type="text" name="category" value="<?php echo $categories ?>" id="category" class="form-control" required />
                <label class="form-label" for="category">Enter category name</label>
            </div>
            <div class="mb-1 d-flex justify-content-center field_error">
                <?php echo $msg ?>
            </div>
            <div class="mb-1 d-flex justify-content-center">
                <?php echo $res ?>
            </div>
            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary mx-5">Submit</button>
            </div>
        </form>
    </div>
</main>


<!-- MDB -->
<script type="text/javascript" src="js/mdb.min.js"></script>
<!-- Custom scripts -->
<script type="text/javascript" src="js/admin.js"></script>
</body>

</html>
<!-- <style>


   

    .container {
        max-width: 1000px;  /* Limits the width to 1200px */
        width: 100%;
    }

    .card {
        width: 100%;
    }

   
    </style> -->