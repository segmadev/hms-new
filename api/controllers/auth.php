<?php 
require_once ROOT."functions/helper.php";
require_once ROOT."functions/formhandler.php";
require_once ROOT."functions/mailer.php";
class auth extends helper {
    private $formHandler;
    private $user_form;
    private $user;
    private $myMailer;
    function __construct() {
        parent::__construct();
        $this->user_form = [
            "ID"=>["is_required"=>false],
            "first_name"=>[],
            "last_name"=>[],
            "email"=>[],
            "phone_number"=>[],
            "password"=>["is_required"=>true],
            "confirm_password"=>["is_required"=>true]
        ];
        $this->formHandler = new formhandler();
        $this->myMailer = new Mymailer;
    }

    public function signin()
    {
        // if(!$this->verify_captcha()) return ;
        $email = htmlspecialchars($_POST['email']);
        $passwordRaw = htmlspecialchars($_POST['password']);

        if (empty($email) || empty($passwordRaw)) {
            return utilities::apiMessage("Make sure you enter your email and password", 400);
        }

        // Validate base64 password
        if (!base64_decode($passwordRaw, true)) {
            return utilities::apiMessage("Invalid password format", 400);
        }

        $password = base64_decode($passwordRaw);

        $this->user = $this->getall("users", "email = ?", [$email]);
        if (!is_array($this->user)) {
            return utilities::apiMessage("User not found or password incorrect.", 404);
        }
        if (!password_verify($password, $this->user['password'])) {
            return utilities::apiMessage("User not found or password incorrect.", 404);
        }
        if (htmlspecialchars($this->user['status']) == 0) {
            $reason = "";
            if ($this->user['reason'] != "") {
                $reason = htmlspecialchars($this->user['reason']);
            }
            return utilities::apiMessage("We're sorry, your account has been blocked. <br> <b>Reason: </b> " . $reason, 403);
        }
        // reson here
        $this->user['token'] = $this->set_token($this->user);
        if (!is_array($this->user['token'])) {
            return  utilities::apiMessage("Unable to set token", 500);
        }
        unset($this->user['password'], $this->user['status'], $this->user['reason'], $this->user['ip_address']);
        // $actInfo = ["userID" => $value['ID'], "date_time" => date("Y-m-d H:i:s"), "action_name" => "Login", "description" => "Admin Login"];
        // $this->new_activity($actInfo);
        // $_SESSION['adminSession'] = ;
        // $d->message("Account logged in Sucessfully <a href='index.php'>Click here to proceed.</a>", "error");
        unset($this->user['password']);
        return utilities::apiMessage("Account logged in successfully", 200, $this->user);
    }

    function register() {
        if(!is_array($this->user)) {
            $_POST['ID'] = utilities::genID(6);
            $this->user = $this->formHandler->validateForm($this->user_form, "users");
            if(!is_array($this->user)) { 
                return utilities::apiMessage("Fill all required fileds", 400, errors: $this->formHandler->errors);
            }
        }

        // Validate base64 passwords
        if (isset($_POST['password']) && !empty(htmlspecialchars($_POST['password']))) {
            $passwordRaw = trim(htmlspecialchars($_POST['password']));
            if (!base64_decode($passwordRaw, true)) {
                return utilities::apiMessage("Invalid password format", 400);
            }

            $password = base64_decode($passwordRaw);
            // Validate password length
            if (strlen($password) < 8) {
                return utilities::apiMessage("Password must be at least 8 characters long", 400);
            }

            

            // Hash the password
            $this->user['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if($this->getall("users", "phone_number = ?", [$this->user['phone_number']], fetch: "") > 0) {
            return utilities::apiMessage("Phone number already exists", 400); 
        }

        $check = $this->getall("users", "email = ?", [$this->user['email']], fetch: "");
        if($check > 0) {
            return utilities::apiMessage("Email already exists", 400); 
        }
        
        $this->user['ID'] = $this->validID($this->user['ID']);
        if(!$this->quick_insert("users", $this->user)) return utilities::apiMessage("Unable to create account for your", 500);
        if(isset($this->user['password']) && $this->user['password'] != "") {
          $this->user['token'] =  $this->set_token($this->user['ID']);
        }
        unset($this->user['password']);
        return utilities::apiMessage("Account created successfully", 200, $this->user);
    }

    private function validID($ID = null) {
        $ID = $this->user['ID'] ?? $ID ??  utilities::genID(6);
        $user = $this->getall("users", "ID = ?", [$ID], fetch: "");
        if($user > 0) {
            $this->validID();
        }
        return $ID;
    }
  

    private function set_token()
    {
        $user = $this->user;
        $userID = $user['ID'];
        $token =  utilities::randcar(rand(20, 40));
        if (!$this->create_table("users", ["token" => []], isCreate: false))
            return false;
        // $otpCode = ($user['is_2fa'] == 1) ? rand(100000, 999999) : "";
        $userDetails = utilities::get_visitor_details();
        // $hashPass = ($otpCode != "") ? password_hash($otpCode, PASSWORD_DEFAULT) : $otpCode;
        $log = [
            "userID"=>$userID,
            "token"=>$token,
               "ip"=>$userDetails['ip_address'],
            "device"=>$userDetails['device'],
            "date_time"=>time(),
            "expiry_date"=>time() + (60 * 60 * 24),
            "otp"=>"",
        ];
        $this->delete("user_logs", "userID = ? and ip = ?", [$log['userID'], $log['ip']]);
        $this->quick_insert("user_logs", $log);
        // if($otpCode != "") {
        //     $smessage = $this->get_email_template("otp")['template'];
        //     $smessage = $this->replace_word(['${first_name}' => $user['first_name'], 
        //     '${otp}' => $otpCode,
        //     '${ip_address}' => $log['ip'],
        //     '${device}' => $log['device'],
        //     '${location}' => $userDetails['state']." ".$userDetails['country'],
        // ], $smessage);
        //     $this->smtpmailer($user['email'], "OTP Verification for Login Attempt on Your Cruise tech log Account", $smessage);
            
        // }
        unset($log['userID'], $log['ip'], $log['device'], $log['otp']);
        return $log;
    }

    public function forget_password()
    {
        $d = new database;
        if (!isset($_POST['email']) || $_POST['email'] == '') {
            return utilities::apiMessage("Please enter email address", 400);
        }
        $email = htmlspecialchars($_POST['email']);
        $data = $d->getall("users", "email = ?", [$email]);
        if (!is_array($data)) {
            return utilities::apiMessage("Email not found check and try again", 404);
        }
        $id = $data['ID'];
        $reset = mt_rand(000000, 99999);
        $hashreset = password_hash($reset, PASSWORD_DEFAULT);
        $where = "ID ='$id'";
        $update = $d->update("users", ["reset" => $hashreset], $where);
        if ($update) {
            $smessage = $this->get_email_template("forget_password")['template'];
            $smessage = $this->replace_word(['${first_name}' => $data['first_name'], '${last_name}' => $data['last_name'], '${message_here}' => $reset, '${website_url}' => $this->get_settings("website_url")], $smessage);
            $sendmail = $this->myMailer->smtpmailer($data['email'], "Password Reset", $smessage);
            if ($sendmail) {
                return utilities::apiMessage("Password reset code sent", 200);
            } else {
                return utilities::apiMessage("Failed to send email. Please try again later.", 500);
            }
        }
        return utilities::apiMessage("Error", 500);
    }

    public function resetpassword()
    {
        $d = new database;
        $from_data = ["code"=>[], "email"=>[], "password"=>[], "confirm_password"=>[]];
        $value = $this->formHandler->validateForm($from_data);
        if (is_array($value)) {
            // if($value['password'] != $value['confirm_password']) {
            //     return utilities::apiMessage("Password and confirm password do not match", 400);
            // }
            $email = $value['email'];
            $data = $d->getall("users", "email = ?", [$email]);
            if (password_verify($value['code'], $data['reset'])) {
                $where = "email ='$email'";
                $update = $d->update("users", ["password" => $value['password'], "reset" => ""], $where);
                if ($update) {
                    return utilities::apiMessage("Password Reset successfully.", 200);
                } else {
                    return utilities::apiMessage("Failed to reset password.", 500);
                }
            } else {
                return utilities::apiMessage("Incorrect Code", 400);
            }
        } else {
            return utilities::apiMessage("Invalid input data", 400);
        }
    }
}