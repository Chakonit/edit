<?php
session_start();
$open_connect = 1;
require __DIR__ . '/connect.php';

if (!isset($_POST["token"], $_POST["password_account"], $_POST["password_confirmation"])) {
    die("All fields are required.");
}

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

$sql = "SELECT * FROM account 
        WHERE reset_token_hash = ?";
$stmt = $connect->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $connect->error);
}

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired");
}

if (strlen($_POST["password_account"]) < 8) {
    die("Password must be at least 8 characters");
}

if (!preg_match("/[a-z]/i", $_POST["password_account"])) {
    die("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password_account"])) {
    die("Password must contain at least one number");
}

if ($_POST["password_account"] !== $_POST["password_confirmation"]) {
    die("Passwords must match");
}

// สร้าง salt ใหม่
$salt = bin2hex(random_bytes(16));

// รวมรหัสผ่านใหม่กับ salt ใหม่
$password_with_salt = $_POST["password_account"] . $salt;

// เข้ารหัสรหัสผ่านที่รวม salt ด้วย Argon2
$password_hash = password_hash($password_with_salt, PASSWORD_ARGON2ID);

$sql = "UPDATE account
        SET password_account = ?,
            salt_account = ?,
            reset_token_hash = NULL,
            reset_token_expires_at = NULL
        WHERE id_account = ?";
$stmt = $connect->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $connect->error);
}

if (!$stmt->bind_param("ssi", $password_hash, $salt, $user["id_account"])) {
    die("Bind_param failed: " . $stmt->error);
}

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

if ($stmt->affected_rows > 0) {
    echo "Password updated. You can now log in.";
} else {
    echo "Failed to update password.";
}
?>
