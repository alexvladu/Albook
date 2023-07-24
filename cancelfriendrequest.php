<?php 
if (session_status() == PHP_SESSION_NONE)
session_start();
$c = mysqli_connect("localhost", "root", "", "albook");
$myph=$_SESSION['phonenumber'];
$hisph=$_GET['toph'];
$check_friend=mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends WHERE (sender='$myph' AND recipient='$hisph') OR sender='$hisph' AND recipient='$myph'"));
if(!$check_friend)
{
    $check_incoming_request=mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends_requests WHERE (sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph')"));
    if($check_incoming_request)
        mysqli_query($c, "DELETE FROM friends_requests WHERE (sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph')");
    echo '<button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="send_friend_request(\''."$hisph".'\')">Add friend</button>';
}
else{
    $array=mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$hisph'"));
    echo '<button type="button" class="btn btn-primary" onclick="window.location=\'/profile?q='.$array[3].'\'">View profile</button><br>
        <button type="button" class="btn btn-primary" style="margin-top:7px;">Send message</button><br>
        <!--  Modal trigger button  -->
        <button type="button" class="btn btn-primary" style="margin-top:7px;" data-bs-toggle="modal" data-bs-target="#modalId">
        Remove friend
        </button>
        
        <!-- Modal Body-->
        <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                        <div class="modal-header">
                                <h5 class="modal-title" id="modalTitleId" style="text-align: center;">Are you sure you want to remove '.$array[1].' '.$array[2].' from your friend list?</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                    <div class="modal-footer" style="justify-content: center;">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="remove_friend(\''."$hisph".'\')">Yes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>';
}
?>