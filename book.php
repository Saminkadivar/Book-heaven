<?php 
require('config.php');
require('header.php'); ?>
<?php
$bookId = '';
if (isset($_GET['id'])) {
    $bookId = mysqli_real_escape_string($con, $_GET['id']);
}
$getProduct = getProduct($con, '', '', $bookId);

if (isset($_GET['submit'])) {
    $duration = getSafeValue($con, $_GET['duration']);
    $id = getSafeValue($con, $_GET['bookId']);
    $_SESSION['BeforeCheckoutLogin'] = 'checkout.php?id=' . $id . '&duration=' . $duration;
?>
<script>
window.top.location = "checkout.php?id=<?php echo $id ?>&duration=<?php echo $duration ?>";
</script>
<?php
}
?>
<script>
document.title = "<?php echo $getProduct['0']['name'] ?> | Book heaven";
</script>
<div class="container-fluid py-5">
    <div class="row mb-3">
        <div class="col-6 col-sm-6 col-md-3 mt-3">
            <img class="card"
                src="<?php echo BOOK_IMAGE_SITE_PATH . $getProduct['0']['img'] ?>" height="500rem" width="360rem">
        </div>
        <div class="col-12 col-md-9">
            <h2 id="bookName" class="text-uppercase font-weight-bold"><?php echo $getProduct['0']['name'] ?></h2>
            <hr>
            <h6>ISBN:- <span class="fw-normal"><?php echo $getProduct['0']['ISBN'] ?></span></h6>
            <h6>Author:- <span class="fw-normal"><?php echo $getProduct['0']['author'] ?></span></h6>
            <p></p>
            <p>
                <span class="fs-4 fw-bolder">₹<?php echo $getProduct['0']['rent'] ?></span><span
                    class="fs-5"><strong>(Per Day)</strong></span>
            </p>

            <?php
            $qtySql = mysqli_query($con, "select qty from books where id='$bookId'");
            $row = mysqli_fetch_assoc($qtySql);
            $qtyArr = array();
            $qtyArr[] = $row;
            if ($qtyArr['0']['qty'] == 0) {
                echo '<p class="fs-4" style="color: #ff0000">Sorry currently the book is out of stock</p>';
            } else {
                echo '<button id="toggle" class="rent-btn " onclick="showDiv()">Rent</button>';
            }
            ?>

            <script>
            function showDiv() {
                document.getElementById("after-rent").style.display = "block";
                document.getElementById("toggle").style.display = "none";
            }
            </script>
            <div id="after-rent" class="mb-4">
                <form method="get">
                    <input type="hidden" name="bookId" value="<?php echo $getProduct['0']['id'] ?>">
                    <h4 class="mb-3">Enter the duration of renting(in days)</h4>
                    <div class="col-2 d-flex">
                        <input type="number" class="form-control" name="duration" min="10" max="40" placeholder="Days"
                            required />
                        <input type="submit" name="submit" value="Rent" class="rent-btn  ms-3">
                    </div>
                </form>
            </div>

            <h6 class="fw-bold fs-5 my-3">Short Description</h6>
            <p id="shortDescription" class="mt-3 text-justify">
                <?php echo $getProduct['0']['short_desc'] ?></p>
        </div>
    </div>
    <div class="accordion" id="accordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <h6 class="fs-5 ms-4">Description</h6>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                data-bs-parent="#accordion">
                <div class="accordion-body">
                    <p id="description" class="mb-3 text-justify"><?php echo $getProduct['0']['description'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require('footer.php') ?>

<style>
    .card {
    border: none;
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease-in-out;
    position: relative;
    width: 100%; /* Make it responsive */
    max-width: 100%; /* Ensure it doesn't overflow */
}

.card:hover {
    transform: scale(1.05);
}

.card-img-top {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    height: auto; /* Let it adjust based on content */
    max-height: 380px;
    width: 100%;
    object-fit: cover;
    transition: opacity 0.3s ease-in-out;
}

.card:hover .card-img-top {
    opacity: 0.8;
}

/* Responsive adjustment for smaller screens */
@media screen and (max-width: 768px) {
    .card-img-top {
        max-height: 250px;
    }
}

.rent-btn {
    background-color: #34495e;
    color: #ffffff;
    font-weight: bold;
    padding: 6px 12px; /* Reduce padding */
    border-radius: 15px;
    font-size: 13px; /* Adjust font size */
    white-space: nowrap; /* Prevent stretching */
    width: auto; /* Allow button to size dynamically */
    min-width: 80px; /* Set a reasonable minimum width */
    text-transform: uppercase;
    transition: background 0.3s ease-in-out;
}

.rent-btn:hover {
    background-color: #fff;
    color: #34495e;
}
@media (max-width: 768px) {
    .rent-btn {
        width: 100%;
    }
}

</style> 