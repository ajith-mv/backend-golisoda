<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'phpmailer/PHPMailerAutoload.php';

function send_mail($to, $subject, $bdymsg, $for, $storename)
{


    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->isHTML(true);
    $mail->Host = 'smtp.gmail.com';
    $mail->Port       = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth   = true;
    $mail->Username = 'info@golisodastore.com';
    $mail->Password = 'gss@2020';
    $mail->From = 'info@golisodastore.com';
    $mail->FromName = 'Goli Soda';
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->msgHTML($bdymsg);



    $mail->SMTPDebug = 3;
    $mail->Debugoutput = 'html';



    if (!$mail->send()) {
        return [
            "status" => false,
            "message" => "Mailer Error: " . $mail->ErrorInfo
        ];
    }    
    
    return [
        "status" => true,
        "message" => "Mail send Success!"
    ];

}

echo send_mail('durairaj.pixel@gmail.com', 'SMTP Mail function', 'Test mail content', '1', 'Goli Soda');
