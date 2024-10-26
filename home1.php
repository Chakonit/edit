<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* สีพื้นหลังให้ความรู้สึกเบา */
            display: flex;
            justify-content: center; /* จัดกลางในแนวนอน */
            align-items: center; /* จัดกลางในแนวตั้ง */
            height: 100vh; /* ความสูงเต็มหน้าจอ */
        }
        .login-container {
            width: 400px; /* กว้างของฟอร์ม */
            background-color: #ffffff; /* พื้นหลังของฟอร์มเป็นสีขาว */
            border-radius: 10px; /* มุมมนของฟอร์ม */
            padding: 30px; /* ระยะห่างภายในฟอร์ม */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); /* เงาเพื่อให้ดูมีมิติ */
        }
        .form-title {
            margin-bottom: 20px; /* ระยะห่างด้านล่างหัวข้อ */
        }
        .error-message {
            color: red; /* สีข้อความเมื่อเกิดข้อผิดพลาด */
            margin-bottom: 15px; /* ระยะห่างด้านล่างของข้อความผิดพลาด */
        }
        .btn {
            width: 100%; /* ปุ่มให้เต็มความกว้าง */
            margin-top: 10px; /* ระยะห่างด้านบน */
        }
        .register-link {
            margin-top: 15px; /* ระยะห่างด้านบนสำหรับลิงก์ลงทะเบียน */
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="text-center form-title">เข้าสู่ระบบ</h1>
        <form action="process-login.php" method="POST">
            <?php if(isset($_GET['error']) && !empty($_GET['error'])): ?>
                <div class="error-message text-center">
                    <?php echo htmlspecialchars($_GET['error']); /* แสดงข้อความผิดพลาด */ ?>
                </div>
            <?php endif; ?>
            <div class="form-group">
                <label for="username">Username</label>
                <input name="username_account" 
                       value="<?php if(isset($_COOKIE['user_login'])) echo htmlspecialchars($_COOKIE['user_login']); ?>"
                       type="text" class="form-control" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input name="password_account" type="password" class="form-control" id="password" required>
            </div>
            <div class="d-flex justify-content-between align-items-center">
                <label><input type="checkbox" name="remember" <?php if(isset($_COOKIE['user_login'])) echo 'checked'; ?>> จดจำฉัน</label>
                <a href="form.forget.php">ลืมรหัสผ่าน?</a> <!-- ลิงก์ไปยังหน้ารีเซ็ตรหัสผ่าน -->
            </div>
            <button type="submit" name="submit" class="btn btn-primary">เข้าสู่ระบบ</button> <!-- ปุ่มเข้าสู่ระบบ -->
            <div class="text-center">
                <p><a href="form.register.php" class="register-link">ลงทะเบียน</a></p> <!-- ลิงก์ไปยังหน้าลงทะเบียน -->
            </div>
        </form>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
