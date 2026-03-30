<?php
$isIn = isset($_SESSION['user_id']);
$username = '';
$points = 0;

if ($isIn) {
    $username = $_SESSION['username'] ?? 'User';
    $points = (int)($_SESSION['points'] ?? 0);

    try {
        require_once __DIR__ . '/db_connect.php';
        $conn = DBConnect::connect();
        $stmt = $conn->prepare("SELECT username, points FROM User WHERE user_id = ? LIMIT 1");

        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['user_id']);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $username = $row['username'];
                    $points = (int)$row['points'];

                    // Keep session in sync with the latest account values.
                    $_SESSION['username'] = $username;
                    $_SESSION['points'] = $points;
                }
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        // Fallback to session values if DB fetch fails.
    }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark glass-navbar mx-3 mt-3">
    <div class="container-fluid px-3 glass-navbar-inner">
        <a class="navbar-brand nav-brand" href="index.php">
            <img src="images/mintmint_logo.png" alt="MintMint Logo" title="MintMint Logo" class="navbar-logo" width="100" height="60" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-primary-links">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>

                <?php if ($isIn): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="shop.php">Point Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tasks.php">Tasks</a>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="about.php">About Us</a>
                </li>
            </ul>

            <ul class="navbar-nav align-items-lg-center gap-lg-2 nav-actions">
                <?php if (!$isIn): ?>
                    <li class="nav-item">
                        <a href="signin.php" class="btn signin-btn">Sign In</a>
                    </li>
                    <li class="nav-item">
                        <a href="signup.php" class="btn btn-outline-light">Sign Up</a>
                    </li>
                <?php endif; ?>

                <?php if ($isIn): ?>
                    <li class="nav-item">
                        <a href="account.php" class="nav-link nav-account-chip" title="My Account">
                            <img src="images/account_icon.png" alt="Account" class="nav-icon" />
                            <span class="nav-user-meta">
                                <span>Hi, <?php echo htmlspecialchars($username); ?></span>
                                <span>⭐ <?php echo number_format($points); ?> pts</span>
                            </span>
                        </a>
                    </li>
                <?php endif; ?>
                
            </ul>
        </div>
    </div>
</nav>