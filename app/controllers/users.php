<?php

require_once '../models/user.php';
require_once '../helpers/session_helper.php';
require '../libraries/start.php';

//phpinfo();

class Users extends sys_utils
{

    private $userModel;
    private $resetModel;

    public function __construct()
    {
        $this->userModel = new User;
        $this->resetModel = new User;
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
        } 
        else if ($data['pwd'] !== $data['pwdrepeat']) {
            flash("register", "No matching passwords");
            redirect ($road2);
        }
        ### CHECK IF EMAIL || USERNAME ALREADY EXIST
        if ($this->userModel->findUsername([$data['email'], $data['username']], "signup")) {
            flash("register", "Username or email already exist");
            redirect('../signup.php');
        }
        else{
            ### ALL VALIDATIONS CHECKED
            ### HASH PWD
            $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);   
            ### REGISTER
            if ($this->userModel->register($data)) {
                redirect ($road); //send to login pag
            } 
            else { //stop script
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
       
        //var_dump($data);
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
            } 
            else {              
                flash("login", "Incorrect password");
                redirect ('../login.php');
            }
        } 
        else {
            redirect ($road);
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
    public function sendEmail()
    {  
        ## User clicked the reset button
        if (isset($_POST['reset-request-submit'])) {

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            $userEmail = trim($_POST["email"]);

            if(empty($userEmail))
            {
            flash("reset", "Please insert email");
            redirect("../reset-password.php");
            }

            if(!filter_var($userEmail, FILTER_VALIDATE_EMAIL))
            {
            flash("reset", "Invalid email");
            redirect("../reset-password.php");
            }

            ## CREATING 2 TOKENS TO PREVENT TIMING ATTACKS

            //token for checking DB to pinpoint the token needed to check the user with, when user get's back to website
            $selector = bin2hex(random_bytes(8));
            //token for authenticate that it's the correct user
            $token = random_bytes(32); 

            $url = "www.exportador.ifresh-host.eu/create-new-password.php?selector =" . $selector . "&validator=" . bin2hex($token);
            $expires = date("U") + 1800; //expiration half an hour?

            if(!$this->resetModel->deleteToken($userEmail))
            {
                die("Error");
            }
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);

            if(!$this->resetModel->insertToken($userEmail, $selector, $hashedToken, $expires))
            {
                die("Error");
            }
            ## SENDING EMAIL

            $to = $userEmail;
            $subject  = 'Reset your  account password';
            $message  = '<p>We received a password reset request. Here is the link to reset your password, if you did not made this request, ignore this email</p>';
            $message .= '<p>Password reset link:<br>';
            $message .= '<a href="' . $url . '">' . $url . '</<></p>';

            $headers  = "From: Shop <ines@ideiasfrescas.com>\r\n";
            $headers .= "Reply-to: ines@ideiasfrescas.com\r\n";
            $headers .= "Content-type: text/html\r\n";

            mail($to, $subject, $message, $headers);
            flash("reset", "Check your mail, 'form-message-green");
            redirect('../reset-password.php');
        }
    }
    public function resetPassword()
    {
        ## SANITIZE DATA
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if(isset($_POST["reset-pwd-submit"]))
        {
            $data = [
                'selector'          => trim($_POST['selector']),
                'validator'         => trim($_POST['validator']),
                'password'          => trim($_POST['pwd']),
                'passwordRepeat'    => trim($_POST['pwdrepeat'])
            ];
            $url = '../create-new-password.php?selector='.$data['selector'].'&validator='.$data['validator'];

            if(empty($_POST['pwd'] || $_POST['pwdrepeat']))
            {
            flash("newReset", "Please fill out all fields");
            redirect($url);
            }
            else if($data['pwd'] != $data['pwdrepeat'])
            {
            flash("newReset", "Passwords do not match");
            redirect($url);
            }
            else if((strlen($data['pwd']) < 6))
            {
                flash("newReset", "Invalid password");
                redirect($url);
            }
            $currentDate =date("U");

            if(!$row = $this->resetModel->resetPassword($data['selector'],$currentDate))
            {
                flash("newReset", "Sorry. The link is no longer valid");
                redirect($url);
            }
            $tokenb     = hex2bin($data['validator']);
            $tokencheck = password_verify($tokenb, $row["pwdResetToken"]);

            if(!$tokencheck)
            {
                flash("newReset", "You need to re-Submit your reset request");
                redirect($url);
            }
            $tokenEmail =$row->pwdResetEmail;

            if(!$this->userModel->findUsername($tokenEmail, $tokenEmail))
            {
                flash("newReset", "There was an error");
                redirect($url);
            }
            $newPwdhash = password_hash($data['pwd'], PASSWORD_DEFAULT);

            if(!$this->userModel->updatePassword($newPwdhash, $tokenEmail))
            {
                flash("newReset", "There was an error");
                redirect($url);
            }
            if(!$this->resetModel->deleteToken($tokenEmail))
            {
                flash("newReset", "There was an error");
                redirect($url);
            }
            flash("newReset", "Password Updated", 'form-message form-message-green');
            redirect($url);
        }
    }
}



$init = new Users;

### ENSURING THE USER IS SENDING A POST REQUEST
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    switch ($_POST['type']) 
    {
        case 'register':
            $init->register();
            break;
        case 'login':
            $init->login();
            break;
        case 'send':
            $init->sendEmail();
            break;
        case 'reset':
            $init->resetPassword();
            break;
        default:
            redirect("../index.php");
    }
}
else {
    switch ($_GET['q']) 
    {
        case 'logout':
            $init->logout();
            break;
        default:
            redirect("../index.php");
    }
}
