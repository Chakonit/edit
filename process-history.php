<?php
session_start();
$open_connect = 1;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('connect.php');

if (isset($_SESSION['shop_id']) && isset($_SESSION['username_account'])) {
    $shop_id = $_SESSION['shop_id'];
    $username_account = $_SESSION['username_account'];
}

header('Content-Type: application/json'); // ตั้งค่า Content-Type ก่อนเริ่มการส่งข้อมูล

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if (is_array($input)) {
        $shop_id = $input['shop_id'] ?? $shop_id;
        $username_account = $input['username_account'] ?? $username_account;
        $payment_method = $input['payment_method'] ?? null;
        $cash_received = $input['cash_received'] ?? null;
        $amount_due = $input['amount_due'] ?? null;
        $menu_items = $input['menu_items'] ?? [];
        $change_amount = $input['change_amount'] ?? 0.00; // รับค่า change_amount

        error_log("Change Amount: " . $change_amount);

        if ($payment_method === 'cash' && $cash_received >= $amount_due) {
            $payment_status = 'paid';
        } else if ($payment_method === 'qrcode') {
            $payment_status = 'paid';
        } else {
            $payment_status = 'pending';
        }

        $menu_items_data = [];
        foreach ($menu_items as $item) {
            $menu_items_data[] = [
                'menu_name' => $item['menu_name'],
                'size' => $item['size'],
                'quantity' => $item['quantity'],
                'total_price' => $item['total_price']
            ];
        }

        if (empty($menu_items_data)) {
            echo json_encode(['success' => false, 'message' => 'ไม่มีรายการเมนู']);
            exit();
        }

        $menu_items_json = json_encode($menu_items_data);

        // เพิ่มการบันทึก change_amount ลงในฐานข้อมูล
        $query = "INSERT INTO history (username_account, shop_id, payment_method, cash_received, amount_due, purchase_date, payment_status, menu_items, created_at, change_amount)
                  VALUES (?, ?, ?, ?, ?, NOW(), ?, ?, NOW(), ?)";

        $stmt = mysqli_prepare($connect, $query);

        if ($stmt === false) {
            error_log("MySQL prepare error: " . mysqli_error($connect));
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL']);
            exit();
        }

        mysqli_stmt_bind_param($stmt, 'sissdsss', $username_account, $shop_id, $payment_method, $cash_received, $amount_due, $payment_status, $menu_items_json, $change_amount);

        if (mysqli_stmt_execute($stmt) === false) {
            error_log("MySQL execute error: " . mysqli_error($connect));
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
            exit();
        }

        $last_id = mysqli_insert_id($connect);
        mysqli_stmt_close($stmt);
        mysqli_close($connect);

        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ', 'last_id' => $last_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON format']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่มีข้อมูลที่ส่งมา']);
}

?>