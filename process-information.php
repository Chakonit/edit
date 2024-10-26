<?php
session_start();
$open_connect = 1;
require('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['username_account'])) {
        header('location: home1.php');
        exit();
    }

    $username_account = $_SESSION['username_account'];
    mysqli_set_charset($connect, 'utf8');

    $shop_id = isset($_POST['shop_id']) ? intval($_POST['shop_id']) : 0;
    $remove_logo = isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1';
    $logo = null;

    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $query = "DELETE FROM information WHERE shop_id = ? AND username_account = ?";
        $stmt = mysqli_prepare($connect, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'is', $delete_id, $username_account);
            if (mysqli_stmt_execute($stmt)) {
                echo "ลบข้อมูลเรียบร้อยแล้ว";
            } else {
                echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . mysqli_error($connect);
            }
            mysqli_stmt_close($stmt);
            header('Location: index1.php');
            exit();
        } else {
            echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($connect);
            exit();
        }
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {  
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['logo']['name']);
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
            $logo = $target_file;
        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์";
            exit();
        }
    } else {
        if ($remove_logo) {
            $logo = ''; // ถ้าเลือกให้ลบรูปภาพ, ให้โลโก้เป็นค่าว่าง
        } else {
            // ดึงโลโก้ที่มีอยู่เดิมจากฐานข้อมูลถ้ามี
            $query = "SELECT logo FROM information WHERE shop_id = ? AND username_account = ?";
            $stmt = mysqli_prepare($connect, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'is', $shop_id, $username_account);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $existing_logo = mysqli_fetch_assoc($result)['logo'];
                mysqli_stmt_close($stmt);
                $logo = $existing_logo;
            }
        }
    }

    // ตรวจสอบค่าที่คาดหวังจาก $_POST
    $shop_name = isset($_POST['shop_name']) ? mysqli_real_escape_string($connect, $_POST['shop_name']) : '';
    $food_type = isset($_POST['food_type']) ? mysqli_real_escape_string($connect, $_POST['food_type']) : '';
    if ($food_type === 'custom') {
        $food_type = isset($_POST['custom_food_type']) ? mysqli_real_escape_string($connect, $_POST['custom_food_type']) : '';
    }
    $has_seating = isset($_POST['has_seating']) ? mysqli_real_escape_string($connect, $_POST['has_seating']) : '';

    // สร้าง query ตามเงื่อนไขที่มี
    if ($shop_id) {
        if ($remove_logo && $logo === '') {
            $query = "UPDATE information SET shop_name=?, food_type=?, has_seating=? WHERE shop_id = ? AND username_account = ?";
            $stmt = mysqli_prepare($connect, $query);
            if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssi', $shop_name, $food_type, $has_seating, $shop_id, $username_account);
            } else {
            echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($connect);
            exit(); 
            }
        } elseif ($logo !== null) {
            $query = "UPDATE information SET logo=?, shop_name=?, food_type=?, has_seating=? WHERE shop_id = ? AND username_account = ?";
            $stmt = mysqli_prepare($connect, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssisi', $logo, $shop_name, $food_type, $has_seating, $shop_id, $username_account);
            } else {
                echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($connect);
                exit();
            }
        } else {
            $query = "UPDATE information SET logo=?, shop_name=?, food_type=?, has_seating=? WHERE shop_id = ? AND username_account = ?";
            $stmt = mysqli_prepare($connect, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'sssisi', $logo, $shop_name, $food_type, $has_seating, $shop_id, $username_account);
            } else {
                echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($connect);
                exit();
            }

        }
    } else {
        $query = "INSERT INTO information (username_account, logo, shop_name, food_type, has_seating) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($connect, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssss', $username_account, $logo, $shop_name, $food_type, $has_seating);
        } else {
            echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . mysqli_error($connect);
            exit();
        }
    }
    
    if ($stmt && mysqli_stmt_execute($stmt)) {
        $shop_id = $shop_id ? $shop_id : mysqli_insert_id($connect);

        // ตั้งค่าเซสชันเพื่อแสดงข้อความสำเร็จ
        $_SESSION['save_success'] = true;
        $_SESSION['shop_id'] = $shop_id;
        // ตั้งค่าเซสชันเพื่อเก็บข้อมูลที่บันทึก
        $_SESSION['shop_data'] = [
            'shop_name' => $shop_name,
            'food_type' => $food_type,
            'has_seating' => $has_seating,
            'logo' => $logo,
        ];

        // ตั้งค่าเซสชันเพื่อรีเซ็ตข้อมูลในหน้า Table.php
        $_SESSION['reset_table_data'] = true;

        // กลับไปที่หน้า information.php พร้อม shop_id
        header('Location: information.php?edit_id=' . $shop_id);
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการเพิ่มหรือแก้ไขข้อมูล: " . mysqli_error($connect);
    }

    if (isset($stmt) && $stmt) {
        mysqli_stmt_close($stmt);
    }
    mysqli_close($connect);
}
?>