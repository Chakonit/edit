<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// โหลดไฟล์ Composer autoload
require 'vendor/autoload.php'; // เปลี่ยนเส้นทางให้เหมาะสมกับที่ตั้งของไฟล์ composer autoload

// เชื่อมต่อฐานข้อมูล
$servername = "localhost"; // แทนที่ด้วยชื่อเซิร์ฟเวอร์ของคุณ
$username = "username"; // แทนที่ด้วยชื่อผู้ใช้ฐานข้อมูลของคุณ
$password = "password"; // แทนที่ด้วยรหัสผ่านฐานข้อมูลของคุณ
$dbname = "your_database"; // แทนที่ด้วยชื่อฐานข้อมูลของคุณ

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// รับค่าอีเมลจากฟอร์ม
$email = $_POST['email_account'];

// ตรวจสอบว่ามีอีเมลนี้ในฐานข้อมูลหรือไม่
$sql = "SELECT * FROM email_account WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // หากมีอีเมลนี้ในฐานข้อมูล
    $token = bin2hex(random_bytes(50)); // สร้าง token สำหรับการรีเซ็ตรหัสผ่าน
    // ที่นี้คุณสามารถบันทึก token ลงฐานข้อมูลเพื่อนำไปใช้ในการรีเซ็ตรหัสผ่าน

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'fiiw2001@gmail.com'; // เปลี่ยนเป็นอีเมลของคุณ
    $mail->Password = 'stdd kivd edsk fgbp'; // ใช้รหัสผ่านแอปที่สร้างขึ้น
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('noreply@example.com', 'Your Name'); // เปลี่ยนให้เหมาะสม
    $mail->addAddress($email); // ใช้ค่าอีเมลผู้รับที่กรอกในฟอร์ม
    $mail->Subject = 'รีเซ็ตรหัสผ่าน';
    $mail->Body = <<<END
คลิก <a href="http://localhost/Project/reset-password.php?token=$token">ที่นี่</a>
เพื่อรีเซ็ตรหัสผ่านของคุณ
END;

    // ส่งอีเมล
    if ($mail->send()) {
        echo "อีเมลส่งเรียบร้อยแล้ว กรุณาตรวจสอบกล่องขาเข้าของคุณ";
    } else {
        echo "ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}";
    }
} else {
    echo "ไม่พบอีเมลนี้ในระบบ";
}

$stmt->close();
$conn->close();
?>