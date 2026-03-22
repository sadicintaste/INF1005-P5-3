<?php
session_start();
include "inc/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: account_settings.php");
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];
$password = $_POST['password'];
$conn = null;

try {
    $conn = DBConnect::connect();

    $stmt = $conn->prepare("SELECT password_hash FROM User WHERE user_id = ?");
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['delete_error'] = "Incorrect password. Account not deleted.";
        header("Location: account_delete.php");
        exit();
    }

    $conn->begin_transaction();

    $stmt = $conn->prepare("DELETE FROM User_Inventory WHERE user_id = ?");
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM User WHERE user_id = ?");
    if (!$stmt) throw new Exception($conn->error);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $conn->commit();

    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
} catch (Exception $e) {
    if ($conn && $conn->in_transaction) {
        $conn->rollback();
    }
    $_SESSION['delete_error'] = "An error occurred during account deletion.";
    header("Location: account_delete.php");
    exit();
} finally {
    DBConnect::close();
}
