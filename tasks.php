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
<?php
$style = "tasks.css";
include 'inc/head.inc.php'; ?>

<body>
    <?php include 'inc/nav.inc.php'; ?>
    <?php include "inc/pokemonIcons.inc.php"; ?>

    <?php
    $totalTasks = 4;
    $completedCount = count($completedToday);
    $percentage = ($completedCount / $totalTasks) * 100;
    ?>
    <div class="progress mb-5" style="height: 25px; background-color: #1a1a1a; border: 1px solid #333; border-radius: 50px;">
        <div class="progress-bar" role="progressbar"
            style="width: <?php echo $percentage; ?>%; background: linear-gradient(90deg, #00ffcc, #0099ff); box-shadow: 0 0 15px #00ffcc80;"
            aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100">
            <span class="fw-bold text-dark"><?php echo $completedCount; ?>/<?php echo $totalTasks; ?> Complete</span>
        </div>
    </div>
    <h1 class="text-center mb-4" style="color: white;">Daily Tasks</h1>
    <p class="text-center mb-5" style="color: white;">Complete these daily tasks to earn in-game points!</p>

    <div class="container">
        <div class="row g-4">
            <?php
            $tasks = [
                ['id' => 'login', 'title' => 'Log in to MintMint', 'desc' => 'Sign in to your account today.', 'pts' => 10],
                ['id' => 'visit_shop', 'title' => 'Visit the shop', 'desc' => 'Browse the point shop for rewards.', 'pts' => 15],
                ['id' => 'share_social', 'title' => 'Spread the word!', 'desc' => 'Share our link with your friends.', 'pts' => 20],
                ['id' => 'play_game', 'title' => 'Play a game', 'desc' => 'Complete the memory game on the site.', 'pts' => 25],
            ];

            foreach ($tasks as $t):
                $isDone = in_array($t['id'], $completedToday);
            ?>
                <div class="col-md-6">
                    <div class="card bg-dark h-100 task-card <?php echo $isDone ? 'task-completed' : ''; ?>">
                        <div class="card-body d-flex flex-column justify-content-between p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="task-title mb-1 text-light"><?php echo $t['title']; ?></h5>
                                    <p class="text-muted small mb-0"><?php echo $t['desc']; ?></p>

                                    <?php if ($t['id'] === 'share_social' && !$isDone): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#shareModal">Get Link</button>
                                    <?php elseif ($t['id'] === 'play_game' && !$isDone): ?>
                                        <a href="game.php" class="btn btn-sm btn-outline-info mt-2">Go to Game</a>
                                    <?php endif; ?>
                                </div>
                                <span class="badge rounded-pill reward-badge">+<?php echo $t['pts']; ?> pts</span>
                            </div>

                            <div class="d-grid mt-auto">
                                <button id="btn-<?php echo $t['id']; ?>"
                                    class="btn btn-mint-action <?php echo $isDone ? 'completed' : ''; ?>"
                                    <?php echo $isDone ? 'disabled' : ''; ?>
                                    onclick="claimTask('<?php echo $t['id']; ?>')">
                                    <?php if ($isDone): ?>
                                        <i class="fa-solid fa-circle-check me-2"></i> Claimed
                                    <?php else: ?>
                                        <i class="fa-solid fa-circle-notch fa-spin me-2" style="--fa-animation-duration: 2s;"></i> Complete
                                    <?php endif; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
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

    <script src="js/tasks.js"></script>

</body>

</html>