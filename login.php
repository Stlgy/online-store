<?php
include_once 'libraries/start.php';
include_once 'helpers/session_helper.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "libraries/head.php"; ?>
    <link rel="stylesheet" href="public/css/style_log.css" type="text/css">
</head>

<body>
    <?php include_once "libraries/header.php"; ?>

    <div class="container-center">
        <div class="row main-content text-center">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 login__form ">
                <br>
                <h2>Log In</h2>
                <br>
                <?php //flash('login'); ?>

                <form class="form__login" action="controllers/users.php" method="post">
                    <input type="hidden" name="type" value="login">
                    <div class="row">
                        
                        <input type="text" name="username" id="username" class="form__input " placeholder="Username/Email">
                        <input type="password" name="pwd" id="password" class="form__input" placeholder="Password">
                    </div>
                    <div class="row-submit">
                        <input type="submit" value="LOGIN" class="btn btn-pequeno">
                        <div class="">
                            <a href="reset-password.php" role="button" id="btns">RESET PASSWORD</a>
                        </div>
                </form>
            </div>
            <div class="row-miss">
                <p>Don't have an account? <br><a href="./signup.php" id="btnsl">SIGNUP</a></p>
            </div>
        </div>
    </div>   
    <div>
        <!-- Footer -->
        <!-- <div class="container-fluid text-center footer"> -->
        <?php include_once "libraries/footer.php"; ?>
    </div>
</body>
<html>
