<?php
session_start();
$open_connect = 1;
require('connect.php');

$token = $_GET["token"];

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__. "/connect.php";

$sql = "SELECT * FROM account
        WHERE reset_token_hash = ?";

$stmt = $connect->prepare($sql);

$stmt -> bind_param("s",$token_hash);

$stmt -> execute();

$result = $stmt -> get_result();

$user = $result->fetch_assoc();

if($user === null){
    die("token not found");
}

if(strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>

    <form method="POST" action="process-reset-password.php">
        
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

        <label for="password">New Password</label>
        <input type="password" id="password_account" name="password_account">

        <label for="password confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" 
        name="password_confirmation">

        <button>Send</button>
    </form>
</body>
</html>
