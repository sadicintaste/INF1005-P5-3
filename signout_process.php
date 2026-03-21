<?php
session_start();
$_SESSION = [];
session_destroy();
header("Location: index.php?success=signed_out");
exit();
?>
