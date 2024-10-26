<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* สีพื้นหลัง */
            display: flex;
            justify-content: center; /* จัดกลางในแนวนอน */
            align-items: flex-start; /* จัดให้เริ่มต้นจากด้านบน */
            height: 100vh; /* ความสูงเต็มหน้าจอ */
            margin: 0; /* ไม่มีระยะห่างรอบๆ */
            padding-top: 50px; /* ระยะห่างจากด้านบน */
        }
        .register-container {
            width: 400px; /* กว้างของฟอร์ม */
            background-color: #ffffff; /* พื้นหลังของฟอร์ม */
            border-radius: 10px; /* มุมมนของฟอร์ม */
            padding: 30px; /* ระยะห่างภายในฟอร์ม */
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2); /* เงาเพื่อให้ดูมีมิติ */
        }
        .form-title {
            margin-bottom: 20px; /* ระยะห่างด้านล่างหัวข้อ */
        }
        .btn {
            width: 100%; /* ปุ่มให้เต็มความกว้าง */
            margin-top: 10px; /* ระยะห่างด้านบน */
        }
        .login-link {
            margin-top: 15px; /* ระยะห่างด้านบนสำหรับลิงก์ */
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="text-center form-title">สมัครสมาชิก</h1>
        <form action="process-register.php" method="POST">
            <div class="form-group">
                <label for="username_account">ชื่อผู้ใช้</label>
                <input name="username_account" type="text" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password_account1">รหัสผ่าน</label>
                <input name="password_account1" type="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password_account2">ยืนยันรหัสผ่าน</label>
                <input name="password_account2" type="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="firstname_account">ชื่อ</label>
                <input name="firstname_account" type="text" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="lastname_account">นามสกุล</label>
                <input name="lastname_account" type="text" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email_account">อีเมล</label>
                <input name="email_account" type="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="phone_account">เบอร์โทรศัพท์</label>
                <input name="phone_account" id="phone_account" type="text" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            <p class="text-center"><a href="home1.php" class="login-link">มีบัญชีอยู่แล้ว?</a></p> <!-- ลิงก์ไปยังหน้าล็อกอิน -->
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
