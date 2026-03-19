<!DOCTYPE html>
<html lang="en">
<?php
session_start();
include "inc/account-head.inc.php";

$errorMessages = [
  'missing_fields' => 'Please enter both email and password.',
  'invalid' => 'Invalid email or password.',
  'server_error' => 'A server error occurred. Please try again later.'
];

$successMessages = [
  'signed_out' => 'You have signed out successfully.',
  'account_created' => 'Your account was created successfully. Please sign in.'
];

$errorCode = $_GET['error'] ?? null;
$errorMessage = ($errorCode && isset($errorMessages[$errorCode])) ? $errorMessages[$errorCode] : null;
$successCode = $_GET['success'] ?? null;
$successMessage = ($successCode && isset($successMessages[$successCode])) ? $successMessages[$successCode] : null;
$oldInput = $_SESSION['signin_old_input'] ?? ['email' => ''];
unset($_SESSION['signin_old_input']);
?>

<body class="text-center signin-body">
  <a href="index.php" class="back-home-btn">&larr; Back to Home</a>
  <main>
    <form class="form-signin" action="signin_process.php" method="post">
       <img class="mb-4" src="images/mintmint_logo.png" alt="Placeholder" title="Placeholder Logo" height="72" />
      <h1 class="h3 mb-3 font-weight-normal">Sign in to MintMint</h1>

      <?php if ($successMessage): ?>
        <div class="alert alert-success" role="alert">
          <?php echo htmlspecialchars($successMessage); ?>
        </div>
      <?php endif; ?>

      <?php if ($errorMessage): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo htmlspecialchars($errorMessage); ?>
        </div>
      <?php endif; ?>
  
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="inputEmail" name="email"  class="form-control" placeholder="Email address" value="<?php echo htmlspecialchars($oldInput['email']); ?>" required autofocus>
  
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>

      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      
    </form>
    <p class="mt-3">Don't have an account? <a href="signup.php">Sign up &#8594;</a></p>
  </main>
  </body>
</html>
