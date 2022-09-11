<?php
    include_once 'header.php';
    include_once './helpers/session_helper.php';
?>
            <h4>SIGN UP</h4>
            <?php flash('register')?>

            <p>Don't have an account yet? Sign uo here</p>

            <form action="./controllers/users.php" method="post">
                <input type="hidden" name="type" value="register">
                <input type="text" name="firstname" placeholder="First name">
                <input type="text" name="lastname" placeholder="Last name">
                <input type="text" name="username" placeholder="Username">
                <input type="text" name="email" placeholder="Email">
                <input type="password" name="pwd" placeholder="Password">
                <input type="password" name="pwdrepeat" placeholder="Repeat Password">
                
                <br>
                <button type="submit" name="submit">SIGN UP</button>
            </form>

<?php 
    include_once 'footer.php'
?>
