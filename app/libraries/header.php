<?php

?>
<header>
    <nav>
        <div>
            <ul class="menu-main">
                <?php 
                    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
                        ?>
                        <h1 id="Welcome">welcome <?= $_SESSION['username']; ?></h1>
                        <?php
                        
                    }
                ?>
                <li><a href="index.php"><i class="bi bi-house-fill"></i></a></li>
                <li><a href="about.php"><i class="bi bi-info-circle-fill"></i></a></li>
                <li><a href="about.php"><i class="bi bi-shop-window"></i></a></li>
                <?php
                    //$_SESSION['username'] = 0;
                    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
                        ?>
                            <li><a class="logs" href="signup.php">SIGN UP</a></li>
                            <li><a class="logs" href="login.php">LOGIN</a></li>
                        <?php
                    }else{
                        ?>
                            <li><a class="logs" href="controllers/users.php?q=logout">Logout</a></li>
                        <?php
                    }
                ?>
            </ul>
        </div>
    </nav>
</header>
