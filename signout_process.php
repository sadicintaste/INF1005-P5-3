<?php
session_start();
// $_SESSION = [];
// session_destroy();
unset($_SESSION['user_id']);
header("Location: index.php?success=signed_out");
exit();
?>
