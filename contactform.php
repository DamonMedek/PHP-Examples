<?php
use PHPMailer\PHPMailer\PHPMailer;

if (isset($_POST['reg_user']))
{

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $body = $_POST['message'];

    if (empty($name))
    {
        echo "name-required";
        exit();
    }
    if (empty($email))
    {
        echo "email-required";
        exit();
    }
    if (empty($subject))
    {
        echo "subject-required";
        exit();
    }
    if (empty($body))
    {
        echo "message-required";
        exit();
    }

    require_once "PHPMailer/PHPMailer.php";
    require_once "PHPMailer/SMTP.php";
    require_once "PHPMailer/Exception.php";

    //smtp settings
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "tls";
    $mail->Port = 587;
    $mail->Host = "smtp.gmail.com";
    $mail->Username = "damon@gmail.com";
    $mail->Password = "";

    //email settings
    $mail->isHTML(true);
    $mail->setFrom($email, $name);
    $mail->addAddress("damon@gmail.com");
    $mail->Subject = ("$email ($subject)");
    $mail->Body = $body;

    if ($mail->send())
    {
        echo "success";
        exit();
        
        
    }
    else
    {
        echo "unsuccessful";
        exit();
        
        
    }

    
}

?>
