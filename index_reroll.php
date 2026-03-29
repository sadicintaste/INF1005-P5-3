<?php
session_start();
require_once "inc/db_connect.php";
require_once __DIR__ . '/vendor/autoload.php';

use TCGdex\TCGdex;

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Sign in to reroll.']);
    exit;
}

$sessionKey = "user_" . $userId;
$cost = 15;

try {
    $conn = DBConnect::connect();
    
    // check if enough
    $user = DBConnect::getUserDetails($userId);
    if (!$user || $user['points'] < $cost) {
        echo json_encode(['success' => false, 'message' => "Insufficient points. You need {$cost} points."]);
        exit;
    }

    // minus credit score
    $newPoints = $user['points'] - $cost;
    $stmt = $conn->prepare("UPDATE User SET points = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $newPoints, $userId);
    $stmt->execute();

    $tcgdex = new TCGdex("en");
    $cards = $tcgdex->card->list();
    $randomKeys = array_rand($cards, 5);
    $newDisplay = [];

    foreach ($randomKeys as $key) {
        $cardData = $tcgdex->card->get($cards[$key]->id);
        $newDisplay[] = [
            'id' => $cardData->id,
            'image' => $cardData->image,
            'name' => $cardData->name,
            'flipped' => false
        ];
    }

    $_SESSION[$sessionKey] = $newDisplay;
    $_SESSION[$sessionKey . "_saved"] = false;

    echo json_encode(['success' => true, 'newPoints' => $newPoints]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
