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
$qrcodePath = ''; // เริ่มต้นค่า

// ดึงข้อมูล QRCODE จากฐานข้อมูล
$sql = "SELECT qrcode_path FROM information WHERE shop_id = ?";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, 'i', $shop_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $qrcodePath = $row['qrcode_path'];
}

// เตรียมคำสั่ง SQL สำหรับเมนู
$query = "SELECT MIN(id) AS id, menu_name, image, GROUP_CONCAT(size ORDER BY size ASC) AS sizes, GROUP_CONCAT(price ORDER BY price ASC) AS prices 
          FROM menu 
          WHERE shop_id = ? 
          GROUP BY menu_name, image";

$stmt = mysqli_prepare($connect, $query);
mysqli_stmt_bind_param($stmt, 'i', $shop_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$menuList = [];
while ($menu = mysqli_fetch_assoc($result)) {
    $menuList[] = $menu;
}

// ดึงข้อมูลเมนูยอดนิยม 5 อันดับ
// คำสั่ง SQL เพื่อดึงข้อมูลจากคอลัมน์ menu_items
$sql = "SELECT menu_items FROM history WHERE username_account = 'fiiw2001' AND shop_id = 120 AND payment_status = 'paid'";
$result = $connect->query($sql);

$menuSales = []; // เก็บยอดขายของแต่ละเมนู

if ($result->num_rows > 0) {
    // วนลูปข้อมูลที่ดึงมา
    while ($row = $result->fetch_assoc()) {
        $menuItems = json_decode($row['menu_items'], true); // แปลง JSON เป็น array
        
        // วนลูปผ่านเมนูแต่ละรายการ
        foreach ($menuItems as $item) {
            $menuName = trim($item['menu_name']); // ชื่อเมนู
            $quantity = isset($item['quantity']) ? $item['quantity'] : 1; // จำนวน
            
            // รวมยอดขายของเมนู
            if (isset($menuSales[$menuName])) {
                $menuSales[$menuName] += $quantity;
            } else {
                $menuSales[$menuName] = $quantity;
            }
        }
    }
}

// จัดเรียงเมนูตามยอดขายจากมากไปน้อย
arsort($menuSales);

// ดึง 5 อันดับเมนูขายดี
$top5Menus = array_slice($menuSales, 0, 5, true);


mysqli_stmt_close($stmt);
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Dashboard</title>
    <link rel="stylesheet" href="styleMain.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="menu-toggle" onclick="toggleMenu()">
                &#9776;
            </div>
            <div class="cart-icon">
                <img src="shopping-cart-01-stroke-rounded.svg" alt="Shopping Cart Icon">
            </div>
            <h1>POS Dashboard</h1>
        </header>
        <nav id="nav-menu">
            <ul>
                <li><a href="Main.php?shop_id=<?php echo $_SESSION['shop_id']; ?>">Home</a></li>
                <li><a href="MainMenu.php?shop_id=<?php echo $_SESSION['shop_id']; ?>">Menu</a></li>
                <li><a href="MainTable.php?shop_id=<?php echo $_SESSION['shop_id']; ?>">Table</a></li>
                <li><a href="History.php?shop_id=<?php echo $_SESSION['shop_id']; ?>">History</a></li>
                <li><a href="index1.php">Exit</a></li>
            </ul>
        </nav>
        <div id="cart" class="hidden">
            <h2>Your Cart</h2>
            <h3>รายการอาหาร</h3>
            <ul id="cart-items"></ul>
            <p>Total: $<span id="cart-total">0.00</span></p>
            <button id="cancel-item">ยกเลิกรายการทั้งหมด</button>
            <p><label>ประเภทการชำระเงิน</label></p>
            <select id="payment-method" class="input-field" required>
                <option value="">-- กรุณาเลือกประเภทการชำระเงิน --</option>
                <option value="cash">เงินสด (Cash on Delivery)</option>
                <option value="qrcode">QRCODE</option>
            </select>
            <!-- ฟิลด์อัปโหลดไฟล์สำหรับ QRCODE -->
            <form action="process-uploadqrcode.php" method="post" enctype="multipart/form-data">
                <div id="qrcode-upload-section" style="display:none;">
                    <label for="qrcode">QRCODE ของร้านเรา:</label>
                    <input type="file" id="qrcode" name="qrcode" accept="image/*" />
                    <!-- แสดง Preview ของ QRCODE -->
                    <img id="qrcode-preview" src="<?php echo $qrcodePath; ?>" alt="QRCODE Preview" style="max-width: 300px; margin-top: 10px; <?php echo $qrcodePath ? 'display:block;' : 'display:none;'; ?>" />
                    <input type="submit" value="บันทึกข้อมูล">
                </div>
            </form>
            <!-- รายละเอียดเงินสด -->
            <div id="cash-details" class="hidden">
                <p>ยอดที่ต้องจ่าย: $<span id="amount-due-display">0.00</span></p>
                <label for="cash-received">จำนวนเงินที่ได้รับ:</label>
                <input type="number" id="cash-received" min="0" step="0.01" placeholder="0.00">
                <p>เงินทอน: $<span id="change-amount-display">0.00</span></p>
            </div>
            <!-- รายละเอียด QRCODE 
            <div id="qrcode-details" class="hidden">
                <label for="cash-received">จำนวนเงินที่ได้รับ:</label>
                <input type="number" id="cash-received" min="0" step="0.01" placeholder="0.00">
            </div>-->
            <div>
                <form id="history-form" action="process-history.php" method="POST">
                    <input type="hidden" id="shop_id" value="<?php echo $_SESSION['shop_id']; ?>">
                    <input type="hidden" id="username_account" value="<?php echo $_SESSION['username_account']; ?>">
                    <input type="hidden" id="payment-method-hidden" name="payment-method">
                    <input type="hidden" id="amount-due" name="amount-due" value="0.00">
                    <input type="hidden" id="cash-received-hidden" name="cash-received" value="0.00">
                    <input type="hidden" id="change-amount" name="change-amount" value="0.00">
                    <input type="hidden" id="menu-items-hidden" name="menu_items" value="">
                    <button type="button" id="confirm-button">ยืนยัน</button>
                </form>
                <button id="cancel-button">ยกเลิก</button>
            </div>
        </div>

        <main>
            <center>
                <section>
                    <h2>Welcome to the POS System</h2>
                    <p>Select an option from the menu to get started.</p>
                </section>
            </center>
        </main>

        <table>
            <caption>List Menu</caption>
            <h5>เมนูยอดนิยม 5 อันดับ</h5>
            <?php foreach ($top5Menus as $menuName => $totalQuantity): ?>
                <p>เมนู: <?php echo htmlspecialchars($menuName); ?> - จำนวนขาย: <?php echo htmlspecialchars($totalQuantity); ?></p>
            <?php endforeach; ?>
            <thead>
                <tr>
                    <th>Menu</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($menuList as $menu):  ?>
                <tr>
                    <td>
                        <img src="<?php echo htmlspecialchars($menu['image'], ENT_QUOTES); ?>" 
                             alt="<?php echo htmlspecialchars($menu['menu_name'], ENT_QUOTES); ?>" 
                             width="100" 
                             onclick="showDetails(<?php echo $menu['id']; ?>, <?php echo $shop_id; ?>)">
                        <p><?php echo htmlspecialchars($menu['menu_name'], ENT_QUOTES); ?></p>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="menuModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h4 id="menuName"></h4>
                <select id="sizeSelect"></select>
                <div id="menuPrice"></div>
                <button onclick="addToCart()">เพิ่มลงตะกร้า</button>
            </div>
        </div>
    </div>
    <script src="scriptMain.js" defer></script>
</body>
</html>
