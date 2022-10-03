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

    <h1 class="headerS">Register</h1>

    <?php flash('register') ?>

    <p class="psign">Please fill this form to create an account</p>

    <form class="formsign" action="controllers/users.php" method="post">
        <input type="hidden" name="type" value="register">
        <input type="text" name="firstname" placeholder="First name">
        <input type="text" name="lastname" placeholder="Last name">
        <input type="text" name="username" placeholder="Username">
        <input type="text" name="email" placeholder="Email">
        <input type="password" name="pwd" placeholder="Password">
        <input type="password" name="pwdrepeat" placeholder="Repeat Password">
        <button type="submit" name="signup-btn">SIGN UP</button>
        <button type="submit" name="reset"><a href="reset-password.php">RESET</button>
        <br></br>
        <p> Already have an account?<a href="login.php">&nbspLogin here</a>
    </form>
    </div>
    <?php include_once 'libraries/footer.php'; ?>
</body>
</html>
