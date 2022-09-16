<?php
    include_once 'libraries/start.php';
    include_once 'helpers/session_helper.php';
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once "libraries/head.php";?>
    </head>
    <body>
        <?php include_once "libraries/header.php";?>

        <h1 class="header">Login</h1>

            <?php flash('login');?>

            <form class="formlogin" action="controllers/users.php" method="post">
            
                <input type="hidden" name="type" value="login">
                <input type="text" name="username" placeholder="Username/Email">
                <input type="password" name="pwd" placeholder="Password">
                <button type="submit" name="login-btn">LOGIN</button>
                <div class="form-msg">Not yet a member?<a href="signup.php">Sign Up</a></div>
                <div><a href="reset-password.php">Forgot Password?</a></div>
            </form>
         
        <?php include_once "libraries/footer.php";?>
    </body>
</html>
