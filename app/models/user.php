<?php
//require_once '../libraries/start.php';
if ($_SESSION["getData"] == 0) {
    require_once '../libraries/start.php';
    include('../libraries/class_utils.php');
}else{
    require_once 'libraries/start.php';
}


class User extends sys_utils{

    ###FIND USER BY EXISTING  EMAIL || USERNAME
    public function findUsername($array, $modo) {

        $payload = []; //Instanciar a variavel de resultado para não dar erros desnecessários.

        if ($modo == "signup") {
            $query = "SELECT * FROM " . BDPX . "_users WHERE email  = '" . $array[0] . "' OR username = '" . $array[1] . "'";
        } else {
            $query = "SELECT * FROM " . BDPX . "_users WHERE email  = '" . $array[0] . "' OR username = '" . $array[0] . "'";
        }

        $res = SQL::run($query);

        if ($res && $res->num_rows > 0) { //Também pode ser $res->num_rows == 1 dado que só queremos 1 row e os emails e usernames deverão ser UNIQUE na tabela.
            $payload = $res->fetch_assoc(); //Assoc MySQL result into  php $ (array)
        }
        return $payload;
    }
    public function findEmail($tokenEmail) {

        $result=[];
        $res = SQL::run("SELECT * FROM " . BDPX . "_users WHERE email='$tokenEmail'");
        if($res && $res->num_rows > 0){
            $result = $res->fetch_assoc();
        }
        return $result;
    }

    ### REGISTER USER
    public function register($data): bool { //only 1 arg all data is stored in array

        $res = SQL::run("INSERT INTO " . BDPX . "_users(firstname, lastname, username, email, pwd)
            VALUES (
                '" . $data['firstname'] . "',
                '" . $data['lastname'] . "', 
                '" . $data['username'] . "', 
                '" . $data['email'] . "', 
                '" . $data['pwd'] . "')");
        
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    ### LOGIN USER   
    public function login($name, $password) {
        
        $result = false;

        if (!empty($row = $this->findUsername([$name], "login"))) {
            $hashedPassword = $row['pwd'];
            if (password_verify($password, $hashedPassword)) $result = $row;
        }
        return $result;
    }

    ### DELETE PREVIOUS EMAIL-TOKEN FROM RESET TABLE IF THERE'S ONE
    public function deleteEmail() {

        $userEmail = filter_var($_POST["email"],FILTER_SANITIZE_STRING);
        $res = SQL::run("DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail='$userEmail'");
        return $res;
    }

    ### INSERT TOKEN FOR PWD RESET
    public function insertToken($userEmail, $selector, $hashedToken, $expires) {
        
        $res = SQL::run("INSERT INTO " . BDPX . "_pwdReset (pwdResetId,pwdResetEmail,pwdResetSelector,pwdResetToken,pwdResetExpires) 
            VALUES ('','$userEmail', '$selector', '$hashedToken', '$expires')");
        
        if($res){
            return true;
        }else{
            return false;
        }
    }

    ### AFTER USAGE DELETE TOKEN
    public function deleteToken($tokenEmail) {

        $res = SQL::run("DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail='$tokenEmail'");
        if($res){
            return true;
        }else{
            return false;
        }
    }

    ### RESET PWD - GET TIME
    public function resetPassword($selector, $currentDate) {
       
        $result=[];
        $res = SQL::run("SELECT * FROM " . BDPX . "_pwdReset WHERE pwdResetSelector='$selector' AND pwdResetExpires>='$currentDate'");
       
        if($res && $res->num_rows > 0){
            $result = $res->fetch_assoc();
        } 
        return $result; 
    }

    ### UPDATE PWD
    public function updatePassword($newPwdHash, $tokenEmail) {

        $res = SQL::run("UPDATE " . BDPX . "_users SET pwd='$newPwdHash' WHERE email='$tokenEmail'");

        if($res){
            return true;
        }else{
            return false;
        }
    }

    ### GET DATA FROM PROFILE
    public function getProfile($id) {

        $result=[];
        $res =SQL::run("SELECT * FROM " . BDPX . "_users WHERE id_u='$id'");
        
        if($res && $res->num_rows > 0){
            $result = $res->fetch_assoc();
            
        }
        
        return $result; 
    }
    
    ### UPDATE PROFILE
    public function updateProfile($data,$id){

        $res=SQL::run("UPDATE " . BDPX . "_users SET 
                        firstname='".$data['firstname']."',
                        lastname='".$data['lastname']."',
                        username='".$data['username']."',
                        email='".$data['email']."',
                        pwd='".$data['pwd']."'
                        WHERE id_u='$id'"); 
                                         
        if ($res) {
            return true;
        } else {
            return false;
        }
       
    }
}
//echo SQL::$error;
