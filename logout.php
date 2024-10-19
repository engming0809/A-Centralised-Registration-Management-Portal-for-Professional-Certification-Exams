<?php
session_start();

$_SESSION = [];

// Destroy the session
session_destroy();

header("Location: index.php");
exit();
?>