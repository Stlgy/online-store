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
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 resetpwd__form ">
                <div class="container-fluid">

                    <h2>Reset your password</h2><br>
                    <p>An e-mail will be send to you with instructions on how to reset your password</p>

                    <?php flash('reset'); ?>

                    <form class="form__resetpwd1" action="controllers/users.php" method="post">
                        <input type="hidden" name="type" value="send">
                        <div class="row">
                            <input type="text" name="email" class="form__input" placeholder="Enter your e-mail address">
                        </div>
                        <div class="row-submit">
                            <button type="submit" class="btn" id="btns" name="reset-request-submit" >RECEIVE EMAIL</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <!-- <div class="container-fluid text-center footer"> -->
    <?php include_once "libraries/footer.php"; ?>
    </div>
</body>
<html>
