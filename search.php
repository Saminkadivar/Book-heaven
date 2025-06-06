<?php require('config.php') ?>
<?php require('header.php') ?>

<?php
$search = mysqli_real_escape_string($con, $_GET['search']);
function getBook($con)
{
  $search = mysqli_real_escape_string($con, $_GET['search']);
  $sql = "SELECT * FROM books WHERE (`name` LIKE '%$search%') OR (`author` LIKE '%$search%')";
  $res = mysqli_query($con, $sql);
  $data = array();
  while ($row = mysqli_fetch_assoc($res)) {
    $data[] = $row;
  }
  return $data;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/Style.css" />
  
  <title>Document</title>
  <style>
.card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease-in-out;
    position: relative;
}

.card:hover {
    transform: scale(1.05);
}

.card-img-top {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    /* height: 380px; */
    object-fit: cover;
    transition: opacity 0.3s ease-in-out;
}

.card:hover .card-img-top {
    opacity: 0.8;
}

/* Overlay Effect */
.overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    border-radius: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    visibility: hidden;
}

.card:hover .overlay {
    visibility: visible;
    animation: fade 0.5s;
}
/* Rent Button - Smaller & Compact */
.rent-btn {
    background-color: #34495e;
    color: #ffffff;
    font-weight: bold;
    padding: 6px 14px;  /* Reduced padding for a smaller size */
    font-size: 0.85rem;  /* Smaller text size */
    border-radius: 20px; /* More compact shape */
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.15);
    text-transform: uppercase;
    transition: background 0.3s ease-in-out, transform 0.2s ease-in-out;
}

/* Hover Effect */
.rent-btn:hover {
    background-color: #f1c40f;
    color: #34495e;
    transform: scale(1.05);
}


/* Book Title & Price */
#bookCardName {
    padding: 15px;
    background: #ffffff;
    text-align: center;
    text-transform: uppercase;
    border-bottom-left-radius: 15px;
    border-bottom-right-radius: 15px;
}

#bookCardName a {
    font-size: 1.1rem;
    color: #2c3e50;
    font-weight: bold;
    transition: color 0.3s ease-in-out;
}

#bookCardName a:hover {
    color: #f1c40f;
}

.card-text {
    color: #7f8c8d;
    font-size: 1rem;
}

/* Smooth Fade Animation */
@keyframes fade {
    0% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}
    </style>
</head>
<body>
  
</body>
</html>
<script>
document.title = "Book Categories | Book Heaven";
</script>
<main class="px-4 row py-3 container-fluid">
    <div class="d-flex justify-content-center flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 border-bottom">
        <h1 class="h2">Searched Books</h1>
    </div>
    <?php
  $getBook = getBook($con);
  if (count($getBook) > 0) {
  ?>
    <div class="row gy-3 text-center ">
        <?php
      foreach ($getBook as $list) {
      ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div class="card border-dark  shadow-sm product">
                <img id="card-img" alt="Book Image" src="<?php echo BOOK_IMAGE_SITE_PATH . $list['img'] ?>"
                    class="card-img-top" height="350rem" />
                <div class="overlay">
                    <a href="book.php?id=<?php echo $list['id'] ?>" class="btn-lg text-decoration-none rent-btn">
                        Rent</a>
                </div>
            </div>
            <div id="bookCardName">
                <a href="book.php?id=<?php echo $list['id'] ?>"
                    class="card-text text-uppercase text-break fw-bold text-decoration-none">
                    <?php echo $list['name'] ?>
                </a>
                <p class="card-text text-break"><strong>Author</strong> - <?php echo $list['author'] ?></p>
            </div>
        </div>
        <?php } ?>
    </div>
    <?php } else {
    echo "No Book Found";
  } ?>
</main>
<?php require('footer.php') ?>