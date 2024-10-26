<?php

session_start();
$open_connect = 1;
require('connect.php');
require __DIR__ . '/vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$email = mysqli_real_escape_string($connect, $_POST['email_account']);
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$sql = "UPDATE account
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email_account = ?";

$stmt = $connect->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($connect->affected_rows > 0) {
    $mail = new PHPMailer(true);

    //$mail->SMTPDebug = 2;

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fiiw2001@gmail.com'; 
        $mail->Password = 'stdd kivd edsk fgbp'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply@example.com', 'Your Name');
        $mail->addAddress($email);
        $mail->Subject = 'รีเซ็ตรหัสผ่าน';
        $mail->isHTML(true);
        $mail->Body = <<<END
        คลิก <a href="http://localhost/Project/reset-password.php?token=$token">ที่นี่</a>
        เพื่อรีเซ็ตรหัสผ่านของคุณ
        END;

        $mail->send();
        echo "ส่งข้อความเรียบร้อยแล้ว กรุณาตรวจสอบกล่องจดหมายของคุณ.";
    } catch (Exception $e) {
        echo "ไม่สามารถส่งข้อความได้ ข้อผิดพลาดของระบบส่งจดหมาย: {$mail->ErrorInfo}";
    }
} else {
    echo "กรุณาตรวจสอบบัญชีอีเมลที่ให้ไว้.";
}

$stmt->close();
$connect->close();

?>

