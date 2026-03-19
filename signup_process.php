<?php
session_start();
require_once './inc/db_connect.php';

function redirectWithError($errorCode, $username, $email)
{
    $_SESSION['signup_old_input'] = [
        'username' => $username,
        'email' => $email
    ];

    header('Location: signup.php?error=' . urlencode($errorCode));
    exit();
}

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
    redirectWithError('missing_fields', $username, $email);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithError('invalid_email', $username, $email);
}

if ($password !== $confirmPassword) {
    redirectWithError('password_mismatch', $username, $email);
}

if (!isValidPassword($password)) {
    redirectWithError('weak_password', $username, $email);
}

try {
    $db = DBConnect::connect();

    $checkStmt = $db->prepare('SELECT user_id FROM User WHERE email = ? OR username = ?');
    $checkStmt->bind_param('ss', $email, $username);
    $checkStmt->execute();
    $existingUser = $checkStmt->get_result();

    if ($existingUser && $existingUser->num_rows > 0) {
        $checkStmt->close();
        redirectWithError('account_exists', $username, $email);
    }
    $checkStmt->close();

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insertStmt = $db->prepare('INSERT INTO User (username, email, password_hash, points) VALUES (?, ?, ?, 0)');
    $insertStmt->bind_param('sss', $username, $email, $passwordHash);

    if (!$insertStmt->execute()) {
        $insertStmt->close();
        redirectWithError('insert_failed', $username, $email);
    }

    $newUserId = $insertStmt->insert_id;
    $insertStmt->close();

    $_SESSION['user_id'] = $newUserId;
    $_SESSION['username'] = $username;
    unset($_SESSION['signup_old_input']);

    header('Location: index.php');
    exit();
} catch (Exception $e) {
    redirectWithError('server_error', $username, $email);
}
?>
