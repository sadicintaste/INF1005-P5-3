<?php
session_start();
require_once './inc/db_connect.php';

function isValidPassword($password)
{
    if (strlen($password) >= 15) {
        return true;
    }

    return strlen($password) >= 8
        && preg_match('/[0-9]/', $password)
        && preg_match('/[a-z]/', $password);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit();
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($username === '' || $email === '' || $password === '' || $confirmPassword === '') {
    header('Location: signup.php?error=missing_fields');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: signup.php?error=invalid_email');
    exit();
}

if ($password !== $confirmPassword) {
    header('Location: signup.php?error=password_mismatch');
    exit();
}

if (!isValidPassword($password)) {
    header('Location: signup.php?error=weak_password');
    exit();
}

try {
    $db = DBConnect::connect();

    $checkStmt = $db->prepare('SELECT user_id FROM User WHERE email = ? OR username = ?');
    $checkStmt->bind_param('ss', $email, $username);
    $checkStmt->execute();
    $existingUser = $checkStmt->get_result();

    if ($existingUser && $existingUser->num_rows > 0) {
        $checkStmt->close();
        header('Location: signup.php?error=account_exists');
        exit();
    }
    $checkStmt->close();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insertStmt = $db->prepare('INSERT INTO User (username, email, password_hash, points) VALUES (?, ?, ?, 0)');
    $insertStmt->bind_param('sss', $username, $email, $passwordHash);

    if (!$insertStmt->execute()) {
        $insertStmt->close();
        header('Location: signup.php?error=insert_failed');
        exit();
    }

    $newUserId = $insertStmt->insert_id;
    $insertStmt->close();

    $_SESSION['user_id'] = $newUserId;
    $_SESSION['username'] = $username;

    header('Location: index.php');
    exit();
} catch (Exception $e) {
    header('Location: signup.php?error=server_error');
    exit();
}
?>
