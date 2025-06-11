<?php
// Check if the constant is already defined
if (!defined('BOOK_IMAGE_SITE_PATH')) {
    define('BOOK_IMAGE_SITE_PATH', 'img/books/');
}
require('header.php');
$defaultImg = 'images/default-book.png';
?>

<!--------------------------------------------NEW ARRIVALS CONTAINER------------------------------------------------------->
<div class="container mb-5 mt-5">
    <h2 class="fs-2 fw-bold text-center"> New Arrivals</h2>
    <hr />
    <div class="row gy-3 text-center ">
        <?php
        $orderBy = 'id desc';
        $getProduct = getProduct($con, 4, '', '', $orderBy);
        foreach ($getProduct as $list) {
            $img = BOOK_IMAGE_SITE_PATH . $list['img'];
        ?>
       <div class="col-6 col-md-4 col-lg-3">
    <div class="card mt-3 product">
        <img id="card-img" alt="Book Image" src="<?php echo $img ?>" class="card-img-top" />
        <div class="overlay">
            <a href="book.php?id=<?php echo $list['id'] ?>" class="btn rent-btn">
                Rent
            </a>
        </div>
    </div>
    <div id="bookCardName">
        <a href="book.php?id=<?php echo $list['id'] ?>"       
         class="card-text text-uppercase text-break fw-bold text-decoration-none">
        
            <?php echo $list['name'] ?>
        </a>
        <p class="card-text">Price- ₹<?php echo $list['rent'] ?> Per day</p>
    </div>
</div>

        <?php } ?>
    </div>
</div>

<!--------------------------------------------MOST VIEWED CONTAINER-------------------------------------------------------->
<div class="container mb-5 mt-5">
    <h2 class="fs-2 fw-bold text-center">Most Viewed</h2>
    <hr />
    <div class="row gy-3 text-center ">
        <?php
        function getBook($con)
        {
            $sql = "select *from books where best_seller=1 limit 8";
            $res = mysqli_query($con, $sql);
            $data = array();
            while ($row = mysqli_fetch_assoc($res)) {
                $data[] = $row;
            }
            return $data;
        }

        $getBook = getBook($con);
        foreach ($getBook as $list) {
            $img = BOOK_IMAGE_SITE_PATH . $list['img'];
        ?>
       <div class="col-6 col-md-4 col-lg-3">
            <div class=" card border-dark mt-3 shadow-sm product">
                <img id="card-img" alt="Book Image" src="<?php echo $img ?>" class="card-img-top rounded"
                    height="396rem" width="260rem" />
                <div class="overlay">
                    <a href="book.php?id=<?php echo $list['id'] ?>" class="btn-lg text-decoration-none rent-btn btn-primary ">
                        Rent</a>
                </div>
            </div>
            <div id="bookCardName">
                <a href="book.php?id=<?php echo $list['id'] ?>"
                    class="card-text text-uppercase text-break fw-bold text-decoration-none">
                    <?php echo $list['name'] ?>
                </a>
                <p class="card-text">Price- ₹<?php echo $list['rent'] ?> Per day</p>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
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
    height: 380px;
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

/* Rent Button */
.rent-btn {
    background-color: #34495e;
    color: #ffffff;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 25px;
    box-shadow: 2px 2px 15px rgba(0, 0, 0, 0.2);
    text-transform: uppercase;
    transition: background 0.3s ease-in-out;
}

.rent-btn:hover {
    background-color: #fff;
    color: #34495e;
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
<?php require('footer.php') ?>
