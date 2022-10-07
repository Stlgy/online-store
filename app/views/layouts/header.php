<?php
/* echo realpath('.'); */
?>
<header>
    <div class="container-fluid">
        <div class="row  justify-content-between">
            <div class="col-md-4 links-menu">
            <a href="index.php"><i class="bi bi-house-fill"></i></a>
            <a href="../site/about.php"><i class="bi bi-info-circle-fill"></i></a>
            <a href="../product/view.php"><i class="bi bi-shop-window"></i></a>
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
                                    <li><a class="logs" href="views/user/login.php">LOGIN</a></li>
                                    <li><a class="logs" href="views/user/signup.php">SIGN UP</a></li>
                                <?php
                            }else{
                                ?>  
                                    <li><a class="logs" href="controllers/cartController.php?q=cart">Cart</a></li>
                                    <li><a class="logs" href="<?= VIEWS_USER;?>/logout.php">Logout</a></li>
                                    <li><a class="logs" href="<?= VIEWS_USER;?>/update-profile.php">Settings</a></li>
                                <?php
                            }
                        ?>
                    </ul>
                
                </nav>
            </div>
        </div>
    </div>
</header>
