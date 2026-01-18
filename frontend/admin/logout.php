<?php
require_once __DIR__ . '/../../backend/services/authentication.php';

session_start();
LogoutUser();

header('Location: ../../frontend/login.php');
exit();
?>