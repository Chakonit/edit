<?php  
session_start();
$open_connect = 1;
require('connect.php');

// ตรวจสอบว่ามีการส่งค่า shop_id หรือไม่
if (!isset($_SESSION['username_account']) || !isset($_GET['shop_id'])) {
    header('location: home1.php');
    exit();
}

// รับค่า shop_id จาก URL หรือ SESSION
$shop_id = $_GET['shop_id'];
$_SESSION['shop_id'] = $shop_id; // เก็บค่าใน session เพื่อใช้ในอนาคต

$username_account = $_SESSION['username_account'];
mysqli_set_charset($connect, 'utf8');

// ใช้ Prepared Statements
$query = "SELECT menu_name, size, price FROM menu WHERE username_account = ? AND shop_id = ?";
$stmt = mysqli_prepare($connect, $query);

// ตรวจสอบการเตรียมคำสั่ง
if (!$stmt) {
    die('Prepared Statement Failed: ' . mysqli_error($connect));
}

// ผูกค่ากับ Prepared Statement
mysqli_stmt_bind_param($stmt, 'si', $username_account, $shop_id);

// ดำเนินการคำสั่ง
if (!mysqli_stmt_execute($stmt)) {
    die('Execution Failed: ' . mysqli_stmt_error($stmt));
}

// รับผลลัพธ์
$result = mysqli_stmt_get_result($stmt);

// ตรวจสอบว่ามีผลลัพธ์หรือไม่
if (!$result) {
    die('Query Failed: ' . mysqli_error($connect));
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MainMenu</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .menu-price {
            color: green;
            font-weight: bold;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .btn {
            margin-right: 10px;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm('คุณแน่ใจว่าต้องการลบข้อมูลนี้?');
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="text-center">รายการเมนู</h1>
        <?php
        // แสดงผลข้อมูลเมนู
        if (mysqli_num_rows($result) > 0) {
            echo "<ul class='list-group'>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                echo "<div>";
                echo "<p class='mb-0'>เมนู: " . htmlspecialchars($row['menu_name'], ENT_QUOTES) . "<br>ขนาด: " . htmlspecialchars($row['size'], ENT_QUOTES) . "<br>ราคา: <span class='menu-price'>" . htmlspecialchars($row['price'], ENT_QUOTES) . " บาท</span></p>";
                echo "</div>";
                echo "<div class='menu-actions'>";
                echo "<a href='Menu.php?shop_id=" . htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES) . "&menu_name=" . urlencode($row['menu_name']) . "' class='btn btn-warning'>แก้ไข</a>";
                // ใช้ฟอร์มสำหรับปุ่มลบ
                echo "<form action='delete_menu.php' method='GET' style='display:inline;' onsubmit='return confirmDelete();'>";
                echo "<input type='hidden' name='shop_id' value='" . htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES) . "'>";
                echo "<input type='hidden' name='menu_name' value='" . htmlspecialchars($row['menu_name'], ENT_QUOTES) . "'>";
                echo "<input type='hidden' name='size' value='" . htmlspecialchars($row['size'], ENT_QUOTES) . "'>";
                echo "<button type='submit' class='btn delete-btn'>ลบ</button>";
                echo "</form>";
                echo "</div>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "<div class='alert alert-info text-center' role='alert'>ไม่มีเมนู</div>";
            echo "<div class='text-center'>";
            echo "<a href='Menu.php?shop_id=" . htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES) . "' class='btn btn-primary'>เพิ่มเมนู</a>";
            echo "<a href='Main.php?shop_id=" . htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES) . "' class='btn btn-secondary'>ย้อนกลับ</a>";
            echo "</div>";
        }
        ?>
        <div class="text-center mt-3">
            <a href="Menu.php?shop_id=<?php echo htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES); ?>" class="btn btn-primary">เพิ่มเมนู</a>   
            <a href="Main.php?shop_id=<?php echo htmlspecialchars($_SESSION['shop_id'], ENT_QUOTES); ?>" class="btn btn-secondary">ย้อนกลับ</a>   
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
