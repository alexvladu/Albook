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
        .col-4 {
            width: 600px;
        }

        .row {
            width: fit-content;
            justify-content: center;
            margin: 0 auto;
            margin-top: 16px;
            user-select: none;
        }

        .card-body {
            text-align: end;
        }

        .yesno {
            margin: 3px;
        }

        @media only screen and (max-width: 576px) {

            .col-4,
            .row {
                width: 90%;
            }

            .card-body {
                justify-content: center;
                align-content: center;
                flex-direction: column;
                text-align: center;
            }
        }

        #photo {
            width: 100px;
            text-align: center;
        }

        textarea {
            resize: none;
        }

        .buline {
            -webkit-text-security: disc;
        }

        .noselect {
            cursor: default;
            outline: none;

        }

        footer {
            display: none;
        }

        .alert {
            margin-top: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .noselect:focus {
            outline: none;
            border-color: #dee2e6;
            box-shadow: 0 0 0 rgb(255, 255, 255);
        }

        .randuri-prieteni {
            width: 440px;
        }

        .zind {
            z-index: 1060;
        }

        .popover-header {
            height: auto;
            max-width: 200px;
        }
    </style>
    <?php
    if (isset($_POST['logout'])) {
        session_destroy();
        header("location: /");
        exit();
    }
    if (isset($_POST['valid_post']) && isset($_GET['q'])) {
        $ph = $_GET['q'];
        $data = date("d.m.Y, H:i");
        $text = $_POST['text_post'];
        echo $text;
        mysqli_query($c, "INSERT INTO posts (publicat_de, data, text) VALUES ('$ph', '$data', '$text')");
        header("location: /profile?q=$ph");
        exit();
    }
    if (isset($_POST['changepwd']) && isset($_GET['q'])) {
        if (isset($_POST['pwd_current']) && isset($_POST['pwd_next'])) {
            $ph = $_GET['q'];
            $phone = $_SESSION['phonenumber'];
            $rez_psw = mysqli_query($c, "SELECT password FROM user WHERE phonenumber='$phone'");
            $psw = mysqli_fetch_array($rez_psw)[0];
            $current_pass = $_POST['pwd_current'];
            $next_pass = $_POST['pwd_next'];
            if ($psw == $current_pass) {
                if ($current_pass == $next_pass) {
                    $_SESSION['changed_pass_equal'] = 1;
                    header("location: /profile?q=$ph");
                    exit();
                } else {
                    $_SESSION['changed_pass_succes'] = 1;
                    mysqli_query($c, "UPDATE user SET password='$next_pass' WHERE phonenumber='$ph'");
                    header("location: /profile?q=$ph");
                    exit();
                }
            } else {
                $_SESSION['changed_pass_wrong'] = 1;
                header("location: /profile?q=$ph");
                exit();
            }
        }
    }
    ?>
    <script>
        var active = <?php $myph = $_SESSION['phonenumber'];
                        $cnt_freq = mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends_requests WHERE (recipient='$myph' OR sender='$myph')"));
                        echo $cnt_freq; ?>;
    
        function hidepopover(to) {
            $(".no" + to).parent().parent().removeClass("show");
        }
        function cancel_friend_request(to, id, check = 0) {
            $.ajax({
                type: "GET",
                url: "cancelfriendrequest.php",
                data: {
                    toph: to
                },
                dataType: "html",
                success: function(data) {
                    $(".freq" + to).hide();
                    active--;
                    if (active <= 0) {
                        $(".afisfreq").html('');
                        $(".afisfreq").html('');
                        $(".friendreq-append").append("<h4>You don\'t have active friend requests</h4>");
                        $(".friendreq-append").css("text-align", "center");
                    } else
                        $(".afisfreq").html(active);
                }
            })
        }

        function accept_friend_request(to) {
            $.ajax({
                type: "GET",
                url: "acceptfriendrequest.php",
                data: {
                    toph: to
                },
                dataType: "html",
                success: function(data) {
                    $(".freq" + to).hide();
                    hidepopover(to);
                    active--;
                    console.log(active);
                    if (active <= 0) {
                        $(".afisfreq").html('');
                        $(".afisfreq").html('');
                        $(".friendreq-append").append("<h4>You don\'t have active friend requests</h4>");
                        $(".friendreq-append").css("text-align", "center");
                    } else
                        $(".afisfreq").html(active);
                }
            });
        }

        function getposts_ajax() {
            $.ajax({
                type: "GET",
                url: "getposts.php",
                dataType: "html",
                success: function(data) {
                    $("#main-content").append(data);
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
                    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                        return new bootstrap.Popover(popoverTriggerEl, {
                            sanitize: false,
                            html: true,
                        })
                    })
                }
            });
        }

        function remove_friend(to) {
            $.ajax({
                type: "GET",
                url: "removefriend.php",
                data: {
                    toph: to
                },
                dataType: "html",
                success: function(data) {
                    $("#btn" + to).parent().parent().parent().parent().hide();
                    hidepopover(to);
                }
            });
        }
        function getfriendreq_ajax() {
            $.ajax({
                type: "GET",
                url: "getfriendreq.php",
                dataType: "html",
                success: function(data) {
                    if (active <= 0) {
                        $(".afisfreq").html('');
                        $(".afisfreq").html('');
                        $(".friendreq-append").append("<h4>You don\'t have active friend requests</h4>");
                        $(".friendreq-append").css("text-align", "center");
                    } else
                        $(".afisfreq").html(active);
                    $(".friendreq-append").append(data);
                    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
                        return new bootstrap.Popover(popoverTriggerEl, {
                            sanitize: false,
                            html: true,
                        })
                    })
                }
            });
        }
        $(window).scroll(function() {
            if ($(window).height() - $(window).scrollTop() <= 1200)
                getposts_ajax();
        });
        $(document).ready(function() {
            getposts_ajax();
            getfriends_ajax();
            getfriendreq_ajax();
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
                    $cnt_freq = mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends_requests WHERE (recipient='$myph' OR sender='$myph')"));
                    if ($cnt_freq)
                        echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger afisfreq" style="font-size:8pt; margin-top:7px;">' . $cnt_freq . '</span>';
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
        if (isset($_GET['q'])) {
            $_SESSION['current'] = $_GET['q'];
            $current_page = $_GET['q'];
            $rez = mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$current_page'");
            $array = mysqli_fetch_array($rez);
            if (mysqli_num_rows($rez)) {
                if ($current_page == $_SESSION['phonenumber']) { {
                        echo '<div class="row">
                    <div class="col-4 position-relative">
                    ';
                        if (isset($_SESSION['changed_pass_equal'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" style="margin-top: auto;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <nobr id="alert_text">Identical passwords!</nobr>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
                            unset($_SESSION['changed_pass_equal']);
                        }
                        if (isset($_SESSION['changed_pass_succes'])) {
                            echo '<div class="alert alert-success alert-dismissible fade show" style="margin-top: auto;">
                            <i class="bi bi-check-circle-fill"></i>
                            <nobr style="margin-left: 4px;" id="sentto">Password changed successfully!</nobr>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
                            unset($_SESSION['changed_pass_succes']);
                        }
                        if (isset($_SESSION['changed_pass_wrong'])) {
                            echo '<div class="alert alert-danger alert-dismissible fade show" style="margin-top: auto;">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <nobr id="alert_text">Wrong password!</nobr>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
                            unset($_SESSION['changed_pass_wrong']);
                        }
                        echo '
                        <div class="card">
                            <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                                <div id="photo">
                                    <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                                    <nobr>' . $array[1] . ' ' . $array[2] . '</nobr>
                                    <nobr>' . $current_page . '</nobr><br>
                                </div>
                                <div id="buttons">
                                    <!--  New post  -->
                                    <button style="width:100%" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalId">New post</button><br>
        
                                    <!-- Modal Body-->
                                    <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                                        <form method="post">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalTitleId">New post</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container-fluid">
                                                            <div class="mb-3" style="text-align: center;">
                                                                <textarea class="form-control" name="text_post" id="" rows="3" maxlength="1000" minlength="1" required></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <input type="submit" class="btn btn-primary" value="Post" name="valid_post">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    

                                    <button type="button" class="btn btn-primary btn position-relative" data-bs-toggle="modal" data-bs-target="#modalId1" style="margin-top:7px;" data-dismiss="modal" data-toggle="modal" > 
                                    Friend requests';
                        $myph = $_SESSION['phonenumber'];
                        $cnt_freq = mysqli_num_rows(mysqli_query($c, "SELECT * FROM friends_requests WHERE (recipient='$myph' OR sender='$myph')"));
                        if ($cnt_freq)
                            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger afisfreq">' . $cnt_freq . '</span>';
                        echo '</button><br>
                                    <!-- Modal Body Friends-->
                                <div class="modal fade" id="modalId1" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                                    <div class="modal-dialog" id="mid3" role="document">
                                        <div class="modal-content" id="friends-height">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalTitleId">Friend requests</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                    <div class="modal-body">
                                    <div class="container-fluid friendreq-append">
                                    </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-primary btn" data-bs-toggle="modal" data-bs-target="#modalId4" style="margin-top:7px; width:100%;">
                                    Settings
                                    </button><br>
                                    
                                    <!-- Modal Body-->
                                    <div class="modal fade" id="modalId4" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                    <div class="modal-header">
                                                            <h5 class="modal-title" id="modalTitleId">Settings</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                <div class="modal-body" style="text-align: center;">
                                                    <div class="container-fluid" style="text-align:center;">
                                                        <button type="button" class="btn btn-primary" style="margin-top:7px; display: inline-grid; width: 35%; min-width: 160px;" data-bs-toggle="modal" data-bs-target="#modalId2">Change password</button>
                                                    </div>
                                                    <form style="display: inline-grid; width: 35%; min-width: 160px;" method="post">
                                                        <input type="submit" class="btn btn-primary" style="margin-top:7px;" value="Log out" name="logout">
                                                    </form><br>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Body-->
                                    <div class="modal fade" id="modalId2" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                                        <form method="post">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalTitleId">Change password</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="container-fluid">
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control buline" id="pwd_current" placeholder="Enter your current password" name="pwd_current" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <input type="text" class="form-control buline" id="pwd_next" placeholder="Enter your new password" name="pwd_next" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <input type="submit" class="btn btn-primary" name="changepwd" value="Change Password">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
                    }
                } else {
                    $hisph = $array[3];
                    echo '<div class="row">
                    <div class="col-4 position-relative">
                    <div class="card">
                    <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                        <div id="photo">
                            <i class="bi bi-person-circle" style="font-size: 45pt;"></i><br>
                            <nobr>' . $array[1] . ' ' . $array[2] . '</nobr>
                            <nobr>' . $current_page . '</nobr><br>
                        </div>
                        <div id="buttons">
                            <button type="button" class="btn btn-primary" style="width:100%;" onclick="window.location=\'/profile?q=' . $array[3] . '\'">View Profile</button><br>
                            <button type="button" class="btn btn-primary" style="margin-top:7px; width:100%;" onclick="window.location=\'/chat?q=' . $array[3] . '\'">Send message</button><br>
                            <button type="button" class="btn btn-primary" style="margin-top:7px; width:100%;" data-bs-toggle="modal" data-bs-target="#modalId">
                                Remove friend
                                </button>
                                
                                <!-- Modal Body-->
                                <div class="modal fade" id="modalId" tabindex="-1" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                                <div class="modal-header">
                                                        <h5 class="modal-title" id="modalTitleId" style="text-align: center;">Are you sure you want to remove ' . $array[1] . ' ' . $array[2] . ' from your friend list?</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                            <div class="modal-footer" style="justify-content: center;">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="remove_friend(\'' . "$hisph" . '\')">Yes</button>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div></div></div>';
                }
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" style="width: 600px; margin: 0 auto; margin-top: 16px;">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <nobr id="alert_text">Account doesn\'t exist</nobr>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>';
                exit();
            }
        }
        ?>
        <div id="main-content">
            <div class="row">
                <div class="col-4 position-relative">
                    <div class="card">
                        <div class="card-body" style="justify-content: space-between; align-items: center; display: inline-flex; flex-wrap: wrap">
                            <div id="text-date">
                                <h4>News Feed</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="text-center text-white mt-auto" style="background-color: #f1f1f1; user-select:none; ">

    </footer>

</body>
<script>
</script>

</html>