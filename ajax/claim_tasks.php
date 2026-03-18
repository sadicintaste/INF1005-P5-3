<?php
session_start();
include '../inc/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];
$today = date('Y-m-d');

try {
    $conn = DBConnect::connect();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $task = $_POST['task_id'] ?? '';

        // 2. CHECK: Verify if the task has already been completed today
        $checkStmt = $conn->prepare("SELECT task_id FROM Tasks WHERE user_id = ? AND task_identifier = ? AND completed_at = ?");
        $checkStmt->bind_param("iss", $userId, $task, $today);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
    }

    // If a record exists, the task has already been completed today
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Task already completed today!']);
        exit;
    }

    $pointsTable = ['share_social' => 20, 'login' => 10, 'visit_shop' => 15, 'play_game' => 25];

    // 3. VALIDATE: Check if the task is valid and get points to add
    if (array_key_exists($task, $pointsTable)) {
        $pointsToAdd = $pointsTable[$task];

        // 4. UPDATE: Add points to the user's total and log the task completion
        $updateStmt = $conn->prepare("UPDATE User SET points = points + ? WHERE user_id = ?");
        $updateStmt->bind_param("ii", $pointsToAdd, $userId);

        // Log the task completion
        $logStmt = $conn->prepare("INSERT INTO Tasks (user_id, task_identifier, completed_at) VALUES (?, ?, ?)");
        $logStmt->bind_param("iss", $userId, $task, $today);

        // 5. RESPOND: Return success or error message based on the database operations
        if ($updateStmt->execute() && $logStmt->execute()) {
            echo json_encode(['success' => true, 'new_points' => $pointsToAdd]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error during update.']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
