<?php
  session_start();
  unset($_SESSION['ADMIN_LOGIN']);
  unset($_SESSION['ADMIN_NAME']);
  unset($_SESSION['ADMIN_EMAIL']);
  unset($_SESSION['ADMIN_email']);
  unset($_SESSION['ADMIN_ROLE']);

  header('location:login.php');
  die();

  