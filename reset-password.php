<?php
include_once 'libraries/start.php';
include_once 'helpers/session_helper.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "libraries/head.php"; ?>
</head>

<body>
    <?php include_once "libraries/header.php"; ?>
    <link rel="stylesheet" href="public/css/style_log.css" type="text/css">


    <div class="container-fluid">
        <div class="row main-content bg-sucess text-ceter">
            <div class="col-md-8 col-xs-12 col-sm-12 resetpwd__form ">
                <div class="container-fluid">
                    <div class="row">
                        <h2>Recover Password</h2>
                        <?php flash('reset'); ?>
                    </div>
                    <div class="row">
                        <form class="form__resetpwd" action="controllers/users.php" method="post">
                            <input type="hidden" name="type" value="resetPwd">
                            <div class="row">
                                <input type="text" name="email" class="form__resetpwd" placeholder="Email">
                            </div>
                    </div>
                    <div class="row-submit">
                        <button type="submit" class="btn" name="forgot-pwd">
                            Receive Email
                        </button>
                    </div>
                </div>
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

