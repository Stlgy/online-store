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
            /*echo "<pre>";
            print_r($payload); 
            echo "</pre>";*/
            return $payload;
        }
         public function findEmail($email)
        {
            $data = [];
            $res = SQL::run("SELECT *FROM " . BDPX . "_users WHERE email=?");
            $connection = $this->_connection;
            $result = $connection->prepare($res);
            $result->execute([$email]);
            $data = $result->fetchALL();
            if (!empty($data)) {
                return FALSE;
            } else {
                return $data;
            }
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
        ### RESET PWD
        public function resetPwd(){

        
        }
    }
?>
