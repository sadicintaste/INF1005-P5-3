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

                    <div class="index-card" onclick="indexFlip(this)">
                        <div class="index-card-inner">
                            <div class="index-card-front"></div>
                            <div class="index-card-back bg-primary"></div>
                        </div>
                    </div>

                    <div class="index-card" onclick="indexFlip(this)">
                        <div class="index-card-inner">
                            <div class="index-card-front"></div>
                            <div class="index-card-back bg-danger"></div>
                        </div>
                    </div>

                    <div class="index-card" onclick="indexFlip(this)">
                        <div class="index-card-inner">
                            <div class="index-card-front"></div>
                            <div class="index-card-back bg-success"></div>
                        </div>
                    </div>

                    <div class="index-card" onclick="indexFlip(this)">
                        <div class="index-card-inner">
                            <div class="index-card-front"></div>
                            <div class="index-card-back bg-warning"></div>
                        </div>
                    </div>

                    <div class="index-card" onclick="indexFlip(this)">
                        <div class="index-card-inner">
                            <div class="index-card-front"></div>
                            <div class="index-card-back bg-info"></div>
                        </div>
                    </div>
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