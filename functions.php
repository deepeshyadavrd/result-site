<?php
// functions.php
session_start();
require_once __DIR__ . '/db.php';

function is_logged_in() {
    return !empty($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header("Location: /admin/login.php");
        exit;
    }
}

function current_user() {
    global $db;
    if (!is_logged_in()) return null;
    $stmt = $db->prepare("SELECT id, username, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function is_superadmin() {
    $u = current_user();
    return $u && $u['role'] === 'superadmin';
}
