<?php

?>
<header>
    <div class="container-fluid">
        <div class="row  justify-content-between">
            <div class="col-md-4 links-menu">
            <a href="index.php"><i class="bi bi-house-fill"></i></a>
                       <a href="about.php"><i class="bi bi-info-circle-fill"></i></a>
                        <a href="shop.php"><i class="bi bi-shop-window"></i></a>
            </div>
            <div class="col-md-8 ">
                <nav class="nav-login">
                
                    <ul class="menu-main">
                        <?php 
                            if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
                                ?>
                                <h1 id="Welcome">welcome <?= $_SESSION['username']; ?></h1>
                                <?php                       
                            }
                        ?>
                        
                        <?php
                            //$_SESSION['username'] = 0;
                            if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
                                ?>
                                    <li><a class="logs" href="login.php">LOGIN</a></li>
                                    <li><a class="logs" href="signup.php">SIGN UP</a></li>
                                <?php
                            }else{
                                ?>
                                    <li><a class="logs" href="controllers/users.php?q=logout">Logout</a></li>
                                    <li><a class="logs" href="update-profile.php">Profile</a></li>
                                <?php
                            }
                        ?>
                    </ul>
                
                </nav>
            </div>
        </div>
    </div>
</header>
