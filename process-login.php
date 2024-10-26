<?php
session_start();

$open_connect = 1;
require('connect.php');

$error_message = '';

if(isset($_POST['username_account']) && isset($_POST['password_account'])) {
    $username_account = htmlspecialchars(mysqli_real_escape_string($connect, $_POST['username_account']));
    $password_account = htmlspecialchars(mysqli_real_escape_string($connect, $_POST['password_account']));

    $query_check_account = "SELECT * FROM account WHERE username_account = '$username_account'";
    $call_back_check_account = mysqli_query($connect, $query_check_account);

    if(mysqli_num_rows($call_back_check_account) == 1) {
        $result_check_account = mysqli_fetch_assoc($call_back_check_account);
        $hash = $result_check_account['password_account'];

        $password_account = $password_account . $result_check_account['salt_account'];

        if(password_verify($password_account, $hash)) {
            $_SESSION['username_account'] = $result_check_account['username_account'];
            $_SESSION['role_account'] = $result_check_account['role_account'];

            if (isset($_POST['remember'])) {
                setcookie('user_login', $username_account, time() + (10 * 365 * 24 * 60 * 60));
                setcookie('user_password', $password_account, time() + (10 * 365 * 24 * 60 * 60));
            } else {
                if (isset($_COOKIE['user_login'])) {
                    setcookie('user_login', '', time() - 3600);
                }
                if (isset($_COOKIE['user_password'])) {
                    setcookie('user_password', '', time() - 3600);
                }
            }

            header('Location: index1.php');
            exit();
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "Username not found.";
    }
} else {
    $error_message = "Username or password not set.";
}

header('Location: home1.php?error=' . urlencode($error_message));
exit();
?>
