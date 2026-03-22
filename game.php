<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
$userId = $_SESSION['user_id'];
$style = "game.css";
include 'inc/head.inc.php';

require_once __DIR__ . '/vendor/autoload.php';

use TCGdex\TCGdex;
use TCGdex\Query;

$tcgdex = new TCGdex("en");

// Fetch 6 random cards to create 12 matching pairs
try {
    // We fetch a larger set and pick 6 random ones to ensure variety
    $query = Query::create()->paginate(1, 50);
    $allCards = $tcgdex->card->list($query);

    // Filter out cards that don't have images
    $validCards = array_filter($allCards, function ($c) {
        return !empty($c->image);
    });

    // Shuffle and pick the first 6
    shuffle($validCards);
    $selectedCards = array_slice($validCards, 0, 6);

    // Map them to their low-res image URLs
    $gameImages = array_map(function ($c) {
        return $c->image . '/low.webp';
    }, $selectedCards);
} catch (Exception $e) {
    // Fallback images if API fails
    $gameImages = ['https://images.pokemontcg.io/base1/1_low.webp'];
}
?>

<!DOCTYPE html>
<html lang="en">

<body class="bg-dark text-light">
    <?php include 'inc/nav.inc.php'; ?>

    <div class="container text-center mt-5">
        <h1>Memory Match</h1>
        <p>Match all pairs to complete your daily task!</p>
        <div class="game-grid" id="game-grid"></div>
    </div>

    <script>
        // Use the $userId variable you defined at the top
        const CURRENT_USER_ID = "<?php echo $userId; ?>";
        const cardImages = <?php echo json_encode($gameImages); ?>;
    </script>
    <script src="js/game.js"></script>
</body>

</html>