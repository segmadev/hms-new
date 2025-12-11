<?php
require_once ROOT."functions/helper.php";
require_once ROOT."functions/formhandler.php";
class Mymailer extends database
{
    private $formHander;   
   function  __construct() {
        parent::__construct();
        $this->formHander = new formHandler;
    }
    function send_email($userID, $subject, $message, $notify = false)
    {
        if (is_array($notify)) {
            $data = [];
            $data['userID'] = $userID;
            $data['n_for'] = "users";
            $data['title'] = $subject;
            $data['description']  = $message;
            $data['time_set'] = time();
            $data['date_set'] = date("Y-m-d");
            $data['url'] = $notify['url'];
            $this->new_notification($data);
        }
        $smessage = $this->get_email_template("default")['template'];
        $user =  $this->getall("users", "ID = ?", [$userID], "first_name, last_name, email");
        if (!is_array($user)) {
            return false;
        }
        // ${amount} ${reason} ${website_url} 
        $smessage = $this->replace_word(['${first_name}' => $user['first_name'], '${last_name}' => $user['last_name'], '${message_here}' => $message, '${website_url}' => $this->get_settings("website_url")], $smessage);
        $sendmessage = $this->smtpmailer($user['email'], $subject, $smessage);
        if ($sendmessage) {
            return true;
        } else {
            return false;
        }
    }
    function smtpmailer($to, $subject, $body, $name = "", $message = '', $smtpid = 1)
    {
        $body = htmlspecialchars_decode($body);
        if (isset($_ENV['mail_type']) && $_ENV['mail_type'] == "log") {
            $maillog = fopen("maillog.log", "w");
            $header = "<p>To: $to </p>";
            $header .= "<h3>Subject: $subject </h3>";
            $header .= "<p>name: $name </p>";
            $body = $header . $body . "<hr>";
            fwrite($maillog, $body);
            return true;
        }
        // return $to;
        require_once ROOT."admin/include/phpmailer/PHPMailerAutoload.php";
        // require_once "";
        $d = new database;
        $smtp = $d->getall("smtp_config", "ID = ?", ["$smtpid"]);
        if (!is_array($smtp)) {
            // $d->message("SMTP selected not found please choose another one or refresh page and try again", "error");
            return false;
        }
        $server = $smtp['server'];
        $username = $smtp['username'];
        $password = $smtp['password'];
        $port = $smtp['port'];
        $smtp_from_email = $smtp['from_email'];
        // echo $body;
        try {
            $from = $username;
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = "$server";
            $mail->Port = "$port";
            $mail->Username = "$username";
            $mail->Password = "$password";

            //   $path = 'reseller.pdf';
            //   $mail->AddAttachment($path);

            $mail->IsHTML(true);
            $mail->From = "$username";
            $mail->FromName = $username;
            $mail->Sender = "$smtp_from_email";
            $mail->AddReplyTo("$username", $username);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AddAddress($to);
            $send = $mail->Send();
            if ($send) {
                return true;
            }
        } catch (phpmailerException $e) {

            echo $e; //Pretty error messages from PHPMailer
            // $d->message("Error Sending message. You can try new SMTP", "error");
            return false;
        } catch (Exception $e) {
            echo $e->getMessage(); //Boring error messages from anything else!
            // $d->message("Error Sending message. You can try new SMTP", "error");
            return false;
        }
    }

    function new_notification(array $data, $what = "quick")
    {
        if (is_array($what != "quick")) {
            if (!isset($_POST['time_set'])) {
                $_POST['time_set'] = time();
            }
            $info = $this->formHander->validate_form($data, 'notifications', "insert");
            if ($info) {
                return true;
            }
        } else {
            if ($this->quick_insert("notifications",  $data)) {
                return true;
            }
        }
        return false;
    }
}