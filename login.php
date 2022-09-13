<?php
    include_once 'app/libraries/start.php';
    include_once 'app/helpers/session_helper.php';
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once "app/libraries/head.php";?>
    </head>
    <body>
        <?php include_once "app/libraries/header.php";?>

        <h1 class="header">Login</h1>

            <?php flash('login');?>

            <form class="formlogin" action="app/controllers/users.php" method="post">
                <input type="hidden" name="type" value="login">
                <input type="text" name="username" placeholder="Username/Email">
                <input type="password" name="pwd" placeholder="Password">
                <button type="submit" name="submit">LOGIN</button>
            </form>

            <div class="form-msg"><a href="reset-password.php">Forgot Password?</a></div>

        <?php include_once "app/libraries/footer.php";?>
    </body>
</html>
