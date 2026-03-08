<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['index']) && isset($_SESSION['indexDisplay'][$data['index']])) {
    $_SESSION['indexDisplay'][$data['index']]['flipped'] = true;
    echo json_encode(['success' => true]);
}
?>