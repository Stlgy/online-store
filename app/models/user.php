<?php
    require_once '../libraries/start.php';
    
    include('../libraries/class_utils.php');

    class User extends sys_utils{

        //Find user by existing email or username
        public function findUsername($email, $username) :mixed
        {
            $res = SQL::getArray(SQL::run("SELECT * FROM " . BDPX . "_users WHERE username = ".$username." OR email = ".$email));
            if(count($res) > 0){
                return $res;
            }else{
                return false;
            }     
        }
         //Register User
        public function register($data) :bool
        {//only 1 arg all data is stored in array
            $res = SQL::run("INSERT INTO " . BDPX . "_users(firstname, lastname, username, email, pwd)
            VALUES (".$data['firstname'].", ".$data['lastname'].", ".$data['username'].", ".$data['email'].", ".$data['pwd'].")");
            if ($res) {
                return true;
            }
            else {
                return false;
            }
        }
        //Login User
        public function login($nameEmail, $password)
        {
            $row = $this->findUsername($nameEmail, $nameEmail);
            if($row == false) return false;// error 
            //if found user
            $hashedPassword = $row->password;
            if(password_verify($password, $hashedPassword)){
                return $row;
            }else{
                return false;
            }
        }

    }
?>