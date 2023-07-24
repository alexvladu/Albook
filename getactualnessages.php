<?php
setlocale(LC_ALL, 'ro', 'ro_RO');
date_default_timezone_set('Europe/Bucharest');
if (session_status() == PHP_SESSION_NONE)
    session_start();
$c=mysqli_connect("localhost", "root", "", "albook");
$myph=$_SESSION['phonenumber'];
$hisph=$_GET['toph'];
$query=mysqli_query($c, "SELECT * FROM chat WHERE (sender='$myph' AND recipient='$hisph' AND seen_sender='0') OR (sender='$hisph' AND recipient='$myph' AND seen_recipient='0')");
$vec=[];
$mymessage=0;
if(mysqli_num_rows($query))
{
    while ($rez=mysqli_fetch_array($query)) 
    {
        $vec[]=$idrez=$rez[0];
        $vec[]=$rez[4];
        if($rez[1]==$myph) ///eu am trimis
        {
            $mymessage=1;
            if(!$rez[6])
                mysqli_query($c, "UPDATE chat SET seen_sender='1' WHERE id='$idrez'");
            $array=mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$myph'"));
            $vec[]='<div class="msg right-msg" id="' . $rez[0] . '">
                    <i class="bi bi-person-circle" style="font-size:35pt;"></i>
                    <div class="msg-bubble">
                        <div class="msg-info">
                            <div class="msg-info-name">'.$array[1].' '.$array[2].'</div>
                            <div class="msg-info-time">'.$rez[5].'</div>
                        </div>

                        <div class="msg-text">
                            ' .$rez[3]. '
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
$vec[]=$mymessage;
echo json_encode($vec);
