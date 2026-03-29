<!-- tcg -->
<?php
session_start();

$userId = $_SESSION['user_id'] ?? 'guest';
$sessionKey = "user_" . $userId;
$isSaved = $_SESSION[$sessionKey . "_saved"] ?? false;

// adjust based on your directory
require_once __DIR__ . '/vendor/autoload.php';
require_once "inc/db_connect.php";

// suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED);
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);

use TCGdex\TCGdex;

$tcgdex = new TCGdex("en");

if (!isset($_SESSION[$sessionKey])) {
    $indexCards = $tcgdex->card->list();
    $indexRandom = array_rand($indexCards, 5);
    $indexDisplay = [];

    foreach ($indexRandom as $key) {
        $cardData = $tcgdex->card->get($indexCards[$key]->id);
        $indexDisplay[] = [
            'id' => $cardData->id,
            'image' => $cardData->image,
            'name' => $cardData->name,
            'flipped' => false
        ];
    }
    $_SESSION[$sessionKey] = $indexDisplay;
}

$indexDisplay = $_SESSION[$sessionKey];
$isIn = isset($_SESSION['user_id']);
$userPoints = 0;

if ($isIn) {
    try {
        $userDetails = DBConnect::getUserDetails($_SESSION['user_id']);
        $userPoints = $userDetails['points'] ?? 0;
    } catch (Exception $e) {
        $userPoints = 0; 
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<!-- head -->
<?php

$style = "index.css";
include "inc/head.inc.php";

?>

<body class="text-light">

    <!-- loader -->
    <?php
    include "inc/loader.inc.php";
    ?>

    <!-- nav -->
    <?php
    include "inc/nav.inc.php";
    ?>

    <?php include "inc/pokemonIcons.inc.php"; ?>

    <main>

        <!-- cards -->
        <section id="index-gacha" class="d-flex align-items-center justify-content-center min-vh-100">
            <div class="container">
                <div class="row justify-content-center gap-3">

                    <?php foreach ($indexDisplay as $index => $card): ?>
                        <div class="index-card <?php echo $card['flipped'] ? 'flipped' : ''; ?>"
                            onclick="indexFlip(this, <?php echo $index; ?>)">
                            <div class="index-card-inner">
                                <div class="index-card-front">
                                    <img src="assets/img/pokemon-card-back.png" alt="" class="img-fluid">
                                </div>
                                <div class="index-card-back">
                                    <img src="<?php echo $card['image'] . '/high.png'; ?>" alt="<?php echo $card['name']; ?>" class="img-fluid" style="border-radius: 12px;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- register -->
                <div id="index-register" class="text-center mt-4 d-none opacity-0">
                    <p class="text-white mb-3">Love your deck?</p>

                    <!-- signed -->
                    <?php if ($isIn): ?>
                        <!-- reroll -->
                        <?php if ($userPoints >= 15): ?>
                        <button id="index-repull-btn" class="btn btn-outline-light btn-lg ms-2" onclick="indexReroll()">
                            Reroll? (-15 Points)
                        </button>
                        <?php endif; ?>

                        <?php if ($isSaved): ?>
                            <a href="account.php">
                                <button class="btn btn-outline-success btn-lg disabled">Collection is Saved!</button>
                            </a>

                            <!-- unsigned -->
                        <?php else: ?>
                            <a href="index_save.php">
                                <button class="btn btn-outline-light btn-lg">Save to Collection</button>
                            </a>
                        <?php endif; ?>


                    <?php else: ?>
                        <a href="signup.php">
                            <button class="btn btn-outline-light btn-lg">Register to Save</button>
                        </a>
                    <?php endif; ?>
                </div>

            </div>

        </section>
    </main>
</body>

</html>