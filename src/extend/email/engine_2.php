<?php
namespace Juhelib\extend\email;
use PHPMailer\PHPMailer\PHPMailer;
class engine_2 extends config
{
    static function send($To, $Title, $Content) {
        // 处理昵称
        $nickname = batch_str_replace(
            config::$nickname, array('[:sitename:]'), array(config::$sitename)
        );
        $mail = new PHPMailer;
        $mail->isSMTP();
        // 检测是否开启 Debug 模式
        if(strtolower(config::$debug) == 'true') {
            $mail->SMTPDebug = 4;
        }else{
            $mail->SMTPDebug = 0;
        }
        $mail->Host = config::$server;
        // 启用 SMTP 认证
        $mail->SMTPAuth = true;
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            )
        );
        $mail->Username = config::$username;
        $mail->Password = config::$password;
        // 启用 TLS 加密，SSL 也接受
        if(config::$secure == 'ssl') {
            $mail->SMTPSecure = 'ssl';
        }else if(config::$secure == 'tls') {
            $mail->SMTPSecure = 'tls';
        }
        $mail->CharSet = 'UTF-8';
        $mail->Port = config::$port;
        $mail->setFrom(config::$username, $nickname);
        $mail->addAddress($To, '');
        $mail->isHTML(true);
        $mail->Subject = $Title;
        $mail->Body = $Content;
        // 'Message could not be sent。Mailer Error：' . $mail->ErrorInfo;
        return (!$mail->send()) ? "error" : "success";
    }
}