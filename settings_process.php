<?php
session_start();
include "inc/db_connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: signin.php");
        exit();
    }

    $user_id = (int)$_SESSION['user_id'];
    $new_username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $conn = DBConnect::connect();

        $stmt = $conn->prepare("SELECT password_hash FROM User WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['update_error'] = "Incorrect password. Please try again.";
            header("Location: account_settings.php");
            exit();
        }

        $stmt = $conn->prepare("SELECT user_id FROM User WHERE (username = ?) AND user_id != ?");
        $stmt->bind_param("si", $new_username, $user_id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['update_error'] = "Username is already in use by another account.";
            header("Location: account_settings.php");
            exit();
        }

        $stmt = $conn->prepare("UPDATE User SET username = ? WHERE user_id = ?");
        $stmt->bind_param("si", $new_username, $user_id);

        if ($stmt->execute()) {
            $_SESSION['update_success'] = "Your account details have been updated successfully.";
        } else {
            throw new Exception("Failed to update account details.");
        }
    } catch (Exception $e) {
        $_SESSION['update_error'] = "An error occurred: " . $e->getMessage();
    } finally {
        DBConnect::close();
        header("Location: account_settings.php");
        exit();
    }
} else {
    header("Location: account_settings.php");
    exit();
}
?>