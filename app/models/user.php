<?php
    require_once '../libraries/start.php';
    
    include('../libraries/class_utils.php');

    class User extends sys_utils{
 
        ###FIND USER BY EXIXTING  EMAIL || USERNAME
        public function findUsername($array, $modo)
        {
            $payload = []; //Instanciar a variavel de resultado para não dar erros desnecessários.

            if($modo=="signup")
            {
                $query = "SELECT * FROM " . BDPX . "_users WHERE email  = '".$array[0]."' OR username = '".$array[1]."'";
            }
            else
            {
                $query = "SELECT * FROM " . BDPX . "_users WHERE email  = '".$array[0]."' OR username = '".$array[0]."'";
            }
            
            $res = SQL::run($query);
            if($res && $res->num_rows > 0){ //Também pode ser $res->num_rows == 1 dado que só queremos 1 row e os emails e usernames deverão ser UNIQUE na tabela.
                $payload = $res->fetch_assoc(); //Associar os resultados MySQL a uma variavel php (array)
            }
            /* echo "<pre>";
            print_r($payload); 
            echo "</pre>"; */
            return $payload;
        }
      
        ### REGISTER USER
        public function register($data) :bool
        {   //only 1 arg all data is stored in array
            $res = SQL::run("INSERT INTO " . BDPX . "_users(firstname, lastname, username, email, pwd)
            VALUES (
                '". $data['firstname'] ."',
                '". $data['lastname'] ."', 
                '". $data['username'] ."', 
                '". $data['email'] ."', 
                '". $data['pwd'] ."')");
            //echo SQL::$error;
            if ($res) 
            {
                return TRUE;
            }
            else 
            {
                return FALSE;
            }
        }
        ### LOGIN USER   
        public function login($name, $password)
        {
            $result = false;

            if(!empty($row = $this->findUsername([$name], "login")))
            {
                $hashedPassword = $row['pwd'];
                if(password_verify($password, $hashedPassword)) $result = $row;
            }
            return $result;
        }
        ##Delete exixting TOKEN from user 
        public function deleteToken(){
            //var_dump("morde a foca4");
            $userEmail = $_POST["email"];
            $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
            if(!mysqli_stmt_prepare($stmt,"DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail=?"))
            {
               echo "There was an error!!";
               exit();
            }
            else
            {
               mysqli_stmt_bind_param($stmt, "s", $userEmail);
               mysqli_stmt_execute($stmt);
            }
             mysqli_stmt_close($stmt);
            //mysqli_close(SQL::getInstance()->getConnection());
        }
        public function insertToken(){

            $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
            if(!mysqli_stmt_prepare($stmt, "INSERT INTO " . BDPX . "_pwdReset(pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) 
                VALUES(?, ?, ?, ?);"))
            {
                echo "There was an error!!";
                exit();
            }
            else
            {
                mysqli_stmt_bind_param($stmt, "ssss", $userEmail, $selector, $hashedToken, $expires);
                if(mysqli_stmt_execute($stmt))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            mysqli_stmt_close($stmt);
            

            
        }
        ## RESET PWD - GET TIME
        public function resetPassword($selector,$currentDate)
        {
            $currentDate =date("U");
            $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
            if(!mysqli_stmt_prepare($stmt, "SELECT * FROM " . BDPX . "_pwdReset WHERE pwdResetSelector=? AND pwdResetExpires >= :currentDate"))
            {
                echo "there was an error!!";
                exit();
            }
            else
            {
                mysqli_stmt_bind_param($stmt, "s", $selector, $currentDate);
                mysqli_stmt_execute($stmt);

                if($stmt && $stmt->num_rows > 0){
                    return $stmt;
                }
                else{
                    return false;
                }
                mysqli_stmt_close($stmt);
                //mysqli_close(SQL::getInstance()->getConnection());
            }
        }
        public function updatePassword($newpwdhash, $tokenEmail)
        {
            $stmt = mysqli_stmt_init(SQL::getInstance()->getConnection());
            if(!mysqli_stmt_prepare($stmt, "UPDATE " . BDPX . "_users SET pwd=? WHERE email=:email"))
            {
                echo "there was an error!!";
                exit();
            }
            else
            {
                mysqli_stmt_bind_param($stmt, "ss", $newpwdhash, $tokenEmail);
                mysqli_stmt_execute($stmt);

                if($stmt && $stmt->num_rows > 0){
                    return $stmt;
                }
                else{
                    return false;
                }
                mysqli_stmt_close($stmt);
                //mysqli_close(SQL::getInstance()->getConnection());
            }            
        }
        
    }
?>
