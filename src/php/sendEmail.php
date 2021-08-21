<?php

use PHPMailer\PHPMailer\PHPMailer;

if (isset($_REQUEST['name']) && isset($_REQUEST['email'])) {
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $subject = $_REQUEST['subject'];
    $message = $_REQUEST['message'];

    require_once 'PHPMailer/PHPMailer.php';
    require_once 'PHPMailer/SMTP.php';
    require_once 'PHPMailer/Exception.php';
    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'waveformhub@gmail.com'; // Gmail address which you want to use as SMTP server
    $mail->Password = 'waveformhubwaveformhubwaveformhub'; // Gmail address Password
    $mail->SMTPSecure = 'ssl';
    $mail->Port = '465';


    // Email Settings

    $mail->isHTML(true);
    $mail->setFrom($email, $name); // Gmail address which you used as SMTP server
    $mail->addAddress('waveformhub@gmail.com'); // Email address where you want to receive emails (you can use any of your gmail address including the gmail address which you used as SMTP server)
    $mail->Subject = ("$email ($subject)");
    $mail->Body = $message;

    if ($mail->send()) {
        $status = "success";
        $response = "Email Is Sent!";
    } else {
        $status = "failed";
        $response = "Something IS Wrong:<br>" . $mail->ErrorInfo;
    }
    exit(json_encode(array("status" => $status, "response" => $response)));
}