<?php
session_start();
$open_connect = 1;
require('connect.php');

if (!isset($_SESSION['username_account'])) {
    header('Location: home1.php');
    exit();
}

$username_account = $_SESSION['username_account'];
mysqli_set_charset($connect, 'utf8');

$shop_id = isset($_POST['shop_id']) ? intval($_POST['shop_id']) : 0;
if ($shop_id <= 0) {
    echo "shop_id ไม่ถูกต้องหรือไม่ได้ถูกส่งมาใน process-menu.php.<br>";
    exit();
}

$menu_names = isset($_POST['menu_name']) ? $_POST['menu_name'] : [];
$sizes = isset($_POST['size']) ? $_POST['size'] : [];
$prices = isset($_POST['price']) ? $_POST['price'] : [];
$images = isset($_FILES['image']) ? $_FILES['image'] : [];

$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true); // สร้างโฟลเดอร์ถ้าไม่มีอยู่
}

foreach ($menu_names as $index => $menu_name) {
    $menu_name = mysqli_real_escape_string($connect, $menu_name);

    if (isset($sizes[$index]) && isset($prices[$index])) {
        $current_sizes = $sizes[$index];
        $current_prices = $prices[$index];

        if (is_array($current_sizes) && is_array($current_prices)) {
            // จัดการรูปภาพหนึ่งครั้งต่อหนึ่งฟอร์ม (เมนู)
            $image = ''; // ค่าเริ่มต้นสำหรับชื่อไฟล์รูปภาพ
            if (isset($images['name'][$index][0]) && $images['error'][$index][0] == 0) {
                $image_name = basename($images['name'][$index][0]);
                $target_file = $target_dir . $image_name;

                // ตรวจสอบชนิดของไฟล์
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($imageFileType, $allowed_types)) {
                    echo "ประเภทไฟล์ไม่ถูกต้องสำหรับเมนูที่ " . ($index + 1) . "<br>";
                    continue;
                }

                // ตรวจสอบขนาดไฟล์
                if ($images['size'][$index][0] > 5000000) { // ขนาดไฟล์ไม่เกิน 5MB
                    echo "ขนาดไฟล์เกินขีดจำกัดสำหรับเมนูที่ " . ($index + 1) . "<br>";
                    continue;
                }

                if (move_uploaded_file($images['tmp_name'][$index][0], $target_file)) {
                    $image = $target_dir . $image_name; // เก็บ path และชื่อไฟล์
                } else {
                    echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์สำหรับเมนูที่ " . ($index + 1) . "<br>";
                    continue;
                }
            }

            foreach ($current_sizes as $size_index => $size) {
                $size = mysqli_real_escape_string($connect, $size);
                $price = isset($current_prices[$size_index]) ? floatval($current_prices[$size_index]) : 0;

                if (!empty($size) && $price > 0) {
                    // เพิ่มข้อมูลเมนูลงในฐานข้อมูล
                    $query = "INSERT INTO menu (shop_id, username_account, menu_name, size, price, image) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = mysqli_prepare($connect, $query);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'isssds', $shop_id, $username_account, $menu_name, $size, $price, $image);
                        if (mysqli_stmt_execute($stmt)) {
                            echo "เพิ่มข้อมูลเมนูสำเร็จสำหรับเมนูที่ " . ($index + 1) . " ขนาดที่ " . ($size_index + 1) . "<br>";
                        } else {
                            echo "เกิดข้อผิดพลาดในการเพิ่มข้อมูลเมนู: " . mysqli_stmt_error($stmt) . "<br>";
                        }
                        mysqli_stmt_close($stmt);
                    } else {
                        echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($connect) . "<br>";
                    }
                } else {
                    echo "กรุณากรอกขนาดและราคาที่ถูกต้องสำหรับเมนูที่ " . ($index + 1) . "<br>";
                }
            }
        } else {
            echo "ข้อมูลขนาดและราคาไม่ถูกต้องสำหรับเมนูที่ " . ($index + 1) . "<br>";
        }
    } else {
        echo "ไม่มีข้อมูลขนาดและราคาสำหรับเมนูที่ " . ($index + 1) . "<br>";
    }
}

mysqli_close($connect);

$_SESSION['save_success'] = true;

header('Location: Menu.php?edit_id=' . $shop_id);
exit();

?>
