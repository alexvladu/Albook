<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
if(!isset($_SESSION['offset_friends']))
    $_SESSION['offset_friends']=-10;
$_SESSION['offset_friends']+=10;
$c = mysqli_connect("localhost", "root", "", "albook");
$off=$_SESSION['offset_friends'];
$myph=$_SESSION['phonenumber'];
$q=mysqli_query($c, "SELECT * FROM friends WHERE (sender='$myph' OR recipient='$myph') ORDER BY last_interact DESC, id DESC LIMIT $off, 10");
if(mysqli_num_rows($q))
{
    while($rez=mysqli_fetch_array($q))
    {
        if($rez[1]!=$myph)
            $hisph=$rez[1];
        else $hisph=$rez[2];
        $array=mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$hisph'"));
        echo '<div class="row">
                    <div class="col-4 position-relative" style="width:440px;">
                    <div class="card">
                        <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                            <div id="photo">
                                <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                <nobr>'.$hisph.'</nobr><br>
                                <nobr>'.$array[1].' '.$array[2].'</nobr>
                            </div>
                            <div id="btn'.$hisph.'">
                                <button type="button" class="btn btn-primary" style="width:100%;" onclick="window.location=\'/profile?q='.$array[3].'\'">View profile</button><br>
                                <button type="button" class="btn btn-primary" style="margin-top:7px; width:100%;" onclick="window.location=\'/chat?q='.$array[3].'\'">Send message</button><br>
                                <button type="button" class="btn btn-primary btn" style="margin-top:7px; width:100%;" data-bs-html="true" data-bs-toggle="popover" title="Are you sure you want to remove '.$array[1].' '.$array[2].' from your friend list?" data-bs-content="<a onclick=remove_friend(\'' . "$hisph" . '\') class=\'btn yesno btn-success yes'.$hisph.'\' data-bs-target=\'.popover\' data-bs-toggle=\'collapse\'>Yes</a><a onclick=hidepopover(\''.$hisph.'\') type=\'button\' class=\'btn yesno btn-danger no'.$hisph.'\'>No</a>">Remove friend</button>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>';
    }
}
