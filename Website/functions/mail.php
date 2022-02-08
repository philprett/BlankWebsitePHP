<?php

require $_SERVER["DOCUMENT_ROOT"]."/assets/PHPMailer_sdfkjhweouihfrncvodsihfo/Exception.php";
require $_SERVER["DOCUMENT_ROOT"]."/assets/PHPMailer_sdfkjhweouihfrncvodsihfo/PHPMailer.php";
require $_SERVER["DOCUMENT_ROOT"]."/assets/PHPMailer_sdfkjhweouihfrncvodsihfo/SMTP.php";

function SendMailHtml($to, $subject, $content, $from = false) {

    global $CONFIG, $LANG;

    $applicationName = $LANG->Get("applicationname");

    $mail = new PHPMailer\PHPMailer\PHPMailer(false);
    $mail->isSMTP();
    $mail->Host = $CONFIG["smtpserver"];
    $mail->Port = $CONFIG["smtpport"];
    $mail->SMTPAuth   = true;
    $mail->Username   = $CONFIG["smtpusername"];
    $mail->Password   = $CONFIG["smtppassword"];
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->CharSet ="UTF-8";

    if ($from === false) {
        $mail->setFrom($CONFIG["smtpfrom"], $applicationName);
    } else {
        $mail->setFrom($from);
    }

    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $content;

    $mail->send();

}

?>