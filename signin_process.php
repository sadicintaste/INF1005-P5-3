<?php
session_start();
require_once './inc/db_connect.php'; 

function redirectWithError($errorCode, $email)
{
    $_SESSION['signin_old_input'] = [
        'email' => $email
    ];

    header('Location: signin.php?error=' . urlencode($errorCode));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signin.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    redirectWithError('missing_fields', $email);
}

try {
    $db = DBConnect::connect();

    $stmt = $db->prepare("SELECT user_id, username, password_hash FROM User WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            unset($_SESSION['signin_old_input']);

            header("Location: index.php");
            exit();
        }
    }

    redirectWithError('invalid', $email);
} catch (Exception $e) {
    redirectWithError('server_error', $email);
}
?>