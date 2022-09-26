<?php
    require_once '../libraries/start.php';
    
    include('../libraries/class_utils.php');

    class User extends sys_utils
    {
 
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
                return true;
            }
            else 
            {
                return false;
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
         public function deleteEmail(){
        
            $userEmail = $_POST["email"];
            $res = SQL::runPrepareStmt("DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail=?","s",[$userEmail]);
            //echo SQL::$error;
            //var_dump($res);
            return $res;
        } 
       public function insertToken($userEmail, $selector, $hashedToken, $expires)
       {
            $res = SQL::runPrepareStmt("INSERT INTO " . BDPX . "_pwdReset (pwdResetId,pwdResetEmail,pwdResetSelector,pwdResetToken,pwdResetExpires) VALUES 
            (NULL,?,?,?,?)","ssss",[$userEmail, $selector, $hashedToken, $expires]);
            var_dump($res);
             return $res;           
        }
        ## RESET PWD - GET TIME
        public function resetPassword($selector,$currentDate) 
        {
           // $currentDate =date("U");
            $res = SQL::runPrepareStmt("SELECT * FROM ". BDPX . "_pwdReset WHERE pwdResetSelector=? AND pwdResetExpires=?","ss",[$currentDate]);
            return $res;
        }
        public function updatePassword($newpwdhash, $tokenEmail)
        {
            $res = SQL::runPrepareStmt("UPDATE " .BDPX . "_users SET pwd?","ss",[$newpwdhash, $tokenEmail]);
            return $res;
        }             
    }
         
    
?>
