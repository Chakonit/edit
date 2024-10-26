<?php
session_start();
$open_connect = 1;
require('connect.php');

// ตรวจสอบว่ามีค่า shop_id และ menu_name ถูกส่งมา
if (isset($_GET['shop_id']) && isset($_GET['table_name'])) {
    $shop_id = $_GET['shop_id'];
    $table_name = $_GET['table_name'];
    $username_account = $_SESSION['username_account'];

    // ลบข้อมูลเมนูจากฐานข้อมูล
    $query = "DELETE FROM newtable WHERE shop_id = '$shop_id' AND table_name = '$table_name' AND username_account = '$username_account'";
    $result = mysqli_query($connect, $query);

    if ($result) {
        // ถ้าสำเร็จ, ให้กลับไปที่ MainMenu.php พร้อมแสดงข้อความว่าเมนูถูกลบแล้ว
        header("Location: MainTable.php?shop_id=$shop_id&message=deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($connect);
    }
} else {
    // ถ้าไม่มีข้อมูลที่ต้องการลบ, ให้กลับไปที่หน้า MainMenu.php
    header("Location: MainMenu.php");
    exit();
}
?>
