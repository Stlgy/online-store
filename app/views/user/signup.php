<?php

include_once '../../libraries/start.php';
include_once '../../helpers/session_helper.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../layouts/head.php"; ?>
    <link rel="stylesheet" href="template/css/style_log.css" type="text/css">
</head>

<body>
    <?php include_once "../layouts/header.php"; ?>

    <div class="container-fluid">
        <div class="row main-content text-center">
            <div class="col-12 signup__form ">
                <div class="container-fluid">
                  <!--   <div class="row" > -->
                        <!-- <h1 class="headerS">Register</h1> -->
                        <h2>Register</h2>
                        <p class="psign">Please fill this form to create an account</p>

                
                        <!-- <div class="row"> -->
                        <?php flash('register'); ?>
                        <form class="form__signup" action="controllers/usersController.php" method="post">
                            <input type="hidden" name="type" value="register">

                            <input type="text" name="firstname" class="form__input" placeholder="First name">
                            <input type="text" name="lastname"  class="form__input" placeholder="Last name">
                            <input type="text" name="username"  class="form__input" placeholder="Username">
                            <input type="text" name="email"     class="form__input" placeholder="Email">
                            
                            <input  type="password" name="pwd"        class="form__input" placeholder="Password">
                            <input  type="password" name="pwdrepeat"  class="form__input" placeholder="Repeat Password">
                            <button type="submit"  name="signup-btn" class="btn-sign">SIGN UP</button>
                            
                        </form>
                            <br>
                            <p> Already have an account?<br><a href="views/user/login.php" id="btnsl">LOGIN HERE</a>
                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div.>

    <div>
    <?php include_once '../layouts/footer.php'; ?>
    </div>
</body>
</html>
