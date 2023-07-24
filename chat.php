<?php
setlocale(LC_ALL, 'ro', 'ro_RO');
date_default_timezone_set('Europe/Bucharest');
if (session_status() == PHP_SESSION_NONE)
    session_start();
$_SESSION['offset'] = -10;
$_SESSION['offset_friends'] = -10;
$_SESSION['offset_msg'] = -10;
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
        :root {
            --body-bg: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            --msger-bg: #fff;
            --border: 2px solid #ddd;
            --left-msg-bg: #ececec;
            --right-msg-bg: #579ffb;
        }

        html {
            box-sizing: border-box;
            height: 100%;
        }

        *,
        *:before,
        *:after {
            margin: 0;
            padding: 0;
            box-sizing: inherit;
        }

        .msger {
            display: flex;
            flex-flow: column wrap;
            justify-content: space-between;
            width: 100%;
            max-width: 867px;
            margin: 25px 10px;
            height: calc(100% - 50px);
            border: var(--border);
            border-radius: 5px;
            background: var(--msger-bg);
            box-shadow: 0 15px 15px -5px rgba(0, 0, 0, 0.2);
        }

        .msger-header {
            height: 50px;
            font-size: 20pt;
            padding: 10px;
            border-bottom: var(--border);
            background: #eee;
            color: #666;
        }

        .msger-chat {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }

        .msger-chat::-webkit-scrollbar {
            width: 6px;
        }

        .msger-chat::-webkit-scrollbar-track {
            background: #ddd;
        }

        .msger-chat::-webkit-scrollbar-thumb {
            background: #bdbdbd;
        }

        .msg {
            display: flex;
            align-items: flex-end;
            margin-bottom: 10px;
        }

        .msg:last-of-type {
            margin: 0;
        }

        .msg-img {
            width: 50px;
            height: 50px;
            margin-right: 10px;
            background: #ddd;
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            border-radius: 50%;
        }

        .msg-bubble {
            max-width: 450px;
            padding: 15px;
            border-radius: 15px;
            background: var(--left-msg-bg);
        }

        .msg-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .msg-info-name {
            margin-right: 10px;
            font-weight: bold;
        }

        .msg-info-time {
            font-size: 0.85em;
        }

        .left-msg .msg-bubble {
            border-bottom-left-radius: 0;
        }

        .right-msg {
            flex-direction: row-reverse;
        }

        .right-msg .msg-bubble {
            background: var(--right-msg-bg);
            color: #fff;
            border-bottom-right-radius: 0;
        }

        .right-msg .msg-img {
            margin: 0 0 0 10px;
        }

        .msger-inputarea {
            display: flex;
            padding: 10px;
            border-top: var(--border);
            background: #eee;
        }

        .msger-inputarea * {
            padding: 10px;
            border: none;
            border-radius: 3px;
            font-size: 1em;
        }

        .msger-input {
            flex: 1;
            background: #ddd;
        }

        .msger-send-btn {
            margin-left: 10px;
            background: rgb(0, 196, 65);
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }

        .msger-send-btn:hover {
            background: rgb(0, 180, 50);
        }

        .msger-chat {
            background-color: #fcfcfe;
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
        .msg-text{
            overflow-wrap: break-word;
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
    </style>
    <script>
        var mesaje=[];
        function getfriends_ajax() {
            $.ajax({
                type: "GET",
                url: "getfriends.php",
                dataType: "html",
                success: function(data) {
                    $(".friends-append").append(data);
                }
            });
        }
        function addmessagetodatabase()
        {
            $.ajax({
                type: "GET",
                url: "insertmsg.php",
                data:{toph:<?php $qsearch=$_GET['q']; echo "'$qsearch'"; ?>, toph_msg:$(".msger-input").val()},
                dataType: "html",
                success: function(data) {
                    console.log(data);
                }
            });
            $(".msger-input").val('');
        }
        var first_get_older_msg=1;
        var lastdate="", lastid=0;
        var firstdate="";
        var maxiscroll=0;
        function get_older_messages()
        {
            $.ajax({
                type: "GET",
                url: "getoldermessages.php",
                data:{toph:<?php $qsearch=$_GET['q']; echo "'$qsearch'"; ?>},
                dataType: "json",
                success: function(data) {
                    console.log(data);
                    var dim=data.length/3;
                    if(dim && lastdate=="")
                        firstdate=data[1];
                    for(var i=0; i<dim; i++)
                    {
                        if(lastdate=="")
                            lastdate=data[3*i+1], lastid=data[3*i];
                        if(data[3*i+1]!=lastdate)
                            $("#ms-content").prepend('<div style="text-align: center"><h4>'+lastdate+'</h4></div>'), lastdate=data[3*i+1], lastid=data[3*i];
                        if(data[3*i+1]==lastdate && data[3*i]<lastid)
                            lastid=data[3*i];
                        $("#ms-content").prepend(data[3*i+2]);
                    }
                    console.log(lastdate+ " " +lastid);
                    if(dim && <?php $myph=$_SESSION['phonenumber']; $hisph=$_GET['q']; $query_firstm=mysqli_query($c, "SELECT id FROM chat WHERE ((sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph'))"); if(mysqli_num_rows($query_firstm)) echo mysqli_fetch_array($query_firstm)[0]; else echo -1;?>==lastid)
                        $("#ms-content").prepend('<div style="text-align: center"><h4>'+lastdate+'</h4></div>');
                    if(first_get_older_msg)
                    {
                        $("#ms-content").animate({scrollTop:700},500);
                        first_get_older_msg=0;
                    }
                }
            });
            return false;
        }
        function get_actual_messages(){
            $.ajax({
                type: "GET",
                url: "getactualnessages.php",
                data:{toph:<?php $qsearch=$_GET['q']; echo "'$qsearch'"; ?>, toph_msg:$(".msger-input").val()},
                dataType: "json",
                success: function(data) {
                    var dim=data.length;
                    if(dim>1 && <?php $myph=$_SESSION['phonenumber']; $hisph=$_GET['q']; $query_firstm=mysqli_query($c, "SELECT id FROM chat WHERE ((sender='$myph' AND recipient='$hisph') OR (sender='$hisph' AND recipient='$myph'))"); if(mysqli_num_rows($query_firstm)) echo mysqli_fetch_array($query_firstm)[0]; else echo -1;?>==data[0])
                        $("#ms-content").append('<div style="text-align: center"><h4>'+data[0]+'</h4></div>'), firstdate=data[0];
                    for(var i=0; i<parseInt(dim/3); i++)
                    {
                        console.log(data[3*i+1]+" "+firstdate);
                        if(data[3*i+1]!=firstdate)
                            firstdate=data[3*i+1], $("#ms-content").append('<div style="text-align: center"><h4>'+firstdate+'</h4></div>');
                        $("#ms-content").append(data[3*i+2]);
                    }
                    if(data[dim-1])
                        $("#ms-content").animate({scrollTop:document.getElementById("ms-content").scrollHeight},500);
                    else if(dim>1)
                    {
                        console.log(123);
                        if(document.getElementById("ms-content").scrollHeight-$("#ms-content").scrollTop()<=1500)
                            $("#ms-content").animate({scrollTop:document.getElementById("ms-content").scrollHeight},500);
                    }
                    setTimeout(get_actual_messages, 250);
                }
            });
        }
        $(document).ready(function() {
            $("#formsubmitfalse").on('submit',function (e) {
                e.preventDefault();
                addmessagetodatabase();
            })
            getfriends_ajax();
            get_older_messages();
            $("#ms-content").scroll(function(){
                console.log(document.getElementById("ms-content").scrollHeight+ " " + $("#ms-content").scrollTop());
                if(!$("#ms-content").is(':animated') && $("#ms-content").scrollTop()<=200)
                    get_older_messages();
                if(maxiscroll<$("#ms-content").scrollTop())
                    maxiscroll=$("#ms-content").scrollTop();
            })
            $("#modalId3").scroll(function() {
                if ($("#friends-height").height() - $("#modalId3").scrollTop() <= 1200)
                    getfriends_ajax();
            });
            setTimeout(get_actual_messages, 250);
        });
    </script>
</head>

<body>
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
                        <i>sp</i><br>
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
    <section class="msger" style="margin: 0 auto; width: 95%; height:calc( 100vh - 65px );">
        <header class="msger-header">
        <i class="bi bi-arrow-left-circle-fill" style="font-size:23pt; cursor:pointer;" onclick="window.location='/'"></i>
        <div style="text-align: center; margin-top:-42px;">
            <nobr style="font-size:23pt;">Chat with <?php $qsearch=$_GET['q']; $array = mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE phonenumber='$qsearch'")); echo $array[1]." ".$array[2];?></nobr>
        </div>
        </header>

        <main class="msger-chat" id="ms-content">
        </main>
        <form class="msger-inputarea" id="formsubmitfalse">
            <input type="text" class="msger-input" placeholder="Enter your message..." maxlength="250" minlength="0">
            <button type="submit" class="msger-send-btn">Send</button>
        </form>
    </section>
</body>

</html>