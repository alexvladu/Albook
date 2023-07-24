<?php 
if (session_status() == PHP_SESSION_NONE)
session_start();
$c = mysqli_connect("localhost", "root", "", "albook");
$myph=$_SESSION['phonenumber'];
$hisph=$_GET['toph'];
$check_friend=mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends WHERE (sender='$myph' AND recipient='$hisph') OR sender='$hisph' AND recipient='$myph'"));
if($check_friend)
    mysqli_query($c, "DELETE FROM friends WHERE (sender='$myph' AND recipient='$hisph') OR sender='$hisph' AND recipient='$myph'");
echo '<button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="send_friend_request(\''."$hisph".'\')">Add friend</button>';
?>