<!DOCTYPE html>
<html lang="en">

<!-- head -->
<?php
session_start();
// Check if the user is logged in
$isIn = isset($_SESSION['user_id']);
$user_id = $_SESSION['user_id'];

if (!$isIn || False) {
    // Redirect to login if they try to access the shop while logged out
    header("Location: signin.php");
    exit();
}

include "inc/head.inc.php";
// adjust based on your directory
require_once __DIR__ . '/vendor/autoload.php';

use TCGdex\TCGdex;
use TCGdex\Query;

$tcgdex = new TCGdex("en");

include_once "inc/db_connect.php";
$user_points = 5000; // Default for local testing without DB
try {
    $conn = DBConnect::connect();
    $stmt = $conn->prepare("SELECT points FROM User WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_points = $row['points'];
    }
    $stmt->close();
} catch (Exception $e) {
    // Handle error if needed
}
?>

<script>
    const CURRENT_USER_ID = "<?php echo $user_id; ?>";
    const today = new Date().toISOString().split('T')[0]; // Format: YYYY-MM-DD
    const shopKey = `visitedShop_user_${CURRENT_USER_ID}`;

    // Store an object containing the status and the date
    const data = {
        status: 'true',
        date: today
    };
    sessionStorage.setItem(shopKey, JSON.stringify(data));
</script>

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
                            <input type="text" name="search" class="form-control" placeholder="Search for card name..."
                                aria-label="Card name"
                                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
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

        <!-- points display section -->
        <section class="py-3 bg-dark">
            <div class="container text-center">
                <h4 class="text-warning mb-0">Total Points: <span
                        id="user-points-display"><?php echo htmlspecialchars($user_points); ?></span></h4>
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
                    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
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
                    function get_deterministic_quality($card_id)
                    {
                        $qualities = ['Common', 'Rare', 'Epic', 'Legendary'];
                        $hash = crc32($card_id);
                        return $qualities[$hash % 4];
                    }

                    $filtered_cards = [];
                    foreach ($filtered_cards_api as $api_card) {
                        // Skip cards without images
                        if (empty($api_card->image))
                            continue;

                        $q = get_deterministic_quality($api_card->id);

                        // Filter by selected quality checkboxes
                        if (!in_array(strtolower($q), $quality_filters))
                            continue;

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

                    foreach ($filtered_cards as $c) {
                        $quality_color = '';
                        switch (strtolower($c['quality'])) {
                            case 'common':
                                $quality_color = '#b0bec5';
                                break; // Gray
                            case 'rare':
                                $quality_color = '#29b6f6';
                                break; // Blue
                            case 'epic':
                                $quality_color = '#ab47bc';
                                break; // Purple
                            case 'legendary':
                                $quality_color = '#ffca28';
                                break; // Gold
                        }
                    ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="card bg-dark text-light h-100 position-relative p-2"
                                style="border: 4px solid <?php echo $quality_color; ?>; box-shadow: 0 4px 8px rgba(0,0,0,0.5), 0 0 15px <?php echo $quality_color; ?>60; transition: transform 0.2s, box-shadow 0.2s;"
                                onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 8px 16px rgba(0,0,0,0.7), 0 0 35px 5px <?php echo $quality_color; ?>';"
                                onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.5), 0 0 15px <?php echo $quality_color; ?>60';">
                                <img src="<?php echo $c["image"]; ?>" class="card-img-top w-100" alt="Card Image">
                                <div class="card-body d-flex flex-column text-center p-3">
                                    <h5 class="card-title fw-bold mb-2"><?php echo $c["name"]; ?></h5>
                                    <div class="mb-2">
                                        <span class="badge rounded-pill"
                                            style="background-color: <?php echo $quality_color; ?>; color: #111; font-weight: 800; letter-spacing: 1px; padding: 0.5em 1em; box-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                                            <?php echo strtoupper($c["quality"]); ?>
                                        </span>
                                    </div>
                                    <p class="card-text text-warning fw-bold fs-5 mb-3">
                                        <?php echo $c["cost"]; ?>
                                    </p>
                                    <div
                                        class="mt-auto d-flex align-items-center justify-content-between pt-3 border-top border-secondary">
                                        <div class="d-flex align-items-center bg-secondary rounded px-1">
                                            <button class="btn btn-sm text-light fw-bold px-2 py-1"
                                                onclick="decrement('<?php echo $c['id']; ?>')">-</button>
                                            <span id="quantity-<?php echo $c['id']; ?>"
                                                class="mx-2 text-light fw-bold">1</span>
                                            <button class="btn btn-sm text-light fw-bold px-2 py-1"
                                                onclick="increment('<?php echo $c['id']; ?>')">+</button>
                                        </div>
                                        <button class="btn btn-success fw-bold px-3 py-1"
                                            onclick="buyCard('<?php echo $c['id']; ?>', this, <?php echo $user_id; ?>)">Buy</button>
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
                            <a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page - 1; ?>"
                                class="btn btn-outline-primary">Previous</a>
                        <?php endif; ?>

                        <?php if (!empty($filtered_cards) && count($filtered_cards_api) == $limit): ?>
                            <a href="?search=<?php echo urlencode($search_term); ?>&page=<?php echo $page + 1; ?>"
                                class="btn btn-outline-primary">Next</a>
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

        function buyCard(cardId, btn, userId) {

            const qElem = document.getElementById('quantity-' + cardId);
            let qty = 1;
            let baseValue = 0.5000000000000000;
            if (qElem) {
                qty = parseInt(qElem.textContent, 10);
                if (isNaN(qty) || qty < 1) qty = 1;
            }

            // Optional: Provide immediate feedback
            const originalText = btn.textContent;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            btn.disabled = true;

            fetch('process_buy.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        card_id: cardId,
                        quantity: qty,
                        baseValue: baseValue
                    })
                })
                .then(response => response.json())
                .then(data => {
                    btn.textContent = originalText;
                    btn.disabled = false;

                    if (data.success) {
                        alert(data.message + '\nNew Points Balance: ' + data.new_points);
                        const pointsDisplay = document.getElementById('user-points-display');
                        if (pointsDisplay) {
                            pointsDisplay.textContent = data.new_points;
                        }
                    } else {
                        alert('Purchase Failed: ' + data.message);
                    }
                })
                .catch(error => {
                    btn.textContent = originalText;
                    btn.disabled = false;
                    alert('An error occurred during purchase.');
                    console.error(error);
                });
        }
    </script>

</body>

</html>