<?php include_once 'connection/db.php'; ?>
<?php include_once 'connection/mysqli.php';?>
<?php
  session_start();
  if($_SESSION['logged_in'] != true){
    header('location: '.$base_url.'index.php');
    die("in header, should have redirect to index because not logged in");
  }
  else{
    $getfullname = $_SESSION['get_fullname'];
    $getage = $_SESSION['get_age'];
    $getaddress = $_SESSION['get_address'];
    $getposition = $_SESSION['get_position'];
  }
?>