<?php
// includes/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Agar login page par ho to allow kar do
$current = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$onLoginPage = (strtolower($current) === 'login.php');

// Agar user login nahi hai aur login.php par bhi nahi hai → direct login.php bhej do
if (empty($_SESSION['user_id']) && !$onLoginPage) {
    header("Location: login.php");
    exit;
}
