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
if (isset($_GET['shop_id'])) {
    $shop_id = $_GET['shop_id'];
    $_SESSION['shop_id'] = $shop_id; // เก็บค่าใน session เพื่อใช้ในอนาคต
} else {
    $shop_id = $_SESSION['shop_id'];
}

$username_account = $_SESSION['username_account'];
mysqli_set_charset($connect, 'utf8');

$query = "SELECT table_count, table_name FROM newtable WHERE username_account = '$username_account' AND shop_id = '$shop_id'";
$result = mysqli_query($connect, $query);

if (!$result) {
    die('Query Failed: ' . mysqli_error($connect));
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Table</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmDelete() {
            return confirm('คุณแน่ใจว่าต้องการลบข้อมูลนี้?');
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h1 class="text-center mb-4">รายการโต๊ะ</h1>
                <?php
                // แสดงผลข้อมูลโต๊ะ
                if (mysqli_num_rows($result) > 0) {
                    echo "<ul class='list-group'>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                        echo "<div>";
                        echo "<p>จำนวนโต๊ะ: " . $row['table_count'] . " | ชื่อโต๊ะ: " . $row['table_name'] . "</p>";
                        echo "</div>";
                        echo "<div class='table-actions'>";
                        echo "<a href='Table.php?shop_id=" . $_SESSION['shop_id'] . "&table_name=" . urlencode($row['table_name']) . "' class='btn btn-warning btn-sm'>แก้ไข</a>";
                        // ใช้ฟอร์มสำหรับปุ่มลบ
                        echo "<form action='delete_table.php' method='GET' style='display:inline;' onsubmit='return confirmDelete();'>";
                        echo "<input type='hidden' name='shop_id' value='" . htmlspecialchars($_SESSION['shop_id']) . "'>";
                        echo "<input type='hidden' name='table_count' value='" . htmlspecialchars($row['table_count']) . "'>";
                        echo "<input type='hidden' name='table_name' value='". htmlspecialchars($row['table_name']). "'>";
                        echo "<button type='submit' class='btn btn-danger btn-sm'>ลบ</button>";
                        echo "</form>";
                        echo "</div>";
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<div class='text-center mt-3'>";
                    echo "<p>ไม่มีข้อมูลโต๊ะ</p>";
                    echo "</div>";
                }
                ?>
                <div class="text-center mt-4">
                    <a href="Table.php?shop_id=<?php echo $_SESSION['shop_id']; ?>" class="btn btn-primary">เพิ่มข้อมูลโต๊ะ</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
