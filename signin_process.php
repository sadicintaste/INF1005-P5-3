<?php
session_start();
require_once './inc/db_connect.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

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
                
                header("Location: index.php");
                exit();
            }
        }
        
        header("Location: signin.php?error=invalid");
        exit();

    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
?>