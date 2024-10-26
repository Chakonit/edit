<?php 
session_start();
$open_connect = 1;
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('connect.php');


// ตรวจสอบว่ามีการส่งค่า username_account, menuId, shop_id หรือไม่
if (!isset($_SESSION['username_account']) || !isset($_GET['menuId']) || !isset($_GET['shop_id'])) {
    echo json_encode(['error' => 'Missing required parameters']);
    exit();
}

$shopId = $_GET['shop_id'];
$menuId = $_GET['menuId'];

// ตรวจสอบว่า shopId และ menuId มีค่าหรือไม่
if (!$shopId || !$menuId) {
    echo json_encode(["error" => "shop_id หรือ menuId ไม่ถูกต้อง"]);
    exit();
}

// สร้างตัวแปรเพื่อเก็บค่าผลลัพธ์
$data = [
    "menu_name" => "",
    "sizes" => [],
    "prices" => [],
    "image" => "" // เก็บ URL ของภาพ
];

// Query เพื่อดึงข้อมูลเมนูทั้งหมดที่มีขนาดและราคาต่างกัน
$query = "SELECT menu_name, size, price, image FROM menu WHERE menu_name = (SELECT menu_name FROM menu WHERE id = ?) AND shop_id = ?";

$stmt = $connect->prepare($query);
if (!$stmt) {
    echo json_encode(["error" => "Query preparation failed: " . $connect->error]);
    exit();
}
$stmt->bind_param('ii', $menuId, $shopId);
$stmt->execute();
$result = $stmt->get_result();

$firstRow = true; // ตัวแปรเพื่อดึงรูปภาพแค่ครั้งแรกเท่านั้น
while ($row = $result->fetch_assoc()) {
    if ($firstRow) {
        $data['menu_name'] = $row['menu_name'];
        $data['image'] = $row['image']; // ตั้งค่ารูปภาพครั้งแรก
        $firstRow = false; // เปลี่ยนตัวแปรเพื่อไม่ให้ดึงรูปอีก
    }
    // เพิ่มขนาดและราคาในลิสต์
    $data['sizes'][] = $row['size'];
    $data['prices'][] = $row['price'];
}

if (empty($data['sizes'])) {
    echo json_encode(["error" => "ไม่พบเมนูนี้ในฐานข้อมูล"]);
    exit();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$connect->close();

// ตั้งค่าหัวข้อเป็น JSON และส่งค่ากลับ
header('Content-Type: application/json');
echo json_encode($data);

?>
