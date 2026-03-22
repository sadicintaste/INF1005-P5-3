<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}
include "inc/head.inc.php";
?>

<!DOCTYPE html>
<html lang="en">

<body>
    <?php include 'inc/nav.inc.php'; ?>

    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card bg-dark text-light">
                    <div class="card-body text-center">
                        <h1 class="card-title text-danger">Delete Account</h1>
                        <p>Are you sure you want to permanently delete your account? All of your data, including inventory, will be lost. This action cannot be undone.</p>
                        <form action="delete_process.php" method="post">
                            <div class="mb-3">
                                <label for="password" class="form-label">Enter your password to confirm:</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                             <?php if (isset($_SESSION['delete_error'])) : ?>
                                <div class="alert alert-danger">
                                    <?php
                                    echo $_SESSION['delete_error'];
                                    unset($_SESSION['delete_error']);
                                    ?>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-danger w-100">I understand, delete my account</button>
                            <a href="account_settings.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>

</html>