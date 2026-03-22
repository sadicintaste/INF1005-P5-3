<?php
session_start();
$isIn = isset($_SESSION['user_id']);

// Redirect if not logged in
if (!$isIn) {
    header("Location: signin.php");
    exit();
}

include "inc/head.inc.php";
include "inc/db_connect.php";

$errorMsg = "";
$success = true;
$username = "";

try {
    $user_id = (int)$_SESSION['user_id'];
    $user = DBConnect::getUserDetails($user_id);

    if ($user === null) {
        throw new Exception("User not found.");
    } else {
        $username = $user['username'];
    }
} catch (Exception $e) {
    $errorMsg = $e->getMessage();
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<body>
    <?php include 'inc/nav.inc.php'; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card bg-dark text-light">
                    <div class="card-body">
                        <h1 class="card-title text-center mb-4">Account Settings</h1>

                        <?php if (isset($_SESSION['update_success'])) : ?>
                            <div class="alert alert-success">
                                <?php
                                echo $_SESSION['update_success'];
                                unset($_SESSION['update_success']);
                                ?>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['update_error'])) : ?>
                            <div class="alert alert-danger">
                                <?php
                                echo $_SESSION['update_error'];
                                unset($_SESSION['update_error']);
                                ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$success) : ?>
                            <p class="text-danger text-center"><?php echo $errorMsg; ?></p>
                        <?php else : ?>
                            <form action="settings_process.php" method="post">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Current Password (Required to make changes)</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                            </form>

                            <hr class="my-4">

                            <div class="text-center">
                                <p class="text-center">Permanently delete your account and all associated data. This action cannot be undone.</p>
                                <a href="account_delete.php" class="btn btn-danger">Delete My Account</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>