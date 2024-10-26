<?php
session_start();
$open_connect = 1;
require('connect.php');

if (isset($_GET['shop_id'])) {
    $shop_id = intval($_GET['shop_id']);
    $query = "SELECT * FROM information WHERE shop_id = $shop_id";
    $result = mysqli_query($connect, $query);

    if (!$result) {
        die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . mysqli_error($connect));
    }

    $shops = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    header('Location: index1.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าร้าน</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <center>
        <h1>Logo</h1>
        <?php if (!empty($shops)): ?>
            <?php foreach ($shops as $shop): ?>
                <div class="shop">
                    <img src="<?php echo htmlspecialchars($shop['logo']); ?>" alt="Logo" style="max-width: 150px; max-height: 150px;">
                    <h2><?php echo htmlspecialchars($shop['shop_name']); ?></h2>
                    <p>ประเภท: <?php echo htmlspecialchars($shop['food_type']); ?></p>
                    <p>มีหน้าร้าน: <?php echo htmlspecialchars($shop['storefront']); ?></p>
                    <h3>รายการอาหาร&สินค้า:</h3>
                        <ul>
                        <?php 
                            $products = json_decode($shop['products'], true);
                            if (is_string($products)) {
                                $products = json_decode($products, true);
                            }
                            foreach ($products as $product): 
                            ?>
                            <li><?php echo htmlspecialchars($product, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                        </ul>
                    <h3>ขนาดอาหาร&สินค้า:</h3>
                        <ul>
                        <?php 
                            $sizes = json_decode($shop['sizes'], true);
                            if (is_string($sizes)) {
                            $sizes = json_decode($sizes, true);
                            }
                        foreach ($sizes as $size): 
                        ?>
                        <li><?php echo htmlspecialchars($size, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                        </ul>
                </div>
                <button><a href="index1.php">ย้อนกลับ</a></button>
                <hr>
            <?php endforeach; ?>
        <?php else: ?>
            <p>ไม่มีข้อมูลร้านค้า</p>
        <?php endif; ?>
    </center>
</body>
</html>
