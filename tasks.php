<?php
session_start();
include 'inc/db_connect.php';

$isIn = isset($_SESSION['user_id']);

if (!$isIn) {
    header("Location: signin.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['username'];


try {
    $conn = DBConnect::connect();
    $completedToday = [];
    $today = date('Y-m-d');

    $stmt = $conn->prepare("SELECT task_identifier FROM Tasks WHERE user_id = ? AND completed_at = ?");
    $stmt->bind_param("is", $userId, $today);
    $stmt->execute();

    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $completedToday[] = $row['task_identifier'];
    }
    $stmt->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">
<?php include 'inc/head.inc.php'; ?>

<body>
    <?php include 'inc/nav.inc.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4" style="color: white;">Daily Tasks</h1>
        <p class="text-center mb-5" style="color: white;">Complete these daily tasks to earn in-game points!</p>

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Log in to the website</h5>
                            <p class="mb-1">Sign in to your account today.</p>
                            <small class="text-muted">+10 points</small>
                        </div>
                        <button id="btn-login"
                            class="btn <?php echo in_array('login', $completedToday) ? 'btn-secondary' : 'btn-success'; ?>"
                            <?php echo in_array('login', $completedToday) ? 'disabled' : ''; ?>
                            onclick="claimTask('login')">
                            <i class="fa-solid <?php echo in_array('login', $completedToday) ? 'fa-user-check' : 'fa-right-to-bracket'; ?>"></i>
                            <?php echo in_array('login', $completedToday) ? 'Claimed' : 'Complete'; ?>
                        </button>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Visit the shop</h5>
                            <p class="mb-1">Browse the point shop for rewards.</p>
                            <small class="text-muted">+15 points</small>
                        </div>
                        <button id="btn-visit-shop"
                            class="btn <?php echo in_array('visit_shop', $completedToday) ? 'btn-secondary' : 'btn-success'; ?>"
                            <?php echo in_array('visit_shop', $completedToday) ? 'disabled' : ''; ?>
                            onclick="claimTask('visit_shop')">
                            <i class="fa-solid <?php echo in_array('visit_shop', $completedToday) ? 'fa-bag-shopping' : 'fa-cart-plus'; ?>"></i>
                            <?php echo in_array('visit_shop', $completedToday) ? 'Claimed' : 'Complete'; ?>
                        </button>
                    </div>

                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Spread the word about MintMint!</h5>
                            <p class="mb-1">Share our link with your friends!</p>

                            <button type="button" class="btn btn-sm btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#shareModal">
                                Get Share Link
                            </button>
                            <br>
                            <small class="text-muted">+20 points</small>
                        </div>
                        <button id="btn-share-claim"
                            class="btn <?php echo in_array('share_social', $completedToday) ? 'btn-secondary' : 'btn-success'; ?>"
                            <?php echo in_array('share_social', $completedToday) ? 'disabled' : ''; ?>
                            onclick="claimTask('share_social')">
                            <?php if (in_array('share_social', $completedToday)): ?>
                                <i class="fa-solid fa-circle-check fa-bounce" style="--fa-animation-iteration-count: 2;"></i> Claimed
                            <?php else: ?>
                                <i class="fa-solid fa-right-to-bracket"></i> Complete
                            <?php endif; ?>
                        </button>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Play a game</h5>
                            <p class="mb-1">Complete a mini-game on the site.</p>

                            <?php if (!in_array('play_game', $completedToday)): ?>
                                <a href="game.php" class="btn btn-sm btn-outline-info mb-2">
                                    <i class="fa-solid fa-gamepad"></i> Go to Game
                                </a>
                                <br>
                            <?php endif; ?>

                            <small class="text-muted">+25 points</small>
                        </div>
                        <button class="btn <?php echo in_array('play_game', $completedToday) ? 'btn-secondary' : 'btn-success'; ?>"
                            <?php echo in_array('play_game', $completedToday) ? 'disabled' : ''; ?>
                            onclick="claimTask('play_game')">
                            <i class="fa-solid fa-gamepad <?php echo in_array('play_game', $completedToday) ? '' : 'fa-beat'; ?>"></i>
                            <?php echo in_array('play_game', $completedToday) ? 'Claimed' : 'Complete'; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share MintMint</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Copy the link below to share on your socials:</p>
                    <div class="input-group mb-3">
                        <input type="text" id="shareUrl" class="form-control" value="https://mintmint-gacha.com" readonly>
                        <button class="btn btn-primary" type="button" onclick="copyAndVerify()">Copy Link</button>
                    </div>
                    <small id="copyFeedback" class="text-success" style="display:none;">Link copied! You can now claim your points.</small>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CURRENT_USER_ID = "<?php echo $userId; ?>";
    </script>

    <script src="js/task.js"></script>

</body>

</html>