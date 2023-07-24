<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
if(!isset($_SESSION['offset']))
    $_SESSION['offset']=-10;
$_SESSION['offset']+=10;
$c = mysqli_connect("localhost", "root", "", "albook");
$ph=$_SESSION['current'];
$getname=mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$ph'");
$rez_getname=mysqli_fetch_array($getname);
if(mysqli_num_rows($getname))
{
    $off=$_SESSION['offset'];
    $q=mysqli_query($c, "SELECT * FROM posts where publicat_de='$ph' ORDER BY id DESC LIMIT $off, 10");
    $array = "";
    while($rez=mysqli_fetch_array($q))
    {
        $array.='<div id="'.$rez[0].'" class="row">
        <div class="col-4 position-relative">
            <div class="card">
                <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                    <div id="text-date">
                        <i class="bi bi-person-circle" style="font-size: 20pt;"></i>';
        $array.=$rez_getname[1]." ".$rez_getname[2].", ".$rez[2].'</div><textarea class="form-control noselect" name="" id="" rows="3" readonly>'.$rez[3].'</textarea>
                </div>
            </div>
        </div>
    </div>';
    }
    echo $array;
}
