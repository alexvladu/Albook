<?php
setlocale(LC_ALL, 'ro', 'ro_RO');
date_default_timezone_set('Europe/Bucharest');
if (session_status() == PHP_SESSION_NONE)
    session_start();
$_SESSION['offset'] = -10;
$_SESSION['offset_friends'] = -10;
$_SESSION['offset_friendreq'] = -10;
$login = 0;
if (isset($_SESSION['login']))
    $login = 1;
if (!$login) {
    header("location: /login");
    exit();
}
$c = mysqli_connect("localhost", "root", "", "albook");
if (isset($_GET['search'])) {
    $s = $_GET['search'];
    header("location: /search?q=$s");
    exit();
}
?>
<html lang="en">

<head>
    <title>alBook</title>
    <meta charset="utf-8">
    <meta name="description" content="Free and secure chat">
    <meta name="keywords" content="Interact with your friends">
    <meta name="author" content="ALEX VLADU">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style>
        .col-4{
            width: 500px;
        }
        .row{
            width:fit-content;
            justify-content: center;
            margin: 0 auto;
            margin-top: 16px;
            user-select: none;
        }
        .card-body{
            text-align: end;
        }
        @media only screen and (max-width: 576px) {
            .col-4, .row {
                width: 85%;
            }
            .card-body{
                justify-content: center;
                align-content: center;
                flex-direction: column;
                text-align: center;
            }
        }
        #photo{
            width: 100px;
            text-align: center;
        }
    </style>
    <script>
        function send_friend_request(to)
        {
            $.ajax({
                type: "GET",
                url: "sendfriendrequest.php",
                data:{toph: to},
                dataType: "html",
                success: function(data) {
                    $("#btn"+to).html(data);
                }
            });
        }
        function cancel_friend_request(to)
        {
            $.ajax({
                type: "GET",
                url: "cancelfriendrequest.php",
                data:{toph: to},
                dataType: "html",
                success: function(data) {
                    $("#btn"+to).html(data);
                }
            });
        }
        function accept_friend_request(to)
        {
            $.ajax({
                type: "GET",
                url: "acceptfriendrequest.php",
                data:{toph: to},
                dataType: "html",
                success: function(data) {
                    $("#btn"+to).html(data);
                }
            });
        }
        function remove_friend(to)
        {
            $.ajax({
                type: "GET",
                url: "removefriend.php",
                data:{toph: to},
                dataType: "html",
                success: function(data) {
                    $("#btn"+to).html(data);
                }
            });
        }
        function getfriends_ajax() {
            $.ajax({
                type: "GET",
                url: "getfriends.php",
                dataType: "html",
                success: function(data) {
                    $(".friends-append").append(data);
                    console.log(data);
                }
            });
        }
        $(document).ready(function() {
            getfriends_ajax();
            $("#modalId3").scroll(function() {
                if ($("#friends-height").height() - $("#modalId3").scrollTop() <= 1200)
                    getfriends_ajax();
            });
        });
    </script>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <div class="container-fluid" style="user-select: none;">
                <a class="navbar-brand" onclick="window.location='/'" style="cursor: pointer;">alBook</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <?php
                if ($login) {
                    $ph = $_SESSION['phonenumber'];
                    echo '<div class="collapse navbar-collapse" id="mynavbar" style="text-align:center;">
                    <i>sp</i><br> 
                        <form class="d-flex" style="margin: 0 auto" method="get">
                            <input class="form-control me-2" type="text" placeholder="Search" name="search" required>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i>
                            </button>
                        </form>
                        <ul class="navbar-nav me-auto" style="display: contents">
                        <i>sp</i><br>
                        <button type="button" class="btn btn-primary btn position-relative" data-bs-toggle="modal" data-bs-target="#modalId3">
                        Friends
                        </button><br>
                        <i>sp</i><br>
                        <!-- Modal Body Friends-->
                    <div class="modal fade" id="modalId3" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                        <div class="modal-dialog" id="mid3" role="document">
                            <div class="modal-content" id="friends-height">
                                <div class="modal-header">
                                        <h5 class="modal-title" id="modalTitleId">Friends</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                        <div class="modal-body">
                        <div class="container-fluid friends-append"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                                </div>
                            </div>
                        </div>
                            <a href=\'/profile?q=' . $ph . '\'><i class="bi bi-person-circle position-relative" style="color:white; font-size: 25pt; cursor: pointer;">';
                            $myph = $_SESSION['phonenumber'];
                            $cnt_freq=mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends_requests WHERE (recipient='$myph' OR sender='$myph')"));
                            if($cnt_freq)
                                echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger afisfreq" style="font-size:8pt; margin-top:7px;">'.$cnt_freq.'</span>';
                            echo '</i>
                            </a>
                        </ul>
                    </div>';
                } else echo '<div class="collapse navbar-collapse" id="mynavbar" style="text-align:center; justify-content: end">
                    <ul class="navbar-nav me-auto" style="display: contents">
                    <i>sp</i><br>
                    <button onclick=window.location=\'/login\' type="button" class="btn btn-primary position-relative">
                        Login
                    </button><br>
                    <i>sp</i><br>
                    <button onclick=window.location=\'/register\' type="button" class="btn btn-primary position-relative">
                        Register
                    </button><br>
                    </ul>
                </div>';
                ?>
        </nav>
    </header>
    <main>
        <?php
        
        if (isset($_GET['q'])){
            $s = $_GET['q'];
            $myph=$_SESSION['phonenumber'];
            $rez = mysqli_query($c, "SELECT * from user WHERE (CONCAT(prenume,' ',nume) LIKE '%$s%' OR prenume LIKE '%$s%' OR nume LIKE '%$s%') ORDER BY prenume ASC, nume ASC LIMIT 30");
            while ($array = mysqli_fetch_array($rez))
            {
                $hisph=$array[3];
                $check_friend=mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends WHERE (sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph')"));
                if($check_friend && $myph!=$hisph)
                {
                    echo '<div class="row">
                    <div class="col-4 position-relative">
                    <div class="card">
                        <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                            <div id="photo">
                                <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                <nobr>'.$hisph.'</nobr><br>
                                <nobr>'.$array[1].' '.$array[2].'</nobr>
                            </div>
                            <div id="btn'.$hisph.'">
                                <button type="button" class="btn btn-primary" onclick="window.location=\'/profile?q='.$array[3].'\'">View profile</button><br>
                                <button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="window.location=\'/chat?q='.$array[3].'\'">Send message</button><br>
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
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    </div>';
                }
                else if($myph!=$hisph)
                {
                    $check_incoming_request=mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends_requests WHERE (sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph')"));
                    if(!$check_incoming_request)
                    {
                        echo '<div class="row">
                        <div class="col-4 position-relative">
                        <div class="card">
                            <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                                <div id="photo">
                                    <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                    <nobr>'.$hisph.'</nobr><br>
                                    <nobr>'.$array[1].' '.$array[2].'</nobr>
                                </div>
                                <div id="btn'.$hisph.'">
                                    <button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="send_friend_request(\''."$hisph".'\')">Add friend</button>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>';
                    }
                    else{
                        $rez_incoming=mysqli_fetch_array(mysqli_query($c, "SELECT * FROM friends_requests WHERE (sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph')"));
                        if($rez_incoming[0]==$myph)
                        {
                            echo '<div class="row">
                            <div class="col-4 position-relative">
                            <div class="card">
                                <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                                    <div id="photo">
                                        <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                        <nobr>'.$hisph.'</nobr><br>
                                        <nobr>'.$array[1].' '.$array[2].'</nobr>
                                    </div>
                                    <div id="btn'.$hisph.'">
                                        <button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="cancel_friend_request(\''."$hisph".'\')">Cancel request</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>';
                        }
                        else{
                            echo '<div class="row">
                            <div class="col-4 position-relative">
                            <div class="card">
                                <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                                    <div id="photo">
                                        <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                        <nobr>'.$hisph.'</nobr><br>
                                        <nobr>'.$array[1].' '.$array[2].'</nobr>
                                    </div>
                                    <div id="btn'.$hisph.'">
                                        <button type="button" class="btn btn-primary" style="margin-top:7px;" onclick="accept_friend_request(\''."$hisph".'\')">Accept request</button><br>
                                        <button type="button" class="btn btn-primary btn" style="margin-top:7px;" data-bs-toggle="modal" data-bs-target="#modalId2">
                                        Delete request
                                        </button>
                                        <!-- Modal Body-->
                                        <div class="modal fade" id="modalId2" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content" style="text-align: center">
                                                        <div class="modal-header">
                                                                <h5 class="modal-title" id="modalTitleId">Are you sure you want to delete friend request from '.$array[1].' '.$array[2].'</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                    <div class="modal-footer" style="justify-content: center">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="cancel_friend_request(\''."$hisph".'\')">Yes</button>
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                            </div>';
                        }
                    }
                }
            }
        }
        ?>
    </main>
    
    <footer class="text-center text-white mt-auto" style="background-color: #f1f1f1; user-select:none; ">

    </footer>

</body>

</html>