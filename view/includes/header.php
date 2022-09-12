<header>
    <nav>
        <ul class="menu-main">
            <li><a href="index.php">Home </a></li>
            <li><a href="about.php">About </a></li>
            <?php
                //$_SESSION['username'] = 1;
                if (!isset($_SESSION['username'])) { //codigo para caso user n logado
                    echo '<li><a class="logs" href="signup.php">Sign Up</a></li>
                          <li><a class="logs" href="login.php">Login</a></li>';
                }else echo '<li><a href="../controllers/users.php?q=logout">Logout</a></li>';
            ?>
        </ul>
    </nav>
</header>
    
