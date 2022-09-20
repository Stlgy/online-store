<?php

require_once '../models/user.php';
require_once '../helpers/session_helper.php';
require '../libraries/start.php';

//phpinfo();

class Users extends sys_utils
{

    private $userModel;

    public function __construct()
    {
        $this->userModel = new User;
    } 

    public function register()
    {
        ####PROCESS FORM####

        ### SANITIZE POST DATA
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        ### INIT DATA
        $data = [
            'firstname' => trim($_POST['firstname']),
            'lastname'  => trim($_POST['lastname']),
            'username'  => trim($_POST['username']),
            'email'     => trim($_POST['email']),
            'pwd'       => trim($_POST['pwd']),
            'pwdrepeat' => trim($_POST['pwdrepeat'])
        ];

        $road = '../login.php';
        $road2 = '../signup.php';
        ### VALIDATE INPUTS
        if (empty($data['firstname']) || empty($data['lastname']) || empty($data['username']) ||
            empty($data['email']) || empty($data['pwd']) || empty($data['pwdrepeat'])) {
            flash("register", "Please fill out all fields"); //assign error
            redirect ($road2);
        }
        if (!preg_match("/^[a-zA-Z0-9]*$/", $data['firstname'])) {
            flash("register", "Invalid firstname");
            redirect ($road2);
        }
        if (!preg_match("/^[a-zA-Z0-9]*$/", $data['lastname'])) {
            flash("register", "Invalid lastname");
            redirect ($road2);
        }
        if (!preg_match("/^[a-zA-Z0-9]*$/", $data['username'])) {
            flash("register", "Invalid username");
            redirect ($road2);
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash("register", "Invalid email");
            redirect ($road2);
        }
        if (strlen($data['pwd']) < 6) {
            flash("register", "Invalid password");
            redirect ($road2);
        } else if ($data['pwd'] !== $data['pwdrepeat']) {
            flash("register", "No matching passwords");
            redirect ($road2);
        }
        ### CHECK IF EMAIL || USERNAME ALREADY EXIST
        if ($this->userModel->findUsername([$data['email'], $data['username']], "signup")) {
            flash("register", "Username or email already exist");
            redirect('../signup.php');
        }else{
            ### ALL VALIDATIONS CHECKED
            ### HASH PWD
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);   
            ### REGISTER
            if ($this->userModel->register($data)) {
                redirect ($road); //send to login pag
            } else { //stop script
                 die("Something went wrong");
            }
        }
    }
    public function login()
    {
        //echo 1;
        $road = '../signup.php';
        
        ### SANITIZE POST DATA
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        ### INIT DATA
        $data = [
            'username' => trim($_POST['username']),
            'pwd'      => trim($_POST['pwd'])
        ];
       
        var_dump($data);
        if (empty($data['username']) || empty($data['pwd'])) {
            flash("login", "Please fill out all fields"); //assign error
            redirect("../login.php");
            //exit();
        }
        
        ### CHECK FOR EMAIL || USERNAME
        if ($this->userModel->findUsername([$data['username']],"login")) 
        {
            ## IF FOUND USER
            $loggedInUser = $this->userModel->login($data['username'], $data['pwd']);
            //var_dump($loggedInUser);
            if ($loggedInUser) {
                ### CREAT SESSION
                $this->createSession($loggedInUser);              
            } else {              
                flash("login", "Incorrect password");
                redirect ('../login.php');
            }
        } else {
            redirect ($road);
        }
    }
    public function reset()
    {   ##User clicked the rest button
        if(isset($_POST['forgot-pwd'])){
            $selector = bin2hex(random_bytes(8));
            $token = random_bytes(32);

            $url = "www.exportador.ifresh-host.eu/create-new-password.php?selector =" . $selector . "&validator=" . bin2hex($token);
            $expires = date("U") + 1800;//1 hour from now

            $userEmail = $_POST["email"];

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

            $headers  = "From: Shop <ines@ideiasfrescas.com>\r\n";
            $headers .="Reply-to: ines@ideiasfrescas.com\r\n";
            $headers .="Content-type: text/html\r\n";

            mail($to, $subject, $message, $headers);
            flash("reset", "Reset Successful");
            redirect('../reset-password.php');
        }else{
            redirect('../index.php');
        }
    }
    public function createSession($user)
    {
        $road = '../index.php';
        $_SESSION['id_u']       = $user["id_u"];
        $_SESSION['username']   = $user["username"];
        $_SESSION['email']      = $user["email"];
        redirect ($road);
    }
    public function logout()
    {
        //echo "bananas";
        $road = '../index.php';
        unset($_SESSION['id_u']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        session_destroy();
        redirect($road); 
       
    }
}

$init = new Users;

###ENSURING THE USER IS SENDING A POST REQUEST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    switch ($_POST['type']) {
        case 'register':
            $init->register();
            break;
        case 'login':
            $init->login();
            break;
    }
} else {
    switch ($_GET['q']) {
        case 'logout':
            $init->logout();
            break;
        default:
            redirect("../index.php");
    }
}
