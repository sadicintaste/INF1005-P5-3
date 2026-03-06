<!DOCTYPE html>
<html lang="en">
<?php
include "inc/account-header.inc.php";
?>

<body class="text-center">
  <main>
    <form class="form-signin">
    <!-- <form action="login_process.php" method="post"> -->
      <img class="mb-4" src="images/logo.webp" alt="Placeholder" title="Placeholder Logo" width="72" height="72" />
      <h1 class="h3 mb-3 font-weight-normal">Sign in to 1005 project</h1>
  
      <label for="inputEmail" class="sr-only">Email address</label>
      <input type="email" id="inputEmail" class="form-control" placeholder="Email address" required autofocus>
  
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
  
      <div class="checkbox mb-3">
        <label>
          <input type="checkbox" value="t&c"> Accept <a href="#">Terms and Conditions</a>
        </label>
      </div>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
      
    </form>
    <p class="mt-3">Don't have an account? <a href="signup.php">Sign up here</a></p>
  </main>
  </body>
</html>
