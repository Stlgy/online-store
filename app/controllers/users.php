<?php
    if ($_SESSION["getData"] == 0) {//user not logged
        require_once '../libraries/start.php';
        require_once '../models/user.php';
        require_once '../helpers/session_helper.php';
    }else{
        require_once 'libraries/start.php';
        require_once 'models/user.php';
        require_once 'helpers/session_helper.php';
    }

//phpinfo();

class Users extends sys_utils {

    private $userModel;
    private $resetModel;

    public function __construct() {
        $this->userModel = new User;
        $this->resetModel = new User;
    }
    ### REGISTER USER
    public function register() {
        #### PROCESS FORM####
        if(isset($_POST['signup-btn'])) {
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
            
            ### VALIDATING INPUTS
            if (empty($data['firstname']) || empty($data['lastname']) || empty($data['username']) ||
                empty($data['email']) || empty($data['pwd']) || empty($data['pwdrepeat'])) {
                flash("register", "Please fill out all fields"); //assign error
                redirect($road2);
            }
            if (!preg_match("/^[a-zA-Z0-9]*$/", $data['firstname'])) {
                flash("register", "Invalid firstname");
                redirect($road2);
            }
            if (!preg_match("/^[a-zA-Z0-9]*$/", $data['lastname'])) {
                flash("register", "Invalid lastname");
                redirect($road2);
            }
            if (!preg_match("/^[a-zA-Z0-9]*$/", $data['username'])) {
                flash("register", "Invalid username");
                redirect($road2);
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                flash("register", "Invalid email");
                redirect($road2);
            }
            if (strlen($data['pwd']) < 6) {
                flash("register", "Invalid password");
                redirect($road2);
            } else if ($data['pwd'] !== $data['pwdrepeat']) {
                flash("register", "No matching passwords");
                redirect($road2);
            }

            ### CHECK IF EMAIL || USERNAME ALREADY EXISTS
            if ($this->userModel->findUsername([$data['email'], $data['username']], "signup")) {
                flash("register", "Username or email already exist");
                redirect('../signup.php');
            } else {
                
                ### ALL VALIDATIONS CHECKED HASH PWD
                $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);

                ### REGISTER
                if ($this->userModel->register($data)) {
                    flash("register", "User registered successfully", 'form-message-green');
                    
                    redirect($road); //send to login pag
                    
                } else { //stop script
                    
                    die("Something went wrong");
                }
            }
        }
    }
    public function login() {
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
        if ($this->userModel->findUsername([$data['username']], "login")) {
            ###IF USER WAS FOUND
            $loggedInUser = $this->userModel->login($data['username'], $data['pwd']);
            
            if ($loggedInUser) {
                ### CREAT SESSION
                $this->createSession($loggedInUser);
            } else {
                flash("login", "Incorrect password");
                
                redirect('../login.php');
            }
        } else {
            redirect($road);
        }
    }
    public function createSession($user) {

        $road = '../index.php';
        $_SESSION['id_u']       = $user["id_u"];
        $_SESSION['username']   = $user["username"];
        $_SESSION['email']      = $user["email"];
        redirect($road);
    }
    public function logout() {
       
        $road = '../index.php';
        unset($_SESSION['id_u']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        unset($_SESSION['getData']);
        session_destroy();
        redirect($road);
    }
    ### SEND EMAIL WITH TOKEN TO RESET PWD
    public function sendEmail() {
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        ### USER CLICKED THE RESET BUTTON
        if (isset($_POST['reset-request-submit'])) {

            $userEmail = trim($_POST["email"]);

            if (empty($userEmail)) {
                flash("reset", "Please insert email");
                redirect("../reset-password.php");
            }

            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                flash("reset", "Invalid email");
                redirect("../reset-password.php");
            }

            ## CREATING 2 TOKENS TO PREVENT TIMING ATTACKS

            //token used to query the user from the DB
            $selector = bin2hex(random_bytes(8));
            //token used for confirmation once the DB entry has been matched
            $token = random_bytes(32);

            $url = "https://www.exportador.ifresh-host.eu/onlinestore/app/create-new-password.php?selector=" . $selector . "&validator=" . bin2hex($token);

            $expires = date("U") + 3600; //expires within an hour

            if (!$this->resetModel->deleteEmail($userEmail)) {
                die("Error!");
            }
            $hashedToken = password_hash($token, PASSWORD_DEFAULT);

            if (!$this->resetModel->insertToken($userEmail, $selector, $hashedToken, $expires)) {
                die("Error");
            }
            ### SENDING EMAIL

            $to = $userEmail;
            $subject  = 'Reset your  account password';
            $message  = '<p>We received a password reset request. Here is the link to reset your password, if you did not made this request, ignore this email</p>';
            $message .= '<p>Password reset link:<br>';
            $message .= '<a href="' . $url . '">' . $url . '</a></p>';

            $headers  = "From: Shop <ines@ideiasfrescas.com>\r\n";
            $headers .= "Reply-to: ines@ideiasfrescas.com\r\n";
            $headers .= "Content-type: text/html\r\n";

            mail($to, $subject, $message, $headers);
            flash("reset", "Check your mail", 'form-message-green');
            redirect('../reset-password.php');
        } else {
            redirect('../index.php');
        }
    }
    ### RESET PWD PROCESS
    public function resetPassword() {
        ### SANITIZE DATA
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        if (isset($_POST["reset-pwd-submit"])) {

            $data = [
                'selector'          => trim($_POST['selector']),
                'validator'         => trim($_POST['validator']),
                'pwd'               => trim($_POST['pwd']),
                'pwdrepeat'         => trim($_POST['pwdrepeat'])
            ];
           
            $url = '../create-new-password.php?selector=' . $data['selector'] . '&validator=' . $data['validator'];

            if (empty($_POST['pwd'] || $_POST['pwdrepeat'])) {
                flash("newReset", "Please fill out all fields");
                redirect($url);
            } else if ($data['pwd'] != $data['pwdrepeat']) {
                flash("newReset", "Passwords do not match!!!!");
                redirect($url);
            } else if ((strlen($data['pwd']) < 6)) {
                flash("newReset", "Invalid password");
                redirect($url);
            }

            $currentDate = date("U"); 

            $row=$this->userModel->resetPassword($data['selector'], $currentDate);
            
             if(!isset($row['pwdResetId'])) { 

                flash("newReset", "Sorry. The link is no longer valid");
                redirect($url);
            
            }
            $tokenb = hex2bin($data['validator']);

            $tokenCheck = password_verify($tokenb, $row['pwdResetToken']);

            if (!$tokenCheck) {
                flash("newReset", "You need to re-Submit your reset request");
                redirect($url);
            }

            $tokenEmail = $row['pwdResetEmail'];
           
            if (!$this->userModel->findEmail($tokenEmail)) {
            
            flash("newReset", "There was an error");
            redirect($url);
            } 

            $newPwdHash = password_hash($data['pwd'], PASSWORD_DEFAULT);

            if (!$this->userModel->updatePassword($newPwdHash, $tokenEmail)) {
                flash("newReset", "There was an error ");
                redirect($url);
            } 
            if (!$this->resetModel->deleteToken($tokenEmail)) {
                flash("newReset", "There was an error");
                redirect($url);
            }
            flash("newReset", "Password Updated", 'form-message form-message-green');
            redirect($url);
        } else {
            redirect('../index.php');
        }
    }
    ### GET PROFILE
    public function getProfile() {

        ### GET DATA FROM PROFILE
        
        $road = '../login.php';

        if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
        flash("editProfile","No permission to access this page");
        redirect($road);
        
        }
        $username = $_SESSION['username'];
           
        if($this->userModel->getProfile($_SESSION['id_u'])){
            return $this->userModel->getProfile($_SESSION['id_u']);
            //die();
        }   

    }
    ### UPDATE PROFILE  
    public function updateProfile() {
       
        ### SANITIZE POST DATA
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        /* echo "<pre>";
        var_dump($_POST);
        echo "</pre>"; */

        if(isset($_POST['update-submit']) && $_POST['update-submit'] != "") {  
            ### INIT DATA
            $data = [
                'firstname' => trim($_POST['firstname']),
                'lastname'  => trim($_POST['lastname']),
                'username'  => trim($_POST['username']),
                'email'     => trim($_POST['email']),
                'pwd'       => trim($_POST['pwd']),
                'pwdrepeat' => trim($_POST['pwdrepeat'])
            ];
        
            $road = '../update-profile.php';
            $road2 = '../login.php';

            ### VALIDATING INPUTS
            if (empty($data['firstname']) || empty($data['lastname']) || empty($data['username']) ||
                empty($data['email']) || empty($data['pwd']) || empty($data['pwdrepeat'])) {
                flash("register", "Please fill out all fields"); //assign error
                redirect($road);
            }
            if (!preg_match("/^[a-zA-Z0-9]*$/", $data['firstname'])) {
                flash("register", "Invalid firstname");
                redirect($road);
            }
            if (!preg_match("/^[a-zA-Z0-9]*$/", $data['lastname'])) {
                flash("register", "Invalid lastname");
                redirect($road);
            }
            if (!preg_match("/^[a-zA-Z0-9]*$/", $data['username'])) {
                flash("register", "Invalid username");
                redirect($road);
            }
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                flash("register", "Invalid email");
                redirect($road);
            }
            if (strlen($data['pwd']) < 6) {
                flash("register", "Invalid password");
                redirect($road);
            } else if ($data['pwd'] !== $data['pwdrepeat']) {
                flash("register", "No matching passwords");
                redirect($road);
            }
            
            ### CHECK IF EMAIL || USERNAME ALREADY EXISTS
            $teste = $this->userModel->findUsername([$data['email'], $data['username']], "signup");
            if ($_SESSION['id_u'] != $teste["id_u"]) {
                
                //redirect($road);
            } else {
                
                ### ALL VALIDATIONS CHECKED HASH PWD
                $data['pwd'] = password_hash($data['pwd'], PASSWORD_DEFAULT);
                
                ### UPDATE USER
                $username = $_SESSION['id_u'];
                if ($this->userModel->updateProfile($data,$_SESSION['id_u'])){
                    
                    flash("register", "Profile Updated", 'form-message-green');
                    redirect('../index.php'); //send to login pag

                } else { //stop script
                    
                    //die("Something went wrong");
                }
            }
        }
    }  
}


$init = new Users;


### ENSURING THE USER IS SENDING A POST REQUEST
if (!in_array(CP, ["update-profile"])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        switch ($_POST['type']) {
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
            case 'update':
                $init->updateProfile();
                break;
            default:
                redirect("../index.php");
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
}
