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

    <div class="container-fluid">
        <div class="row main-content bg-success text-center">
            <!-- <div class="col-md-4 text-center company__info"> -->

            <!-- <div class="col-md-4 text-center company__info">
                            <span class="company__logo"><h2><span class="fa fa-android"></span></h2></span>
				        <h4 class="company_title">Shop Logo</h4>
			            </div> -->
            <div class="col-md-8 col-xs-12 col-sm-12 login__form ">
                <div class="container-fluid">
                    <div class="row">
                        <h2>Log In</h2>
                        <?php flash('login'); ?>
                    </div>
                    <div class="row">
                        <form class="form__login" action="controllers/users.php" method="post">
                            <input type="hidden" name="type" value="login">
                            <div class="row">
                                <input type="text" name="username" id="username" class="form__input" placeholder="Username/Email">
                                <input type="password" name="pwd" id="password" class="form__input" placeholder="Password">
                            </div>
                    </div>
                    <div class="row-submit">
                        <input type="submit" value="Login" class="btn btn-pequeno">
                        <button type="button" class="btn"><a class="fixclr" href="reset-password.php">Reset Password</a><button>
                    </div>
                    </form>
                </div>
                <div class="row-miss">
                    <p>Don't have an account? <br><a class="fixclr" href="signup.php">Signup</a></p>
                </div>
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
