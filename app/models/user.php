<?php
    require_once '../libraries/start.php';
    
    include('../libraries/class_utils.php');

    class User extends sys_utils{

        //Find user by existing email or username
        public function findUsername($array, $modo) {
            $result=[];

            if($modo=="signup"){
                $res = SQL::run("SELECT * FROM " . BDPX . "_users WHERE email  = ".$array[0]." OR username = ".$array[1]);
            }else{//modo signup
                $res = SQL::run("SELECT * FROM " . BDPX . "_users WHERE email  = ".$array[0]." OR username = ".$array[0]);
            }
            
            //var_dump($res);
            if(is_array($res) && count($res) > 0){
                $result= $res;
            }

            return $result;
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
        public function login($name, $password)
        {
            $row = $this->findUsername([$name], "login");
            if(count($row)==0) return false;// error 
            //if found user
            $hashedPassword = $row['pwd']->pwd;
            if(password_verify($password, $hashedPassword)){
                return $row;
            }else{
                return false;
            }
        }

    }
?>
