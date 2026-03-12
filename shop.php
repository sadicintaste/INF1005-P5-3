<!DOCTYPE html>
<html lang="en">

<!-- head -->
<?php
include "inc/head.inc.php";
require_once 'C:/Users/kenneth/vendor/autoload.php';
use TCGdex\TCGdex;
use TCGdex\Query;

$tcgdex = new TCGdex("en");
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
        <!-- search/filter section -->
        <section class="py-4 bg-secondary">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <form class="d-flex flex-column flex-lg-row align-items-center gap-3" method="get">
                            <input type="text" name="search" class="form-control" placeholder="Search for card name..." aria-label="Card name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <div class="d-flex flex-column flex-lg-row gap-3">
                                <?php
                                $qualities = ['common', 'rare', 'epic', 'legendary'];
                                $selected_qualities = isset($_GET['quality']) ? $_GET['quality'] : $qualities;
                                foreach ($qualities as $q) {
                                    $checked = in_array($q, $selected_qualities) ? 'checked' : '';
                                    $label = ucfirst($q);
                                    echo "<div class='form-check'>
                                        <input class='form-check-input' type='checkbox' name='quality[]' id='$q' value='$q' $checked>
                                        <label class='form-check-label' for='$q'>
                                            $label
                                        </label>
                                    </div>";
                                }
                                ?>
                            </div>
                            <button class="btn btn-primary" type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- shop cards section -->
        <section class="py-5">
            <div class="container">
                <div class="row g-4" style="overflow-y: auto; max-height: 70vh;">
                
                <?php
                // Get search parameters
                $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
                $qualities = ['common', 'rare', 'epic', 'legendary'];
                $quality_filters = isset($_GET['quality']) ? $_GET['quality'] : $qualities;
                $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                $limit = 12; // Cards per page
                
                // Build the TCGdex Query
                $query = Query::create();
                
                if (!empty($search_term)) {
                    $query->contains('name', $search_term);
                }
                
                // Fetch the paginated results using Query Builder
                $query->paginate($page, $limit);

                try {
                    $filtered_cards_api = $tcgdex->card->list($query);
                } catch (Exception $e) {
                    $filtered_cards_api = []; // Fallback on error
                }
                
                // Quality mapping helpers
                $quality_costs = ['common' => 10, 'rare' => 25, 'epic' => 50, 'legendary' => 100];
                
                // To avoid sending 12 individual API requests for detailed card rarity data to assign custom "Quality", 
                // we will assign a random quality for display purposes based on its string ID length as a deterministic pseudo-random.
                // In a production app with a database, you would sync TCGdex rarity to your own qualities.
                function get_deterministic_quality($card_id) {
                    $qualities = ['Common', 'Rare', 'Epic', 'Legendary'];
                    $hash = crc32($card_id);
                    return $qualities[$hash % 4];
                }

                $filtered_cards = [];
                foreach ($filtered_cards_api as $api_card) {
                    // Skip cards without images
                    if (empty($api_card->image)) continue;

                    $q = get_deterministic_quality($api_card->id);

                    // Filter by selected quality checkboxes
                    if (!in_array(strtolower($q), $quality_filters)) continue;

                    $filtered_cards[] = [
                        'id' => $api_card->id,
                        'name' => $api_card->name,
                        'quality' => $q,
                        'image' => $api_card->image . '/low.webp',
                        'cost' => $quality_costs[strtolower($q)]
                    ];
                }

                if (empty($filtered_cards)) {
                    echo "<p class='text-center mt-5'>No cards found matching your criteria.</p>";
                }

                foreach($filtered_cards as $c) { ?>
                    <div class="col-md-4">
                        <div class="card bg-dark text-light h-100">
                            <img src="<?php echo $c["image"]; ?>" class="card-img-top" alt="Card Image" style="height: 200px; object-fit: contain;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $c["name"]; ?></h5>
                                <p class="card-text">Quality: <?php echo $c["quality"]; ?></p>
                                <p class="card-text">Cost: $<?php echo number_format($c["cost"], 2); ?></p>
                                <div class="mt-auto d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <button class="btn btn-outline-light btn-sm" onclick="decrement('<?php echo $c['id']; ?>')">-</button>
                                        <span id="quantity-<?php echo $c['id']; ?>" class="mx-2 text-light">1</span>
                                        <button class="btn btn-outline-light btn-sm" onclick="increment('<?php echo $c['id']; ?>')">+</button>
                                    </div>
                                    <button class="btn btn-success">Buy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                </div>

                <!-- Pagination Controls -->
                <div class="row mt-4">
                    <div class="col-12 d-flex justify-content-center gap-2">
                        <?php if ($page > 1): ?>
                            <a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page - 1; ?>" class="btn btn-outline-primary">Previous</a>
                        <?php endif; ?>
                        
                        <?php if (!empty($filtered_cards) && count($filtered_cards_api) == $limit): ?>
                            <a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page + 1; ?>" class="btn btn-outline-primary">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function increment(cardId) {
            const qElem = document.getElementById('quantity-' + cardId);
            if (!qElem) return;
            let qty = parseInt(qElem.textContent, 10);
            if (isNaN(qty)) qty = 0;
            qty += 1;
            qElem.textContent = qty;
        }

        function decrement(cardId) {
            const qElem = document.getElementById('quantity-' + cardId);
            if (!qElem) return;
            let qty = parseInt(qElem.textContent, 10);
            if (isNaN(qty)) qty = 1;
            qty = Math.max(1, qty - 1);
            qElem.textContent = qty;
        }
    </script>

</body>

</html>