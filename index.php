<!-- tcg -->
<?php

// adjust based on your directory
require_once '../PHP/vendor/autoload.php';

// suppress deprecated warnings
error_reporting(E_ALL & ~E_DEPRECATED);

use TCGdex\TCGdex;

$tcgdex = new TCGdex("en");

$cardsResponse = $tcgdex->card->list();
$randomKeys = array_rand($cardsResponse, 5);
$displayCards = [];

foreach ($randomKeys as $key) {
    $displayCards[] = $tcgdex->card->get($cardsResponse[$key]->id);
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

    <main>

        <!-- cards -->
        <section id="index-gacha" class="d-flex align-items-center justify-content-center min-vh-100">
            <div class="container">
                <div class="row justify-content-center gap-3">

                    <?php foreach ($displayCards as $card): ?>
                        <div class="index-card" onclick="indexFlip(this)">
                            <div class="index-card-inner">
                                <div class="index-card-front">
                                    <img src="assets/img/pokemon-card-back.png" alt="" class="img-fluid">
                                </div>

                                <div class="index-card-back">
                                    <img src="<?php echo $card->image . '/high.png'; ?>" alt="<?php echo $card->name; ?>" class="img-fluid" style="border-radius: 12px;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- register -->
                <div id="index-register" class="text-center mt-4 d-none opacity-0">
                    <p class="text-white mb-3">Love your deck?</p>
                    <a href="signup.php">
                        <button class="btn btn-outline-light btn-lg">Register to Save</button>
                    </a>
                </div>

            </div>

        </section>
    </main>
</body>

</html>