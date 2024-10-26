<?php 
session_start();
$open_connect = 1;
require('connect.php');

// ตรวจสอบว่าได้รับ receipt_id และมี session หรือไม่
if (!isset($_GET['receipt_id']) || !isset($_SESSION['username_account'])) {
    echo 'ไม่พบข้อมูล session หรือ receipt_id';
    exit();
}

// รับค่า receipt_id จาก URL และ username_account จาก session
$receipt_id = $_GET['receipt_id'];
$username_account = $_SESSION['username_account'];

mysqli_set_charset($connect, 'utf8');

// ดึงข้อมูลใบเสร็จจาก history ตาม receipt_id และ username_account
$query = "SELECT * FROM history WHERE id = ? AND username_account = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 'is', $receipt_id, $username_account);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $payment_method = $row['payment_method'];
    $cash_received = $row['cash_received'];
    $amount_due = $row['amount_due'];
    $purchase_date = $row['purchase_date'];
    $menu_items = json_decode($row['menu_items'], true); // แปลง JSON เป็น array
    
    // คำนวณเงินทอน
    $change = $cash_received - $amount_due;
} else {
    echo 'ไม่พบข้อมูลใบเสร็จ';
    exit();
}
mysqli_stmt_close($stmt);
mysqli_close($connect);

// ฟังก์ชัน separateSize
function separateSize($menu_name) {
    $parts = explode('-', $menu_name);
    if (count($parts) == 2) {
        $menu_name_clean = trim($parts[0]);
        $size = trim($parts[1]);
    } else {
        $menu_name_clean = $menu_name;
        $size = '';
    }
    return [$menu_name_clean, $size];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จรับเงิน</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* สีพื้นหลัง */
        }
        .container {
            margin-top: 50px;
            background-color: #ffffff; /* พื้นหลังของ container */
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* เงาเพื่อให้ดูมีมิติ */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">ใบเสร็จรับเงิน</h1>
        <p class="text-right">วันที่ชำระ: <?php echo htmlspecialchars($purchase_date); ?></p>
        <p class="text-right">วิธีการชำระเงิน: <?php echo htmlspecialchars($payment_method); ?></p>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ชื่อเมนู</th>
                    <th>ราคา</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menu_items as $item): ?>
                    <?php
                    list($menu_name_clean, $size) = separateSize($item['menu_name']);
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($menu_name_clean); ?></td>
                        <td><?php echo htmlspecialchars($item['total_price']); ?> บาท</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="text-right">ยอดที่ชำระ: <?php echo htmlspecialchars($amount_due); ?> บาท</p>
        <p class="text-right">ยอดที่จ่าย: <?php echo htmlspecialchars($cash_received); ?> บาท</p>
        <p class="text-right">เงินทอน: <?php echo htmlspecialchars($change); ?> บาท</p>
        
        <div class="d-flex justify-content-between mt-4">
            <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
            <a href="history.php?shop_id=<?php echo $_SESSION['shop_id']; ?>" class="btn btn-secondary">ย้อนกลับ</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
