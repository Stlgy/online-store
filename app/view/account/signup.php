<?php
    include_once 'includes/start.php';
    include_once '../helpers/session_helper.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include_once "includes/head.php";?>
    </head>
    <body>
        <?php include_once "includes/header.php";?>

        <h1 class="headerS">Sign up</h1>

            <?php flash('register')?>

            <p class="psign">Don't have an account yet? <br>Sign up here</p>

            <form class="formsign" action="../controllers/users.php" method="post">
                <input type="hidden" name="type" value="register">
                <input type="text" name="firstname" placeholder="First name">
                <input type="text" name="lastname" placeholder="Last name">
                <input type="text" name="username" placeholder="Username">
                <input type="text" name="email" placeholder="Email">
                <input type="password" name="pwd" placeholder="Password">
                <input type="password" name="pwdrepeat" placeholder="Repeat Password">
                <button type="submit" name="submit">SIGN UP</button>
                <br></br>                 
            </form>
        <?php include_once 'includes/footer.php';?>
    </body>
</html>
