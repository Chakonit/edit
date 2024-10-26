<?php
session_start();
$open_connect = 1;
require('connect.php');

// ตรวจสอบสถานะการเข้าสู่ระบบ
if (!isset($_SESSION['username_account'])) {
    header('location: home1.php');
    exit();
} elseif (isset($_GET['logout'])) {
    session_destroy();
    header('location: home1.php');
    exit();
} elseif (isset($_GET['reset_shop_data'])) {
    // ล้างข้อมูลร้านค้า
    unset($_SESSION['shop_data']);
} else {
    $username_account = $_SESSION['username_account'];
    $query_show = "SELECT * FROM account WHERE username_account = ?";
    $stmt = mysqli_prepare($connect, $query_show);
    mysqli_stmt_bind_param($stmt, 's', $username_account);
    mysqli_stmt_execute($stmt);
    $result_show = mysqli_stmt_get_result($stmt);
    $result_show = mysqli_fetch_assoc($result_show);
}

// ดึงข้อมูลร้านค้า
$query = "SELECT * FROM information WHERE username_account = ?";
$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 's', $username_account);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . mysqli_error($connect));
}

$shops = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROCHA</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style3.css">
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
        <h1 class="text-center">PROCHA</h1>
        <h2 class="text-center">ยินดีต้อนรับคุณ <?php echo htmlspecialchars($result_show['username_account']); ?></h2>

        <div class="text-center my-4">
            <a href="information.php?reset_shop_data=1" class="btn btn-success btn-lg">เพิ่มข้อมูลร้านค้า <span class="badge badge-light">+</span></a>
            <a href="home1.php?logout=1" class="btn btn-danger btn-lg">ออกจากระบบ</a>
        </div>

        <?php if (!empty($shops)): ?>
            <h2 class="text-center my-4">รายการร้านค้า</h2>
            <div class="row">
                <?php foreach ($shops as $shop): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card text-center">
                            <a href="Main.php?shop_id=<?php echo htmlspecialchars($shop['shop_id']); ?>">
                                <img src="<?php echo htmlspecialchars($shop['logo']); ?>" alt="Logo" class="card-img-top" style="max-width: 100%; height: 200px; object-fit: cover;">
                            </a>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($shop['shop_name']); ?></h5>
                                <form action="process-information.php" method="POST" style="display: inline;">
                                    <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($shop['shop_id']); ?>">
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('คุณแน่ใจว่าต้องการลบข้อมูลนี้?');">ลบ</button>
                                </form>
                                <form action="information.php" method="GET" style="display: inline;">
                                    <input type="hidden" name="edit_id" value="<?php echo htmlspecialchars($shop['shop_id']); ?>">
                                    <button type="submit" class="btn btn-warning">แก้ไข</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center">ไม่มีข้อมูลร้านค้า</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
