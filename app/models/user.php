<?php
    require_once '../view/includes/start.php';
    //require_once '../libraries/database.php';
    
    include('../libraries/class_utils.php');

    class User extends sys_utils{

        //Find user by existing email or username
        public function findUsername($email, $username){
            $res = $this->SQL::run("SELECT * FROM ".BDPX."_users WHERE username = :username OR email = :email");
                $this->res->bind(':username', $username);
                $this->res->bind(':email', $email);
                if($res && $res->num_rows > 0){
                    $row = $this->res->single();
                    return $row;
                }else{
                    return false;
                }     
    }
         //Register User
        public function register($data){//only 1 arg all data is stored in array
            $res = $this->SQL::run("INSERT INTO ".BDPX."_users(firstname, lastname, username, email, pwd)
            VALUES (:firstname, :lastname, :username, :email, :password)");
            $this->res->bind(':firstname', $data['firstname']);
            $this->res->bind(':lastname',  $data['lastname']);
            $this->res->bind(':username', $data['username']);
            $this->res->bind(':email', $data['email']);
            $this->res->bind(':pwd', $data['pwd']);

            

        }
        //Login User
        public function login($nameEmail, $password){
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

    /*class User{
        private $db;

        public function __construct()
        {
            $this->db = new Database;
        }
    
    //Find user by existing email or username
    public function findUsername($email, $username){
        $this->db->query('SELECT *FROM users WHERE username = :username OR email = :email');
        $this->db->bind(':username', $username);
        $this->db->bind(':email', $email);
        

        $row = $this->db->single();//only 1 user match

        //Checking row
        if($this->db->rowCount() > 0){
            return $row;
        }else{
            return false;
        }
    }
    //Register User
    public function register($data){//only 1 arg all data is stored in array
        $this->db->query('INSERT INTO users(firstname, lastname, username, email, pwd)
        VALUES (:firstname, :lastname, :username, :email, :password)');
        //Binding values
        $this->db->bind(':firstname', $data['firstname']);
        $this->db->bind(':lastname', $data['firstname']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $data['pwd']);

        //Executing
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }
    //Login User
    public function login($nameEmail, $password){
        $row = $this->findUserEmailUsername($nameEmail, $nameEmail);

    if($row == false) return false;// error 
    //if found user
    $hashedPassword = $row->password;
    if(password_verify($password, $hashedPassword)){
        return $row;
    }else{
        return false;
    }
}
//Reset Password
    public function resetPassword($newPwdHash, $tokenEmail){
        $this->db->query('UPDATE users SET pwd=:pwd WHERE email=:email');
        $this->db->bind(':pwd', $newPwdHash);
        $this->db->bind(':email', $tokenEmail);

        //Execute
        if($this->db->execute()){
            return true;
        }else{
            return false;
        }
    }
}*/

    

