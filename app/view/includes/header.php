<?php ?><header>
    <nav>
        <div>
            <ul class="menu-main">
                <li><a href="index.php">HOME </a></li>
                <li><a href="about.php">ABOUT </a></li>
                <?php
                    //$_SESSION['username'] = 1;
                    if (!isset($_SESSION['username'])) { //codigo para caso user n logado
                        echo '
                            <ul class="menu-member">
                            <li><a class="logs" href="signup.php">SIGN UP</a></li>
                            <li><a class="logs" href="login.php">LOGIN</a></li>';
                    }else 
                        echo '<li><a href="../controllers/users.php?q=logout">Logout</a></li>';
                ?>
            </ul>
        </div>
    </nav>
</header>
