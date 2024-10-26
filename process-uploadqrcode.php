<?php 
session_start();
$open_connect = 1;
require('connect.php'); // ตรวจสอบให้แน่ใจว่ามีการเรียกใช้ไฟล์นี้

if (isset($_SESSION['shop_id']) && isset($_SESSION['username_account'])) {
    $shop_id = $_SESSION['shop_id'];
    $username_account = $_SESSION['username_account'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['qrcode']) && $_FILES['qrcode']['error'] == UPLOAD_ERR_OK) {
        // กำหนดที่อยู่สำหรับบันทึกไฟล์
        $uploadDir = 'uploads/'; // โฟลเดอร์ที่ต้องการเก็บไฟล์
        $uploadFile = $uploadDir . basename($_FILES['qrcode']['name']);
        
        // ตรวจสอบและย้ายไฟล์ไปยังที่อยู่ที่กำหนด
        if (move_uploaded_file($_FILES['qrcode']['tmp_name'], $uploadFile)) {
            // ตรวจสอบให้แน่ใจว่ามี shop_id ที่ถูกต้อง
            // ปรับคำสั่ง SQL เพื่ออัปเดต qrcode_path
            $sql = "UPDATE information SET qrcode_path = '$uploadFile' WHERE shop_id = '$shop_id'";
            
            // ใช้ $connect แทน $connection
            if (mysqli_query($connect, $sql)) {
                echo "อัปโหลด QRCODE และบันทึกข้อมูลเรียบร้อย";
            } else {
                echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . mysqli_error($connect);
            }
        } else {
            echo "การอัปโหลดไฟล์ล้มเหลว";
        }
    } else {
        echo "กรุณาเลือกไฟล์ QRCODE เพื่ออัปโหลด";
    }
}

// ปิดการเชื่อมต่อ
mysqli_close($connect);
?>
