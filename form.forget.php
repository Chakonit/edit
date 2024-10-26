<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลืมรหัสผ่าน</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa; /* สีพื้นหลังให้ความรู้สึกเบา */
            display: flex;
            justify-content: center; /* จัดกลางในแนวนอน */
            align-items: center; /* จัดกลางในแนวตั้ง */
            height: 100vh; /* ความสูงเต็มหน้าจอ */
        }
        .reset-container {
            width: 400px; /* กว้างของฟอร์ม */
            background-color: #ffffff; /* พื้นหลังของฟอร์มเป็นสีขาว */
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
            margin-top: 15px; /* ระยะห่างด้านบนสำหรับลิงก์กลับ */
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h1 class="text-center form-title">ลืมรหัสผ่าน</h1>
        <form method="POST" action="sent.passwordreset.php">
            <div class="form-group">
                <label for="email_account">Email</label>
                <input type="email" name="email_account" id="email_account" class="form-control" placeholder="กรุณากรอกอีเมลที่ลงทะเบียนไว้" required>
            </div>
            <button type="submit" class="btn btn-primary">ส่ง</button> <!-- ปุ่มส่ง -->
            <p class="text-center"><a href="home1.php" class="login-link">ยกเลิก</a></p> <!-- ลิงก์กลับ -->
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="mailer.php"></script>
</body>
</html>
