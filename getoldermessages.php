<?php 
setlocale(LC_ALL, 'ro', 'ro_RO');
date_default_timezone_set('Europe/Bucharest');
if (session_status() == PHP_SESSION_NONE)
    session_start();
if(!isset($_SESSION['offset_msg']))
    $_SESSION['offset_msg']=-10;
$_SESSION['offset_msg']+=10;
$c = mysqli_connect("localhost", "root", "", "albook");
$off=$_SESSION['offset_msg'];
$myph=$_SESSION['phonenumber'];
$hisph=$_GET['toph'];
$q=mysqli_query($c, "SELECT * FROM chat WHERE ((sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph')) ORDER BY id DESC LIMIT $off, 10");
$vec=[];
if(mysqli_num_rows($q))
{
    while($rez=mysqli_fetch_array($q))
    {
        $vec[]=$rez[0];
        $vec[]=$rez[4];
        $idrez=$rez[0];
        if($rez[1]==$myph)///eu am trimis
        {
            if(!$rez[6])
                mysqli_query($c, "UPDATE chat SET seen_sender='1' WHERE id='$idrez'");
            $array=mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$myph'"));
            $vec[]='<div class="msg right-msg" id="'.$rez[0].'">
                <i class="bi bi-person-circle" style="font-size:35pt;"></i>
                <div class="msg-bubble">
                    <div class="msg-info">
                        <div class="msg-info-name">'.$array[1].' '.$array[2].'</div>
                        <div class="msg-info-time">'.$rez[5].'</div>
                    </div>

                    <div class="msg-text">
                        '.$rez[3].'
                    </div>
                </div>
            </div>';
        }
        else//el a trimis
        {
            if(!$rez[7])
                mysqli_query($c, "UPDATE chat SET seen_recipient='1' WHERE id='$idrez'");
            $array=mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$hisph'"));
            $vec[]='<div class="msg left-msg" id="'.$rez[4].'">
                <i class="bi bi-person-circle" style="font-size:35pt;"></i>
                <div class="msg-bubble">
                    <div class="msg-info">
                        <div class="msg-info-name">'.$array[1].' '.$array[2].'</div>
                        <div class="msg-info-time">'.$rez[5].'</div>
                    </div>

                    <div class="msg-text">
                        '.$rez[3].'
                    </div>
                </div>
            </div>';
        }
    }
}
echo json_encode($vec);
?>