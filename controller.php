<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'db.php';

$json   = file_get_contents(dirname(__FILE__) . "/config.json");
$config = json_decode($json);

$mysqli = new mysqli(
    $config->db_host,
    $config->db_user,
    $config->db_pass,
    $config->db_name
);

$insert = add_mail_and_wallet($_POST['email'], $_POST['wallet'], $mysqli);
echo json_encode($insert);


function add_mail_and_wallet($email, $wallet, mysqli $mysqli)
{
    if (empty($email) || empty($wallet))               return ["fail" => "email or wallet is empty"];
    if (strlen($email) > 300 || strlen($wallet) > 300) return ["fail" => "email or wallet is wrongly formatted"];

    $email = $mysqli->real_escape_string($email);
    $wallet = $mysqli->real_escape_string($wallet);

    // check if email already registered
    $stmt = $mysqli->prepare("SELECT email FROM emails WHERE email = ?");
    $stmt->bind_param('s', $email);
    $res = $stmt->Execute();

    if ($res) {
        $result = $stmt->get_result();
        $items = $result->fetch_assoc();
        if (!empty($items)) return ["fail" => "email already signed up"];
    }

    // get client ip
    if     (!empty($_SERVER['HTTP_CLIENT_IP']))         $ip = $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else                                                $ip = $_SERVER['REMOTE_ADDR'];

    // insert into database
    $stmt = $mysqli->prepare("INSERT INTO emails (email, wallet, ip) VALUES(?, ?, ?)");
    $stmt->bind_param('ssi', $email, $wallet, $ip);

    if ($stmt->Execute())    return ["success" => ""];
    else                    return ["fail" => "something went wrong"];
}

function send_confirmation_mail()
{
}


// //Load Composer's autoloader
// require 'vendor/autoload.php';

// //Create an instance; passing `true` enables exceptions
// $mail = new PHPMailer(true);

// try {
//     //Server settings
//     $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
//     $mail->isSMTP();                                            //Send using SMTP
//     $mail->Host       = 'smtp.simply.com';                     //Set the SMTP server to send through
//     $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
//     $mail->Username   = 'ekko@ekko-academy.com';                     //SMTP username
//     $mail->Password   = 'gli6ArCH@Qrih&I5ewGvD';                               //SMTP password
//     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
//     $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
//     $mail->Priority = 1;
//     $mail->CharSet = 'UTF-8';
//     $mail->AddCustomHeader("X-MSMail-Priority: High");
//     $mail->WordWrap = 50;

//     //Recipients
//     $mail->addAddress($_POST['email'], 'Joe User');     //Add a recipient

//     //Content
//     $mail->isHTML(true);                                  //Set email format to HTML
//     $mail->setFrom('ekko@ekko-academy.com', 'Ekko');
//     $mail->Subject = 'Hehes';
//     $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
//     $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

//     $mail->send();
//     echo 'Message has been sent';

//     header('Location: ' . $_SERVER['HTTP_REFERER']);
// } catch (Exception $e) {
//     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
// }
