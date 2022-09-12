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

    <h1 class="header">Reset your Password</h1>

    <form action="rstpwd" method="post">
        <input type="text" name="email" placeholder="Email">
        <button type="submit" name="submit">Receive Email</button>
    </form>
    <br></br>
            <br></br>

        <?php include_once 'includes/footer.php';?>
    </body>
</html>
