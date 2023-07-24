<?php 
setlocale(LC_ALL, 'ro', 'ro_RO');
date_default_timezone_set('Europe/Bucharest');
if (session_status() == PHP_SESSION_NONE)
    session_start();
$c = mysqli_connect("localhost", "root", "", "albook");
$myph=$_SESSION['phonenumber'];
$hisph=$_GET['toph'];
$msg=$_GET['toph_msg'];
$data = date("d.m.Y");
$ora = date("H:i");
if(strlen($msg)>0 && strlen($msg)<=250)
{
    mysqli_query($c, "INSERT INTO chat (sender, recipient, text, date, time) VALUES ('$myph', '$hisph', '$msg', '$data', '$ora')");
    $timp=time();
    mysqli_query($c, "UPDATE friends SET last_interact WHERE (sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph')");
}
?>