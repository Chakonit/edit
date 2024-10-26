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

// รับค่า shop_id
$shop_id = isset($_GET['shop_id']) ? intval($_GET['shop_id']) : 0;

if ($shop_id > 0) {
    $_SESSION['shop_id'] = $shop_id; // เก็บค่าใน session
} else {
    if (isset($_SESSION['shop_id']) && $_SESSION['shop_id'] > 0) {
        $shop_id = $_SESSION['shop_id']; // ดึงค่าจาก session ถ้ามี
    } else {
        echo "ไม่พบ shop_id กรุณาตรวจสอบอีกครั้ง";
        exit(); // ออกจากสคริปต์หากไม่มี shop_id
    }
}

// ตรวจสอบการรีเซ็ตข้อมูล
if (isset($_SESSION['reset_table_data']) && $_SESSION['reset_table_data'] == true) {
    unset($_SESSION['table_data']);
    unset($_SESSION['table_count']);
    unset($_SESSION['table_names']);
    $_SESSION['reset_table_data'] = false; // รีเซ็ตการรีเซ็ต
}

// แสดงข้อมูลที่บันทึกแล้ว
$table_count = isset($_SESSION['table_count']) ? $_SESSION['table_count'] : 0;
$table_names = isset($_SESSION['table_names']) ? $_SESSION['table_names'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Table</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* สีพื้นหลัง */
        }
        .container {
            margin-top: 50px;
            background-color: #ffffff; /* พื้นหลังของ container */
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* เงาเพื่อให้ดูมีมิติ */
        }
        .form-buttons {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">จำนวนโต๊ะ</h1>
        <form action="process-table.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="shop_id" value="<?php echo $shop_id; ?>">
            <div class="form-group">
                <label for="table">กรอกจำนวนโต๊ะ</label>
                <input type="number" class="form-control" name="table" id="table" value="<?php echo htmlspecialchars($table_count); ?>" required>
            </div>
            <div class="form-group">
                <label for="tablePrefix">กรอกตัวอักษรสำหรับชื่อโต๊ะ</label>
                <input type="text" class="form-control" name="tablePrefix" id="tablePrefix" maxlength="1" value="A" required>
            </div>
            <div class="form-buttons">
                <button type="button" class="btn btn-primary" onclick="showNameFields()">ยืนยัน</button>
            </div>
            <div id="name-fields" class="mt-3">
                <?php
                if ($table_count > 0) {
                    for ($i = 1; $i <= $table_count; $i++) {
                        $name = isset($table_names[$i]) ? htmlspecialchars($table_names[$i]) : '';
                        echo '<div class="form-group">';
                        echo '<label for="name_table_' . $i . '">ชื่อโต๊ะ ' . $i . '</label>';
                        echo '<input type="text" class="form-control" name="table_name_' . $i . '" id="table_name_' . $i . '" value="' . $name . '">';
                        echo '</div>';
                    }
                }
                ?>
            </div>
            <input type="submit" value="บันทึกข้อมูล" class="btn btn-success mt-3">  
            <div class="form-buttons">
                <button type="button" class="btn btn-info" onclick="window.location.href='Menu.php';">ถัดไป</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='information.php?shop_id=<?php echo $shop_id; ?>';">ย้อนกลับ</button>
            </div>
        </form>
    </div>

    <script src="script2.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
