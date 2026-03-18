<?php
header('Content-Type: application/json');
include "inc/db_connect.php";

// suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED);

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['card_id']) || !isset($data['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$user_id = 1; // Currently hardcoded to 1 for testing as requested
$card_id = $data['card_id'];
$quantity = max(1, (int)$data['quantity']);

function get_deterministic_quality($card_id) {
    $qualities = ['Common', 'Rare', 'Epic', 'Legendary'];
    $hash = crc32($card_id);
    return $qualities[$hash % 4];
}

$quality_costs = ['common' => 10, 'rare' => 25, 'epic' => 50, 'legendary' => 100];
$quality = get_deterministic_quality($card_id);
$cost_per_card = $quality_costs[strtolower($quality)];
$total_cost = $cost_per_card * $quantity;

try {
    $conn = DBConnect::connect();
    
    // Start transaction
    $conn->begin_transaction();

    // Check points
    $stmt = $conn->prepare("SELECT points FROM User WHERE user_id = ? FOR UPDATE");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user) {
        throw new Exception("User not found.");
    }

    if ($user['points'] < $total_cost) {
        throw new Exception("Insufficient points. You need {$total_cost} points, but only have {$user['points']}.");
    }

    // Deduct points
    $new_points = $user['points'] - $total_cost;
    $stmt = $conn->prepare("UPDATE User SET points = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $new_points, $user_id);
    $stmt->execute();
    $stmt->close();

    // Add to inventory
    // Depending on schema, we might need a specific format for quality_value
    $stmt = $conn->prepare("INSERT INTO User_Inventory (user_id, card_id, quality_value) VALUES (?, ?, ?)");
    for ($i = 0; $i < $quantity; $i++) {
        // use bind_param with 'iss' treating quality_value as a string for now, to support actual quality names
        $stmt->bind_param("iss", $user_id, $card_id, $quality); 
        $stmt->execute();
    }
    $stmt->close();

    $conn->commit();
    DBConnect::close();

    echo json_encode([
        'success' => true, 
        'new_points' => $new_points, 
        'message' => "Successfully purchased {$quantity}x {$quality} card(s)!"
    ]);

} catch (Exception $e) {
    if (isset($conn) && $conn) {
        $conn->rollback();
        DBConnect::close();
    }
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
