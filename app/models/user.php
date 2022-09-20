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
            }else
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
            if ($res) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }
        ### LOGIN USER   
        public function login($name, $password)
        {
            $resultado = false;

            if(!empty($row = $this->findUsername([$name], "login"))){
                $hashedPassword = $row['pwd'];
                if(password_verify($password, $hashedPassword)) $resultado = $row;
            }
            return $resultado;
        }
        public function deleteToken(){
            $userEmail = $_POST["email"];
            $res = SQL::run("DELETE FROM " . BDPX . "_pwdReset WHERE pwdResetEmail=?");
            $stmt = mysqli_stmt_init($this->_connection);
            if(!mysqli_stmt_prepare($stmt, $res)){
                echo "there was an error!!";
                exit();
            }else{
                mysqli_stmt_bind_param($stmt, "s",$userEmail);
                mysqli_stmt_execute($stmt);
            }
        }
        public function insertData(){
            $res = SQL::run("INSERT INTO pwdReset(pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) 
            VALUES(?, ?, ?, ?);");
            $stmt = mysqli_stmt_init($this->_connection);
            if(!mysqli_stmt_prepare($stmt, $res)){
                echo "there was an error!!";
                exit();
            }else{
                $expires = date("U") + 1800;//1 hour from now
                $token = random_bytes(32);
                $hashedToken = password_hash($token, PASSWORD_DEFAULT);
                mysqli_stmt_bind_param($stmt, "ssss",$userEmail, $selector, $hashedToken, $expires);
                mysqli_stmt_execute($stmt);
            }
            mysqli_stmt_close($stmt);
            mysqli_close($this->_connection);

            $url = "www.exportador.ifresh-host.eu/create-new-password.php?selector =" . $selector . "&validator=" . bin2hex($token);
            $userEmail = $_POST["email"];
            
            $to = $userEmail;
            $subject  = 'Reset your password for shop';
            $message  = '<p>We received a password reset request. Here is the link to reset your password, if you did not made this request, igore this email</p>';
            $message .= '<p>Password reset link:<br>';
            $message .= '<a href="' . $url . '">' .$url .'</<></p>';

            $headers = "From: Shop <ines@ideiasfrescas.com>\r\n";
            $headers .="Reply-to: ines@ideiasfrescas.com\r\n";
            $headers .="Content-type: text/html\r\n";

            mail($to, $subject, $message, $headers);
            flash("resetPwd","Reset successful");
            redirect('../reset-password.php');
        }
        
    }
?>
