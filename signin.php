<!DOCTYPE html>
<html lang="en">
<?php
include "inc/account-head.inc.php";
?>

<body class="text-center">
  <main>
    <form class="form-signin" action="signin_process.php" method="post">
       <img class="mb-4" src="images/mintmint_logo.png" alt="Placeholder" title="Placeholder Logo" height="72" />
      <h1 class="h3 mb-3 font-weight-normal">Sign in to MintMint</h1>
  
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="inputEmail" name="email"  class="form-control" placeholder="Email address" required autofocus>
  
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>

      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      
    </form>
    <p class="mt-3">Don't have an account? <a href="signup.php">Sign up here</a></p>
  </main>
  </body>
</html>
