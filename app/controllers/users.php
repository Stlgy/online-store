<?php

require_once '../models/user.php';
require_once '../helpers/session_helper.php';

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
        //Process form

        //Sanitize POST data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //Init data
        $data = [
            'firstname' => trim($_POST['firstname']),
            'lastname'  => trim($_POST['lastname']),
            'username'  => trim($_POST['username']),
            'email'     => trim($_POST['email']),
            'pwd'       => trim($_POST['pwd']),
            'pwdrepeat' => trim($_POST['pwdrepeat'])
        ];

        $caminho = '../login.php';
        //Validate inputs see if empty
        if (
            empty($data['firstname']) || empty($data['lastname']) || empty($data['username']) ||
            empty($data['email']) || empty($data['pwd']) || empty($data['pwdrepeat'])) {
            flash("register", "Please fill out all fields"); //assign error
            redirect($caminho);
        }
        if (!preg_match("/^[a-zA-Z0-9]*$/", $data['firstname'])) {
            flash("register", "Invalid firstname");
            redirect($caminho);
        }
        if (!preg_match("/^[a-zA-Z0-9]*$/", $data['lastname'])) {
            flash("register", "Invalid lastname");
            redirect($caminho);
        }
        if (!preg_match("/^[a-zA-Z0-9]*$/", $data['username'])) {
            flash("register", "Invalid username");
            redirect($caminho);
        }
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            flash("register", "Invalid email");
            redirect($caminho);
        }
        if (strlen($data['pwd']) < 6) {
            flash("register", "Invalid password");
            redirect($caminho);
        } else if ($data['pwd'] !== $data['pwdrepeat']) {
            flash("register", "No matching passwords");
            redirect($caminho);
        }
        //email or username already exists
        if ($this->userModel->findUsername([$data['email'], $data['username']], "signup")) {
            flash("register", "Username or email already exist");
            redirect('../signup.php');
        }else{
            //All validation checked
            //pwd hash 
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);   
            //Register user
            if ($this->userModel->register($data)) {
                redirect($caminho); //send to login pag
            } else { //stop script
                 die("Something went wrong");
            }
        }
    }
    public function login()
    {
        
        $caminho = '../signup.php';
        
        //sanatizing Post data
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        //Init data
        $data = [
            'username' => trim($_POST['username']),
            'pwd'      => trim($_POST['pwd'])
        ];

        if (empty($data['username']) || empty($data['pwd'])) {
            flash("login", "Please fill out all fields"); //assign error
            redirect("../login.php");
            exit();
        }
        
        //Check for user  or email
        if ($this->userModel->findUsername([$data['username']],"login")) 
        {
            //if found user
            $loggedInUser = $this->userModel->login($data['username'], $data['pwd']);
            
            //var_dump($data['username'], $data['pwd']);
            //echo '<br>';
            //print_r($loggedInUser);
            //die("morde a foca");
            if ($loggedInUser) {
                //Create Session
                $this->createSession($loggedInUser);              
            } else {              
                flash("login", "Incorrect password");
                //redirect($caminho);
            }
        } else {
            //redirect($caminho);
        }
    }
    public function resetPwd()
    {
        $user = new User();
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
            'user' =>trim($_POST['username'])
        ];
        //var_dump($data);
        if(empty($data['user'])){
            flash("reset", "Username or Email is required");
            redirect('../login.php');
            exit();
        }
        //check for user
         if ($user->findUsername([$data['username']], "login")) {

        }
        /* */
    }
    public function createSession($user)
    {
        $caminho = '../index.php';
        $_SESSION['id_u']       = $user["id_u"];
        $_SESSION['username']   = $user["username"];
        $_SESSION['email']      = $user["email"];
        redirect($caminho);
    }
    public function logout()
    {
        unset($_SESSION['id_u']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        session_destroy();
        redirect('../index.php');
    }
}

$init = new Users;

//Ensure that user is sending a POST request
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
            redirect("index.php");
    }
}
