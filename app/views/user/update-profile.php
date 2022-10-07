<?php
include_once '../../libraries/start.php';
include_once '../../helpers/session_helper.php';

    $_SESSION["getData"] = 1;
    require_once '../../models/user.php';
    include('../../controllers/usersController.php');
   // $usys = new Users;
    
    $data = $init->getProfile();
?>
<!DOCTYPE html>
<html lang="en">

<head>
   <?php include_once "../layouts/head.php"; ?>
    <link rel="stylesheet" href="template/css/style_log.css" type="text/css">
</head>

<body>
    <?php include_once "../layouts/header.php"; ?>
  
        <div class="container-center">
            <div class="row main-content ">
                <div class="col-12 login__form ">
                    <br>
                    <h2>Edit Profile</h2>
                    <br>

                    <form class="form__edit" action="controllers/usersController.php" method="post">
                        <input type="hidden" name="type" value="update">
                        <div class="row">
                            <label>Firstname</label>
                            <input type="text" name="firstname" id="firstname" class="form__input" value="<?php echo $data['firstname']?>" >
                            <label>Lastname</label>
                            <input type="text" name="lastname"  id="lastname"  class="form__input" value="<?php echo $data['lastname']?>"  >
                            <label>Username</label>
                            <input type="text" name="username"  id="username"  class="form__input" value="<?php echo $data['username']?>"  >
                            <label>Email</label>
                            <input type="text" name="email"     id="email"     class="form__input" value="<?php echo $data['email']?>"     >
                            <label>Password</label>
                            <input type="password" name="pwd"       class="form__input" value="">
                            <label>Password repeat</label>
                            <input type="password" name="pwdrepeat" class="form__input" value="">
                        </div>
                        <div class="row-submit">
                            <button type="submit" name="update-submit" value="yes">SUBMIT</button>

                    </form>
                </div>
            </div>
        </div>
   
    <div>
        <!-- Footer -->
        <!-- <div class="container-fluid text-center footer"> -->
        <?php include_once "../layouts/footer.php"; ?>
    </div>
</body>
<html>
