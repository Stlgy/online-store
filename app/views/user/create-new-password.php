<?php
include_once 'libraries/start.php';
include_once 'helpers/session_helper.php';
/* include_once 'controllers/usersController.php'; */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "libraries/head.php"; ?>
    <link rel="stylesheet" href="template/css/style_log.css" type="text/css">
</head>

<body>
    <?php include_once "libraries/header.php"; ?>
    
    <div class="container-fluid">
        <div class="row main-content bg-sucess text-ceter">
            <div class="col-md-8 col-xs-12 col-sm-12 resetpwd__form ">
                <div class="container-fluid">
                    <div class="row">
                       <?php 
                            
                            if(empty($_GET['selector']) || empty($_GET['validator'])){
                                echo 'Could not validate your request!';
                                
                            }else{   # check if tokens are inside URL
                                $selector = $_GET['selector'];
                                $validator = $_GET['validator'];//token that validates user inside the url
                                if(ctype_xdigit($selector) && ctype_xdigit($validator)) 
                                {
                                    ?>                               
                                        <?php flash('newReset'); ?>
                                            <form class="" action=controllers/usersController.php method="POST">
                                            
                                                <input type="hidden" name="type" value="reset"/>
                                                <input type="hidden" name="selector" value="<?php echo $selector?>"/>
                                                <input type="hidden" name="validator" value="<?php echo $validator?>"/>
                                                <input type="password" name="pwd" placeholder="Enter new password">
                                                <input type="password" name="pwdrepeat" placeholder="Repeat new password">

                                                <button type="submit" name="reset-pwd-submit">SUBMIT</button>                             
                                            </form>
                        <?php
                        }
                                else
                                {
                                    echo 'Could not validate your request!'; //if selector  & validator not in hexadecimal form
                                }
                            }
                        ?>       
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <!-- Footer -->
        <!-- <div class="container-fluid text-center footer"> -->
        <?php 
            include_once "libraries/footer.php"; 
        ?>                    
    </div>
</body>
<html>
                       
