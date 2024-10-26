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
    // ตรวจสอบหากไม่มี shop_id
    if (isset($_SESSION['shop_id']) && $_SESSION['shop_id'] > 0) {
        $shop_id = $_SESSION['shop_id']; // ดึงค่าจาก session ถ้ามี
    } else {
        echo "ไม่พบ shop_id กรุณาตรวจสอบอีกครั้ง";
        exit(); // ออกจากสคริปต์หากไม่มี shop_id
    }
}

$save_success = isset($_SESSION['save_success']) ? $_SESSION['save_success'] : false;
unset($_SESSION['save_success']); // เคลียร์ค่าเพื่อไม่ให้แสดงแจ้งเตือนซ้ำ

// ดึงข้อมูลโลโก้จากฐานข้อมูล
$query = "SELECT logo FROM information WHERE shop_id = $shop_id";
$result = mysqli_query($connect, $query);
$shop_data = mysqli_fetch_assoc($result);
$shop_logo = $shop_data['logo'] ?? ''; // เอาโลโก้ที่ได้หรือเป็นค่าว่าง

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมนูอาหาร</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        /* กำหนดฟอนต์ให้เป็น Prompt */
        body {
            font-family: 'Prompt', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                
                <div class="text-center mb-4">
                    <?php if (!empty($shop_logo)): ?>
                        <img src="<?php echo htmlspecialchars($shop_logo); ?>" alt="Logo" class="img-fluid" style="max-width: 150px; max-height: 150px;"> <!-- แสดงภาพ -->
                    <?php else: ?>
                        <p>ไม่มีภาพที่ร้านค้าได้ใส่</p>
                    <?php endif; ?>
                </div>
                <h2 class="text-center mb-4">เมนูอาหาร</h2>
                <h3 class="text-center mb-4">รายการอาหาร</h3>

                <!-- แสดงข้อความแจ้งเตือนการบันทึกข้อมูล -->
                <?php if ($save_success): ?>
                    <div class="alert alert-success text-center" role="alert">
                        บันทึกข้อมูลเรียบร้อย
                    </div>
                <?php endif; ?>

                <form id="menu-form" action="process-menu.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="shop_id" value="<?php echo htmlspecialchars($shop_id); ?>">

                    <div id="product-container"></div>
                    
                    <!-- ใช้ปุ่ม Bootstrap -->
                    <div class="text-center mt-3">
                        <button type="button" id="add-product-button" class="btn btn-outline-primary mb-2">เพิ่มสินค้าใหม่</button>
                    </div>

                    <!-- ปุ่มการทำงาน -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-success mb-2">บันทึกข้อมูล</button>
                        <button type="button" class="btn btn-secondary mb-2" onclick="window.location.href='index1.php';">เสร็จสิ้น</button>
                        <button type="button" class="btn btn-warning mb-2" onclick="window.location.href='information.php';">ย้อนกลับ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js" defer></script>
</body>
</html>
