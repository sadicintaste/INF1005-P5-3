<!DOCTYPE html>
<html lang="en">


<?php
session_start();
$style = "about.css";
include "inc/head.inc.php";
?>

<body class="text-light">


    <?php
    include "inc/loader.inc.php";
    ?>

    <?php
    include "inc/nav.inc.php";
    ?>

    <?php include "inc/pokemonIcons.inc.php"; ?>

    <main>
        <section class="about-section d-flex align-items-center justify-content-center min-vh-100">
            <div class="container">

            <div class="row justify-content-center mt-5">
                
                <div class="text-center mb-5">
                    <img src="images/mintmint_logo.png" class="mb-3 logo-glow">
                    <h1 class="title-glow">MintMint Supremacy</h1>
                    <p class="subtitle">✨ Ready. Set. Gacha! ✨</p>
                </div>

                <div class="col-md-15">
                    <div class="custom-card story-card">
                        <h4 class="mb-3">📖 Our Story</h4>
                        <p>
                            Welcome to MintMint Supremacy, your ultimate destination for digital card collecting with a rewarding twist. 
                            We blend the thrill of gacha-style pack openings with the satisfaction of earning your collection through daily engagement. 
                            Every player deserves a fair shot at building something unique — whether you're chasing rare cards, managing your points, 
                            or simply enjoying the grind. Join us on a journey where every login brings a new chance to discover something extraordinary.
                        </p>
                    </div>
                </div>

                <div class="row g-4 justify-content-center">

                    <div class="col-md-4">
                        <div class="custom-card">
                            <h4>🎮 What We Do</h4>
                            <p>Open packs, collect cards, and build your ultimate digital collection.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="custom-card">
                            <h4>💎 Our Mission</h4>
                            <p>Make every login feel like pulling a rare card.</p>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="custom-card">
                            <h4>🔥 Why Us</h4>
                            <p>Fair rewards, daily engagement, and pure gacha excitement.</p>
                        </div>
                    </div>

                </div>

                <div class="text-center mt-5">
                    <h3 class="mb-3">🎴 Your Journey</h3>
                    <div class="journey">
                        <span>Login</span>
                        <span>→</span>
                        <span>Open Pack</span>
                        <span>→</span>
                        <span>Get Card</span>
                        <span>→</span>
                        <span>Build Collection</span>
                    </div>
                </div>

                <div class="row text-center mt-5">
                    <div class="col-md-4 stat">
                        <h2>12K+</h2>
                        <p>Trainers</p>
                    </div>
                    <div class="col-md-4 stat">
                        <h2>90K+</h2>
                        <p>Cards Collected</p>
                    </div>
                    <div class="col-md-4 stat">
                        <h2>5K+</h2>
                        <p>Daily Rewards</p>
                    </div>  
                </div>

                <?php 
                if (isset($_SESSION['username'])) {
                    echo '<div class="text-center mt-5">';
                    echo '<a href="account.php" class="cta-btn">View Your Collection</a>';
                    echo '</div>';
                }
                else {
                    echo '<div class="text-center mt-5">';
                    echo '<a href="signup.php" class="cta-btn">Start Your Collection</a>';
                    echo '</div>';
                }
                ?>

            </div>
        </section>
    </main>

</body>

</html>
