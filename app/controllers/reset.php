<?php
    require_once '../libraries/start.php';
    include('../libraries/class_utils.php');

    if(isset($_POST["reset-pwd-submit"])){
        $selector          = $_POST["selector"];
        $validator         = $_POST["validator"];
        $password          = $_POST["pwd"];
        $passwordRepeat    = $_POST["pwd-repeat"];

        if(empty($password) || empty( $passwordRepeat)){
            redirect('../create-new-password.php');
        }else if($password !=  $passwordRepeat){
            redirect('../create-new-password.php');
            exit();
        }
        $currentDate =date("_U");

        $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
        if(!mysqli_stmt_prepare($stmt, "SELECT * FROM " . BDPX . "_pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >=?")){
            echo "there was an error!!";
                exit();
        }else{
            mysqli_stmt_bind_param($stmt, "s", $selector);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            if(!$row = mysqli_fetch_assoc($result)){
                echo "Something went wrong with your reset password request";
                exit();
            }else{
                $tokenb = hex2bin($validator)   ;
                $tokencheck = password_verify($tokenb, $row["pwdResetToken"]);
                
                    if($tokencheck === false){
                        echo "Something went wrong with your reset password request";
                        exit();
                    }elseif($tokencheck === true){
                        $tokenEmail = $row['pwdResetEmail'];

                        $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
                        if(!mysqli_stmt_prepare($stmt, "SELECT * FROM users WHERE email=?")){
                            echo "there was an error!!";
                            exit();
                        }else{
                            mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            if(!$row = mysqli_fetch_assoc($result)){
                                echo "There was an error";
                                exit();
                            }else{

                                $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
                                if(!mysqli_stmt_prepare($stmt, "UPDATE " . BDPX . "_users SET pwd=? WHERE email=?")){
                                    echo "there was an error!!";
                                    exit();

                                }else{
                                    $newpwdhash = password_hash($password, PASSWORD_DEFAULT);
                                    mysqli_stmt_bind_param($stmt, "ss", $newpwdhash, $tokenEmail);
                                    mysqli_stmt_execute($stmt);

                                    $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
                                    if(!mysqli_stmt_prepare($stmt,"DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail=?")){
                                        echo "there was an error!!";
                                        exit();
                                    }else{
                                        mysqli_stmt_bind_param($stmt, "s", $tokenEmail);
                                        mysqli_stmt_execute($stmt);
                                        redirect('../login.php');
                                        flash("update","Password updated");
                                    }
                                }
                            }
                        }
                    }
            }
        }      
    }else{
        redirect('../index.php');
    }
?>
