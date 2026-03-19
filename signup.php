<!DOCTYPE html>
<html lang="en">
<?php
include "inc/account-head.inc.php";
?>

<body style="display: block; padding: 0;">
  <main class="container-fluid">
    <div class="row min-vh-100">

      <!-- Left column: image -->
      <div class="register_bg col-md-6 d-none d-md-flex align-items-center justify-content-center bg-light p-0">
        <img src="images/mintmint_logo.png" alt="Register illustration" class="img-fluid" style="max-height: 80vh; object-fit: contain;">
      </div>

      <!-- Right column: register form -->
      <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 400px; padding: 2rem;">
          <div class="text-center mb-4">
            <h1 class="h3 font-weight-normal">Sign up for MintMint</h1>
          </div>

          <form class="form-signup" action="signup_process.php" method="post">
            <label for="inputusername">Username*</label>
            <input type="text" id="inputusername" name="username" class="form-control" placeholder="Username" required autofocus>

            <label for="inputEmail">Email address*</label>
            <input type="email" id="inputEmail" name="email" class="form-control" placeholder="Email address" required autofocus>

            <label for="inputPassword">Password*</label>
            <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
            <p class="form-text text-muted" style="font-size: smaller;">
              Password should be at least 15 characters OR at least 8 characters including a number and a lowercase letter.
            </p>

            <label for="inputConfirmPassword">Confirm Password*</label>
            <input type="password" id="inputConfirmPassword" name="confirm_password" class="form-control" placeholder="Confirm Password" required>

            <!-- <div class="checkbox mb-3 mt-2">
              <label>
                <input type="checkbox" value="t&c"> Accept <a href="#">Terms and Conditions</a>
              </label>
            </div> -->
            <button class="btn btn-lg btn-primary w-100" type="submit">Sign up</button>
          </form>
          <p class="mt-3 text-center">Already have an account? <a href="signin.php">Sign in &#8594;</a></p>
        </div>
      </div>

    </div>
  </main>
</body>
</html>
