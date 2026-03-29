<?php
session_start();
require_once "inc/db_connect.php";

$data = json_decode(file_get_contents('php://input'), true);
$userId = $_SESSION['user_id'] ?? 'guest';
$sessionKey = "user_" . $userId;

if (isset($data['index']) && isset($_SESSION[$sessionKey][$data['index']])) {

    if (!$_SESSION[$sessionKey][$data['index']]['flipped'] && $userId) {
        try {
            $conn = DBConnect::connect();
            $stmt = $conn->prepare("UPDATE User SET points = points + 1 WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
             $_SESSION['points'] = ($_SESSION['points'] ?? 0) + 5;
        } catch (Exception $e) {
        }
    }

    $_SESSION[$sessionKey][$data['index']]['flipped'] = true;

   echo json_encode([
        'success' => true,
        'newPoints' => $_SESSION['points'] ?? 0
    ]);
}
