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

$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$shop_data = [];
$shop_id = 0;

if (isset($_SESSION['shop_data'])) {
    $shop_data = $_SESSION['shop_data'];
    $shop_id = isset($shop_data['shop_id']) ? $shop_data['shop_id'] : $edit_id;
} else {
    $shop_id = $edit_id;
}

if (isset($_GET['reset_shop_data']) && $_GET['reset_shop_data'] == '1') {
    unset($_SESSION['shop_data']);
} else {
    if ($edit_id && empty($_SESSION['shop_data'])) {
        $query = "SELECT * FROM information WHERE shop_id = ? AND username_account = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, 'is', $edit_id, $username_account);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $_SESSION['shop_data'] = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['next_button'])) {
    $shop_id = isset($_POST['shop_id']) ? intval($_POST['shop_id']) : 0;
    header('Location: Table.php?shop_id=' . $shop_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กรอกข้อมูล</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h1 class="text-center mb-4">Procha</h1>
                <h2 class="text-center mb-4">กรอกข้อมูล</h2>
                <form id="information-form" action="process-information.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="shop_id" value="<?php echo htmlspecialchars($shop_id); ?>">
                    <input type="hidden" name="remove_logo" id="remove_logo" value="0">

                    <div class="form-group">
                        <label>โลโก้</label>
                        <input name="logo" type="file" accept="image/*" class="form-control-file" <?php echo $edit_id ? '' : 'required'; ?>>
                        <?php if ($edit_id && !empty($shop_data['logo'])): ?>
                            <div style="position: relative; display: inline-block; margin-top: 10px;">
                                <img src="<?php echo htmlspecialchars($shop_data['logo']); ?>" alt="Logo" style="max-width: 50px; max-height: 50px;">
                                <button type="button" class="btn btn-danger btn-sm" style="position: absolute; top: 0; right: 0;" onclick="document.getElementById('remove_logo').value = '1'; this.parentNode.style.display = 'none';">X</button>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>ชื่อร้านค้า</label>
                        <input name="shop_name" type="text" class="form-control" value="<?php echo htmlspecialchars($shop_data['shop_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>ประเภทอาหาร</label>
                        <select name="food_type" class="form-control" required>
                            <option value="">-- กรุณาเลือกประเภทอาหาร --</option>
                            <option value="อาหารตามสั่ง" <?php echo isset($shop_data['food_type']) && $shop_data['food_type'] == 'อาหารตามสั่ง' ? 'selected' : ''; ?>>อาหารตามสั่ง</option>
                            <option value="ส้มตำ" <?php echo isset($shop_data['food_type']) && $shop_data['food_type'] == 'ส้มตำ' ? 'selected' : ''; ?>>ส้มตำ</option>
                            <option value="ก๋วยเตี๋ยว" <?php echo isset($shop_data['food_type']) && $shop_data['food_type'] == 'ก๋วยเตี๋ยว' ? 'selected' : ''; ?>>ก๋วยเตี๋ยว</option>
                            <option value="ผัดไท" <?php echo isset($shop_data['food_type']) && $shop_data['food_type'] == 'ผัดไท' ? 'selected' : ''; ?>>ผัดไท</option>
                            <option value="มาม่า" <?php echo isset($shop_data['food_type']) && $shop_data['food_type'] == 'มาม่า' ? 'selected' : ''; ?>>มาม่า</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>มีโต๊ะนั่ง:</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="yes" name="has_seating" value="1" class="form-check-input" <?php echo (isset($shop_data['has_seating']) && $shop_data['has_seating'] == '1') ? 'checked' : ''; ?>>
                            <label for="yes" class="form-check-label">มี</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" id="no" name="has_seating" value="0" class="form-check-input" <?php echo (isset($shop_data['has_seating']) && $shop_data['has_seating'] == '0') ? 'checked' : ''; ?>>
                            <label for="no" class="form-check-label">ไม่มี</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">บันทึกข้อมูล</button>

                    <div class="form-group mt-3 text-center">
                        <button type="button" class="btn btn-success" id="next-button">ถัดไป</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='index1.php';">ย้อนกลับ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="script.js" defer></script>
</body>
</html>
