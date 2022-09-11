<?php
    include_once './view/header.php';
    include_once './helpers/session-helper.php';
    
?>
            <h1 class="header">Login</h1>

            <?php flash('login')?>

            <form action="./controllers/users.php" method="post">
                <input type="hidden" name="type" value="login">
                <input type="text" name="name/email" placeholder="Username/Email">
                <input type="password" name="pwd" placeholder="Password">
                <button type="submit" name="submit">Log in</button>
            </form>

            <div class="form-msg"><a href="./reset-password.php">Forgot Password?</a></div>
<?php
    include_once 'view/footer.php';
?>