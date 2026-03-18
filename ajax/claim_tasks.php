<?php
session_start();
header('Content-Type: application/json'); // Tells JS to expect JSON
require_once '../inc/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit;
}

$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

try {
    $conn = DBConnect::connect();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task = $_POST['task_id'] ?? '';

        // Verify if already completed
        $checkStmt = $conn->prepare("SELECT task_id FROM Tasks WHERE user_id = ? AND task_identifier = ? AND completed_at = ?");
        $checkStmt->bind_param("iss", $userId, $task, $today);
        $checkStmt->execute();
        
        if ($checkStmt->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Task already completed today!']);
            exit;
        }

        $pointsTable = ['share_social' => 20, 'login' => 10, 'visit_shop' => 15, 'play_game' => 25];

        if (array_key_exists($task, $pointsTable)) {
            $pointsToAdd = $pointsTable[$task];

            // Update the 'User' table (matching your capitalized schema)
            $updateStmt = $conn->prepare("UPDATE User SET points = points + ? WHERE user_id = ?");
            $updateStmt->bind_param("ii", $pointsToAdd, $userId);

            $logStmt = $conn->prepare("INSERT INTO Tasks (user_id, task_identifier, completed_at) VALUES (?, ?, ?)");
            $logStmt->bind_param("iss", $userId, $task, $today);

            if ($updateStmt->execute() && $logStmt->execute()) {
                echo json_encode(['success' => true, 'new_points' => $pointsToAdd]);
            }
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}