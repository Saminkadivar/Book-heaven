<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'mini_project';

// Create connection
$mysqli = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>
