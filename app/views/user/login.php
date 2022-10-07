<?php
include_once '../../libraries/start.php';
include_once '../../helpers/session_helper.php';
/* echo realpath('.');  */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../layouts/head.php"; ?>
    <link rel="stylesheet" href="template/css/style_log.css" type="text/css">
</head>

<body>
    <?php include_once "../layouts/header.php"; ?>

    <div class="container-center">
        <div class="row main-content text-center">
            <div class="col-12 login__form ">
               
                <h2>Log In</h2>
                 <br>
                <?php flash('login'); ?> 

                <form class="form__login" action="controllers/usersController.php" method="post">
                    <input type="hidden" name="type" value="login">
                    <div class="row">
                        <input type="text" name="username" id="username" class="form__input" placeholder="Username/Email">
                        <input type="password" name="pwd" id="password" class="form__input" placeholder="Password">
                    </div>
                    <div class="row-submit">
                        <input type="submit" value="LOGIN" class="btn">
                        <div class="">
                            <a href="<?= VIEWS_USER;?>/reset-password.php" role="button" id="btns">RESET PASSWORD</a>
                        </div>
                </form>
            </div>
            <div class="row-miss">
                <p>Don't have an account? <br><a href="views/user/signup.php" id="btnsl">SIGNUP</a></p>
            </div>
        </div>
    </div>   
    <div>
        <!-- Footer -->
        <!-- <div class="container-fluid text-center footer"> -->
        <?php include_once "../layouts/footer.php"; ?>
    </div>
</body>
<html>
