<?php

    include_once 'view/header.php';
?>

    <h1 id= "index-text">Stigy's Online Shop <br> 
        <?php if(isset($_SESSION['username'])){
            echo ($_SESSION['username']);
        }else{
            echo 'Guest';
        }
        ?></h1>



<?php
    include_once 'view/footer.php';
?> 



<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name= "viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <nav>
        <div>
            <h3>Online Store</h3>
            <ul class="menu-main">
                <li><a href="index.php">HOME</a></li>
                <li><a href="index.php">PRODUCTS</a></li>
                <li><a href="index.php">SALES</a></li>
            </ul>
        </div>
        <ul class="menu-member">
            <li><a href="#"SIGN IN</a></li>
            <li><a href="#" class=""> LOGIN</a></li>
        </ul>
    </nav>
</header>

<section class="index-intro">
    <div class="index-intro-bg">
        <div class="wrapper">
            <h2>We have all sort of products</h2>
            <a href="#">FIND OUT PRODUCTS</a>
        </div>
    </div>
</section>

<section class="index-login">
    <div class="wrapper">
        <div class="index-login-signup">
            <h4>SIGN UP</h4>
            <p>Don't have an account yet? Sign uo here</p>
            <form action="" method="post">
                <input type="hidden" name="type" value="register">
                <input type="text" name="firstname" placeholder="First name">
                <input type="text" name="lastname" placeholder="Last name">
                <input type="text" name="userid" placeholder="Username">
                <input type="text" name="email" placeholder="E-mail">
                <input type="password" name="pwd" placeholder="Password">
                <input type="password" name="pwdrepeat" placeholder="Repeat Password">
                
                <br>
                <button type="submit" name="submit">SIGN UP</button>
            </form>
        </div>
        <div class="index-login-login">
            <h4>LOGIN</h4>
            <p>Don't have an account yet? Sign uo here</p>
            <form action="includes/login.php" method="post">
                <input type="text" name="userid" placeholder="Username">
                <input type="password" name="pwd" placeholder="Password">
                <br>
                <button type="submit" name="submit">LOGIN</button>
            </form>
        </div>
    </div>
</section>

</body>
</html>-->

