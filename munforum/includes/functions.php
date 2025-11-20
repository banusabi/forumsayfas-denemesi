<?php
// includes/functions.php
session_start();

$DB_HOST = 'sql306.infinityfree.com';
$DB_USER = 'if0_40402787';
$DB_PASS = 'VIDkYCQBj5f4oZ';
$DB_NAME = 'if0_40402787_mundb';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    error_log("DB connect failed: " . $mysqli->connect_error);
    die("Database connection failed.");
}
$mysqli->set_charset('utf8mb4');

function get_ip() {
    // Basit: gerçek IP için X-Forwarded-For kontrol edilebilir, ancak InfinityFree proxy olabilir
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function current_user_role() {
    return $_SESSION['role'] ?? 'user';
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}
