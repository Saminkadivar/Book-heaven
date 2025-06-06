<?php
// Start session only if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define constants only if they haven't been defined already
if (!defined('SERVER_PATH')) {
    define('SERVER_PATH', $_SERVER['DOCUMENT_ROOT'] . '/');
}

if (!defined('SITE_PATH')) {
    define('SITE_PATH', 'http://localhost/BscitMiniProject3rdSem/');
}

// (Optional) Book image paths (if needed in the future)
// if (!defined('BOOK_IMAGE_SERVER_PATH')) {
//     define('BOOK_IMAGE_SERVER_PATH', SERVER_PATH . 'Img/books/');
// }
// if (!defined('BOOK_IMAGE_SITE_PATH')) {
//     define('BOOK_IMAGE_SITE_PATH', SITE_PATH . 'Img/books/');
// }

// Establish database connection
$con = mysqli_connect("localhost", "root", "", "mini_project");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>
