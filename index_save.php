<?php
session_start();
require_once "inc/db_connect.php"; 

$userId = $_SESSION['user_id'] ?? null;
$sessionKey = "user_" . $userId;

if (!$userId) {
    header("Location: signin.php");
    exit();
}

try {
    $conn = DBConnect::connect();
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

if (isset($_SESSION[$sessionKey . "_saved"]) && $_SESSION[$sessionKey . "_saved"] === true) {
    header("Location: index.php?info=already_saved");
    exit();
}

if (isset($_SESSION[$sessionKey]) && !empty($_SESSION[$sessionKey])) {
    
    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO User_Inventory (user_id, card_id, quality_value) VALUES (?, ?, ?)");

        foreach ($_SESSION[$sessionKey] as $card) {
            $quality = number_format(mt_rand() / mt_getrandmax(), 16, '.', '');
            
            $stmt->bind_param("isd", $userId, $card['id'], $quality);
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        }

        $conn->commit();
        
        $_SESSION[$sessionKey . "_saved"] = true; 

        header("Location: account.php?success=is_saved");
        
    } catch (Exception $e) {
        // 6. MySQLi Rollback
        $conn->rollback();
        die("Error saving to inventory: " . $e->getMessage());
    }
} else {
    header("Location: index.php?error=missing_cards");
}
exit();