<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();
if (!isset($_SESSION['offset_friendreq']))
    $_SESSION['offset_friendreq'] = -10;
$_SESSION['offset_friendreq'] += 10;
$c = mysqli_connect("localhost", "root", "", "albook");
$off = $_SESSION['offset_friendreq'];
$myph = $_SESSION['phonenumber'];
$q = mysqli_query($c, "SELECT * FROM friends_requests WHERE (recipient='$myph' OR sender='$myph') LIMIT $off, 10");
$cnt = mysqli_num_rows($q);
$_SESSION['offset_friendreq'] += $cnt;
if ($cnt) {
    while ($rez = mysqli_fetch_array($q)) {
        if ($rez[0] == $myph) {
            $hisph = $rez[1];
            $array = mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$hisph'"));
            echo '<div class="row freq'.$hisph.'">
                            <div class="col-4 position-relative" style="width:440px;">
                            <div class="card">
                                <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                                    <div id="photo">
                                        <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                        <nobr>' . $hisph . '</nobr><br>
                                        <nobr>' . $array[1] . ' ' . $array[2] . '</nobr>
                                    </div>
                                    <div id="btn' . $hisph . '">
                                        <button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="cancel_friend_request(\'' . "$hisph" . '\')">Cancel request</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>';
        } else {
            $hisph = $rez[0];
            $array = mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$hisph'"));
            echo '<div class="row freq'.$hisph.'">
                            <div class="col-4 position-relative" style="width:440px;">
                            <div class="card">
                                <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                                    <div id="photo">
                                        <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                        <nobr>' . $hisph . '</nobr><br>
                                        <nobr>' . $array[1] . ' ' . $array[2] . '</nobr>
                                    </div>
                                    <div id="btn' . $hisph . '">
                                        <button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="accept_friend_request(\'' . "$hisph" . '\')">Accept request</button><br>
                                        <button type="button" class="btn btn-primary btn" style="margin-top:7px;" data-bs-html="true" data-bs-toggle="popover" title="Are you sure you want to remove '.$array[1].' '.$array[2].' from your friend requests list?" data-bs-content="<a onclick=cancel_friend_request(\'' . "$hisph" . '\') class=\'btn yesno btn-success yes'.$hisph.'\' data-bs-target=\'.popover\' data-bs-toggle=\'collapse\'>Yes</a><a onclick=hidepopover(\''.$hisph.'\') type=\'button\' class=\'btn yesno btn-danger no'.$hisph.'\'>No</a>">Delete request</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>';
        }
    }
}
?>
