<?php
$isIn = isset($_SESSION['user_id']);
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary" data-bs-theme="dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="images/mintmint_logo.png" alt="MintMint Logo" title="MintMint Logo"
                style="width: 200px; height: 100px" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
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
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" />
                <button class="btn btn-success" type="submit">Search</button>
            </form>
            <ul class="navbar-nav">
                
                 <!-- Show sign up icon only if user is not signed in -->
                <?php if (!$isIn): ?>
                <li class="nav-item">
                    <a href="signup.php" class="nav-link">
                        <p class="nav-btn" style="color: white; margin: 0;">Sign Up</p>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a href="<?php echo $isIn ? 'account.php' : 'signin.php'; ?>" class="nav-link">
                        <img src="images/account_icon.png" alt="<?php echo $isIn ? 'Account' : 'Sign In'; ?>" title="<?php echo $isIn ? 'Account' : 'Sign In'; ?>" class="nav-icon" />
                    </a>
                </li>

                <!-- Show sign out icon only if user is signed in -->
                <?php if ($isIn): ?>
                <li class="nav-item">
                    <a href="signout_process.php" class="nav-link">
                        <p class="nav-btn" style="color: white; margin: 0;">Sign Out</p>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>