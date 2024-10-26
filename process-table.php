<?php
session_start();
$open_connect = 1;
require('connect.php');

if (!isset($_SESSION['username_account'])) {
    header('location: home1.php');
    exit();
}

$username_account = $_SESSION['username_account'];
mysqli_set_charset($connect, 'utf8');

$shop_id = isset($_POST['shop_id']) ? intval($_POST['shop_id']) : (isset($_SESSION['shop_id']) ? intval($_SESSION['shop_id']) : 0);
$table_count = isset($_POST['table']) ? intval($_POST['table']) : 0;
$table_prefix = isset($_POST['tablePrefix']) ? $_POST['tablePrefix'] : 'A'; // ตัวอักษรคำนำหน้าชื่อโต๊ะ

// ตรวจสอบว่า shop_id มีอยู่ในตาราง information
$query = "SELECT shop_id FROM information WHERE shop_id = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 'i', $shop_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) == 0) {
    die('Error: shop_id does not exist in the information table.');
}
mysqli_stmt_close($stmt);

$_SESSION['table_count'] = $table_count;
$_SESSION['table_names'] = [];

if ($table_count > 0) {
    for ($i = 1; $i <= $table_count; $i++) {
        $table_name = isset($_POST["table_name_$i"]) ? trim($_POST["table_name_$i"]) : '';
        $_SESSION['table_names'][$i] = $table_name;

        // ตรวจสอบว่ามีชื่อโต๊ะหรือไม่ก่อนบันทึกลงฐานข้อมูล
        if (!empty($table_name)) {
            $query = "INSERT INTO newtable (shop_id, username_account, table_count, table_name) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($connect, $query);
            if (!$stmt) {
                die('Error preparing statement: ' . mysqli_error($connect));
            }
            mysqli_stmt_bind_param($stmt, 'isis', $shop_id, $_SESSION['username_account'], $table_count, $table_name);
            if (!mysqli_stmt_execute($stmt)) {
                die('Error executing statement: ' . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        }
    }

    $_SESSION['table_data_saved'] = true;
    $_SESSION['save_success'] = true;

    header('Location: Table.php?edit_id=' . $shop_id);
    exit();
}
?>
