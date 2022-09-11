<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name= "viewport" content="width=device-width, initial-scale=1.0">
    <title>Stigy's Online Store</title>
    <link rel="stylesheet" href="./style.css" type="text/css">
</head>
<body>
    <nav>
            <a href="index.php"><li>Home</li></a>
            <?php if(!isset($_SESSION['username'])) : ?>
                <a href="view/signup.php"><li>Sign Up</li></a>
                <a href="view/login.php"><li>Login</li></a>
            <?php else: ?>
                <a href="./controllers/users.php?q=logout"><li>Logout</li></a>
            <?php endif; ?>
        </ul>
    </nav>