<?php
session_start();
$open_connect = 1;
require('connect.php');

// ตรวจสอบว่ามีการส่งค่า shop_id หรือไม่
if (!isset($_SESSION['username_account']) || !isset($_GET['shop_id'])) {
    header('location: home1.php');
    exit();
}

$shop_id = $_GET['shop_id'];
$_SESSION['shop_id'] = $shop_id; // เก็บค่าใน session เพื่อใช้ในอนาคต

$username_account = $_SESSION['username_account'];
mysqli_set_charset($connect, 'utf8');

// ฟังก์ชันแยกขนาดออกจากชื่อเมนู โดยไม่ต้องใช้วงเล็บ
function separateSize($menu_name) {
    if (preg_match('/\((.*?)\)$/', $menu_name, $matches)) {
        $size = $matches[1];
        $menu_name_clean = trim(preg_replace('/\s*\(.*?\)\s*/', '', $menu_name));
        return [$menu_name_clean, $size];
    }
    return [$menu_name, ''];
}

// สร้างคำสั่ง SQL สำหรับการดึงข้อมูล
$query = "SELECT id, menu_items, payment_status, payment_method, purchase_date 
          FROM history 
          WHERE username_account = ? AND shop_id = ?";

$params = [$username_account, $shop_id];

// ตรวจสอบว่ามีการกรองหรือไม่
if (isset($_GET['start_date']) && isset($_GET['end_date']) && isset($_GET['filter_type'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
    $filter_type = $_GET['filter_type'];

    $query .= " AND DATE(purchase_date) BETWEEN ? AND ?";
    $params[] = $start_date;
    $params[] = $end_date;

    if ($filter_type == 'weekly') {
        $query .= " AND WEEK(purchase_date, 1) = WEEK(?, 1) AND YEAR(purchase_date) = YEAR(?)";
        $params[] = $start_date;
        $params[] = $start_date;
    } elseif ($filter_type == 'monthly') {
        $query .= " AND MONTH(purchase_date) = MONTH(?) AND YEAR(purchase_date) = YEAR(?)";
        $params[] = $start_date;
        $params[] = $start_date;
    } elseif ($filter_type == 'yearly') {
        $query .= " AND YEAR(purchase_date) = YEAR(?)";
        $params[] = $start_date;
    }
}

// เตรียมคำสั่ง SQL
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, str_repeat('s', count($params)), ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die('Query Failed: ' . mysqli_error($connect));
}

function displayHistory($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<ul class='list-group'>";
        while ($row = mysqli_fetch_assoc($result)) {
            $menu_items = json_decode($row['menu_items'], true);
            if (is_array($menu_items)) {
                echo "<li class='list-group-item'>";
                echo "<h5>คำสั่งซื้อ</h5>";
                echo "<p><strong>สถานะการชำระเงิน:</strong> " . htmlspecialchars($row['payment_status']) . "</p>";
                echo "<p><strong>วิธีการชำระเงิน:</strong> " . htmlspecialchars($row['payment_method']) . "</p>";
                echo "<ul class='list-group'>";
                foreach ($menu_items as $menu_item) {
                    echo "<li class='list-group-item'>";
                    list($menu_name_clean, $size) = separateSize($menu_item['menu_name']);
                    echo "<p><strong>ชื่อเมนู:</strong> " . htmlspecialchars($menu_name_clean) . "</p>";
                    echo "<p><strong>จำนวน:</strong> " . htmlspecialchars($menu_item['quantity']) . "</p>";
                    echo "<p><strong>ราคารวม:</strong> " . htmlspecialchars($menu_item['total_price']) . " บาท</p>";
                    echo "</li>";
                }
                echo "</ul>";
                echo "<p><strong>วันที่ชำระเงิน:</strong> " . htmlspecialchars($row['purchase_date']) . "</p>";
                echo "<p><a href='bill.php?receipt_id=" . $row['id'] . "' class='btn btn-primary'>ดูใบเสร็จ</a></p>";
                echo "</li>";
            } else {
                echo "<li class='list-group-item'>ไม่พบข้อมูลเมนู</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>ไม่มีประวัติการขายในช่วงเวลาที่เลือก</p>";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MainHistory</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete() {
            return confirm('คุณแน่ใจว่าต้องการลบข้อมูลนี้?');
        }
    </script>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="mb-4">List History</h1>
        <form action="history.php" method="GET" class="mb-4">
            <input type="hidden" name="shop_id" value="<?php echo htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES); ?>">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="start_date">วันที่เริ่มต้น:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="end_date">วันที่สิ้นสุด:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">กรองข้อมูล</button>
        </form>

        <?php
        // แสดงผลข้อมูล history
        displayHistory($result);
        ?>
        
        <a href="Main.php?shop_id=<?php echo htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES); ?>" class="btn btn-secondary">ย้อนกลับ</a>
    </div>
</body>
</html>
