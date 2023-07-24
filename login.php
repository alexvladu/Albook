<?php
if(session_status() == PHP_SESSION_NONE)
    session_start();
$login = 0;
if (isset($_SESSION['login']))
    $login = 1;
if ($login)
{
    header("location: /");
    exit();
}
$c = mysqli_connect("localhost", "root", "", "albook");
?>
<html lang="en">

<head>
    <title>alBook</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="description" content="Free and secure chat">
    <meta name="keywords" content="Interact with your friends">
    <meta name="author" content="ALEX VLADU">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=yes">
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <?php
    if (isset($_GET['search'])) {
    }
    ?>
    <style>
        .form_login {
            text-align: center;
            width: 350px;
            margin: 0 auto;
        }
        .alert{
            margin-top: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #resetpswd {
            border: none;
            background-color: #cfe2ff;
            color: #052c65;
            padding: 3.5px;
            text-decoration: underline;
        }

        @media only screen and (max-width: 576px) {
            .form_login {
                width: 85%;
            }
        }
    </style>
    <?php
    ?>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
            <div class="container-fluid" style="user-select: none;">
                <a class="navbar-brand" onclick="window.location='/'" style="cursor: pointer;">alBook</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mynavbar" style="text-align:center; justify-content: end">
                    <ul class="navbar-nav me-auto" style="display: contents">
                        <i>sp</i><br>
                        <button onclick='window.location="login"' type="button" class="btn btn-primary position-relative">
                            Login
                        </button><br>
                        <i>sp</i><br>
                        <button onclick='window.location="register"' type="button" class="btn btn-primary position-relative">
                            Register
                        </button><br>
                    </ul>
                </div>
        </nav>
    </header>
    <main>
        <form name="resetps" id="resetps" method="post"></form>
        <div style="margin: 0 auto;">
            <form class="form_login" method="post" style="user-select: none; text-align:center;">
                <?php
                if (isset($_POST['email']) && isset($_POST['pswd'])) {
                    $email = $_POST['email'];
                    $_SESSION['email_entered'] = $email;
                    $password = $_POST['pswd'];
                    $rez = mysqli_query($c, "SELECT * FROM user WHERE email='$email'");
                    if (mysqli_num_rows($rez)) {
                        $array=mysqli_fetch_array($rez);
                        if ($array[5] == $password) {
                            $_SESSION['login'] = 1;
                            $_SESSION['email'] = $email;
                            $_SESSION['phonenumber'] = $array[3];
                            $_SESSION['prenume'] = $array[1];
                            $_SESSION['nume'] = $array[2];
                            echo $_SESSION['phonenumber'];
                            header("location: /");
                            exit();
                        } else {
                            echo '<div class="alert alert-danger alert-dismissible fade show">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <nobr id="alert_text">Wrong password</nobr>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
                            echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <i class="bi bi-info-circle-fill"></i>
                            <nobr>Recover your password</nobr><a href="#" class="alert-link"><input type=submit id="resetpswd" name="resetpswd" value="here" form="resetps"></a>
                            </div>';
                        }
                    } else echo '<div class="alert alert-danger alert-dismissible align-items-center fade show">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <nobr id="alert_text">Email doesn\'t exist</nobr>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>';
                }
                if (isset($_POST['resetpswd']) && isset($_SESSION['email_entered'])) {
                    $email = $_SESSION['email_entered'];
                    $pswd=mysqli_fetch_array(mysqli_query($c, "SELECT * FROM user WHERE email='$email'"))[5];
                    $subject = "Albook login data";
                    $body = "<html><body>
                    <p>Hello! Below are your login data:</p>
                    <p>Your email: $email</p>
                    <p>Your password: $pswd</p>
                    </body></html>";
                    $head = "MIME-Version: 1.0\r\n";
                    $head .= "Content-type: text/html; charset=utf-8";
                    $ip = $_SERVER['REMOTE_ADDR'];
                    $time = time();
                    $rez_antispam = mysqli_query($c, "SELECT * FROM antispam_mailsend WHERE ip='$ip'");
                    if (mysqli_num_rows($rez_antispam)) {
                        $dif = time() - mysqli_fetch_array($rez_antispam)[1];
                        if ($dif >= 60) {
                            echo '<div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle-fill"></i>
                            <nobr style="margin-left: 4px;" id="sentto">An email was sent to ' . $email . '</nobr>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>';
                            mysqli_query($c, "UPDATE antispam_mailsend SET time='$time' WHERE ip='$ip'");
                            mail($email, $subject, $body, $head);
                        } else{
                            echo '<div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <nobr id="alert_text">You can send another mail in '.(60-$dif).'seconds</nobr>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
                        echo '<div class="alert alert-primary alert-dismissible fade show" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <i class="bi bi-info-circle-fill"></i>
                        <nobr>Recover your password</nobr><a href="#" class="alert-link"><input type=submit id="resetpswd" name="resetpswd" value="here" form="resetps"></a>
                        </div>';
                        }
                    } else {
                        echo '<div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill"></i>
                        <nobr style="margin-left: 4px;" id="sentto">An email was sent to ' . $email . '</nobr>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
                        mysqli_query($c, "INSERT INTO antispam_mailsend (ip, time) VALUES ('$ip', '$time')");
                        mail($email, $subject, $body, $head);
                    }
                }
                ?>
                <div class="mb-3 mt-3">
                    <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pswd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-bottom:16px;">Login</button>
            </form>
        </div>
    </main>
    <footer class="text-center text-white mt-auto" style="background-color: #f1f1f1; user-select:none; ">

    </footer>

</body>

</html>