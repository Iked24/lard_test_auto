<?php
require_once 'db.php';

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isManager() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'manager';
}

function isDriver() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'driver';
}

function login($username, $password) {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header('Location: index.php');
    exit;
}