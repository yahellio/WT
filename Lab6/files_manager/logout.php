<?php
session_start();
global $pdo;
require 'db.php';

if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("UPDATE users SET auth_token = NULL WHERE username = ?");
    $stmt->execute([$_SESSION['user']]);
}

setcookie('auth_token', '', time() - 3600, "/");
session_destroy();

header("Location: login.php");
