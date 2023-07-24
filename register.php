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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <script src="jquery.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.4/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <?php
    $error_email = 0;
    $error_phone = 0;
    if (isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['phone']) && isset($_POST['email']) && isset($_POST['pswd']) && isset($_POST['country'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email'];
        $password = $_POST['pswd'];
        $country = $_POST['country'];
        $mail_exist = mysqli_query($c, "SELECT email FROM user WHERE email='$email'");
        if (mysqli_num_rows($mail_exist))
            $error_email = 1;
        $phone = $_POST['phone'];
        $phone_exist = mysqli_query($c, "SELECT phonenumber FROM user WHERE phonenumber='$phone'");
        if (mysqli_num_rows($phone_exist))
            $error_phone = 1;
        if ($error_email || $error_phone) {
            echo "<script>
            $(document).ready(function() {
                $('#firstname').val('$firstname');";

            echo "$('#lastname').val('$lastname');";
            if(!$error_email)
                echo "$('#email').val('$email');";
            if(!$error_phone)
                echo "$('#phone').val('$phone');";
            echo "$('#country').val('$country');";
            echo "
            });
            </script>";
        } else {
            mysqli_query($c, "INSERT INTO user (prenume, nume, phonenumber, email, password, country) VALUES ('$firstname', '$lastname', '$phone', '$email', '$password', '$country')");
            $_SESSION['login'] = 1;
            $_SESSION['email'] = $email;
            $_SESSION['phonenumber']=$phone;
            $_SESSION['prenume']=$firstname;
            $_SESSION['nume']=$lastname;
            header("location: /");
            exit();
        }
    }
    ?>
    <script>


    </script>
    <style>
        #alertphone, #alertemail{
            margin-top: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .form_register {
            text-align: center;
            width: 350px;
            margin: 0 auto;
        }

        @media only screen and (max-width: 576px) {
            .form_register {
                width: 85%;
            }
        }
    </style>
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
        <div style="margin: 0 auto;">
            <form method="POST" class="form_register">
                <?php
                if($error_phone)
                    echo '<div class="alert alert-danger alert-dismissible fade show" id="alertphone">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <nobr id="alert_text">Phonenumber already in use!</nobr>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
                if($error_email)
                    echo '<div class="alert alert-danger alert-dismissible fade show" id="alertemail">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <nobr id="alert_text">Email already in use!</nobr>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>';
                ?>
                <div class="mb-3 mt-3">
                    <input type="text" class="form-control" id="firstname" placeholder="Enter firstname" name="firstname" required>
                </div>
                <div class="mb-3 mt-3">
                    <input type="text" class="form-control" id="lastname" placeholder="Enter lastname" name="lastname" required>
                </div>
                <div class="mb-3 mt-3">
                    <input type="text" class="form-control" id="phone" placeholder="Enter Phonenumber" name="phone" required pattern="[0-9]+" maxlength="10" minlength="10">
                </div>
                <div class="mb-3 mt-3">
                    <input type="email" class="form-control" id="email" placeholder="Enter email" name="email" required>
                </div>
                <div class="mb-3">
                    <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pswd" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                </div>
                <div class="mb-3 mt-3">
                    <input type="text" class="form-control" id="country" placeholder="Enter country" name="country" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
        </div>
    </main>
    <footer class="text-center text-white mt-auto" style="background-color: #f1f1f1; user-select:none; ">

    </footer>

</body>

</html>