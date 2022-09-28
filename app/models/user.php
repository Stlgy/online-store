<?php
require_once '../libraries/start.php';

include('../libraries/class_utils.php');

class User extends sys_utils
{

    ###FIND USER BY EXIXTING  EMAIL || USERNAME
    public function findUsername($array, $modo)
    {
        $payload = []; //Instanciar a variavel de resultado para não dar erros desnecessários.

        if ($modo == "signup") {
            $query = "SELECT * FROM " . BDPX . "_users WHERE email  = '" . $array[0] . "' OR username = '" . $array[1] . "'";
        } else {
            $query = "SELECT * FROM " . BDPX . "_users WHERE email  = '" . $array[0] . "' OR username = '" . $array[0] . "'";
        }

        $res = SQL::run($query);
      /*   echo "<pre>";
            print_r($res); 
            echo "</pre>"; 
        die(); */
        if ($res && $res->num_rows > 0) { //Também pode ser $res->num_rows == 1 dado que só queremos 1 row e os emails e usernames deverão ser UNIQUE na tabela.
            $payload = $res->fetch_assoc(); //Associar os resultados MySQL a uma variavel php (array)
        }
       /*  echo "<pre>";
            print_r($payload); 
            echo "</pre>"; 
            die(); */
        return $payload;
    }

    ### REGISTER USER
    public function register($data): bool
    {   //only 1 arg all data is stored in array
        $res = SQL::run("INSERT INTO " . BDPX . "_users(firstname, lastname, username, email, pwd)
            VALUES (
                '" . $data['firstname'] . "',
                '" . $data['lastname'] . "', 
                '" . $data['username'] . "', 
                '" . $data['email'] . "', 
                '" . $data['pwd'] . "')");
        //echo SQL::$error;
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
    ### LOGIN USER   
    public function login($name, $password)
    {
        $result = false;

        if (!empty($row = $this->findUsername([$name], "login"))) {
            $hashedPassword = $row['pwd'];
            if (password_verify($password, $hashedPassword)) $result = $row;
        }
        return $result;
    }
    ##Delete exixting TOKEN from user 
    public function deleteEmail(){
        $userEmail = filter_var($_POST["email"],FILTER_SANITIZE_STRING);
        //$res = SQL::runPrepareStmt("DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail=?", "s", [$userEmail]);
        $res = SQL::run("DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail='$userEmail'");
        //echo "DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail='$userEmail'";
        //echo SQL::$error;
        //var_dump($res);
        //var_dump($_POST);
        return $res;
    }
    public function insertToken($userEmail, $selector, $hashedToken, $expires){
        /* $res = SQL::runPrepareStmt("INSERT INTO " . BDPX . "_pwdReset (pwdResetId,pwdResetEmail,pwdResetSelector,pwdResetToken,pwdResetExpires) VALUES 
            (NULL,?,?,?,?)", "ssss", [$userEmail, $selector, $hashedToken, $expires]); */

        
        
        $res = SQL::run("INSERT INTO " . BDPX . "_pwdReset (pwdResetId,pwdResetEmail,pwdResetSelector,pwdResetToken,pwdResetExpires) 
        VALUES ('','$userEmail', '$selector', '$hashedToken', '$expires')");
        
        if($res){
            return true;
        }else{
            return false;
        }
    }
    ## RESET PWD - GET TIME

    public function resetPassword($selector, $currentDate)
    {
        /* $resultado = false;
        
        $res = SQL::runPrepareStmt("SELECT * FROM " . BDPX . "_pwdReset WHERE pwdResetSelector=? AND pwdResetExpires>=?", "ss", [$selector, $currentDate]);
        if($res && $res->num_rows == 1){ //Se o $res for verdadeiro e houver uma row com dados
            $resultado = $res->fetch_assoc();//fetch_assoc() ao resultado do sql para colocar tudo num array */
        $result=[];
        $res = SQL::run("SELECT * FROM " . BDPX . "_pwdReset WHERE pwdResetSelector='$selector' AND pwdResetExpires>='$currentDate'");
        //echo "SELECT * FROM " . BDPX . "_pwdReset WHERE pwdResetSelector='$selector' AND pwdResetExpires>='$currentDate'";
        if($res && $res->num_rows > 0){
            $result = $res->fetch_assoc();
            
            echo SQL::$error;
            //var_dump($res);
           /*  echo "<pre>";
            print_r($res); 
            echo "</pre>";
            die();  */
        } 
        
        return $result; 
    }
    public function updatePassword($newPwdHash, $tokenEmail)
    {
        
        /* $res = SQL::runPrepareStmt("UPDATE " . BDPX . "_users SET pwd=?", "ss", [$newPwdHash, $tokenEmail]); */
        $res = SQL::run("UPDATE " . BDPX . "_users SET pwd='$newPwdHash','$tokenEmail'");
        /* echo "<pre>";
            print_r($res); 
            echo "</pre>";
            die(); */
        return $res;
    }
}
 
?>
