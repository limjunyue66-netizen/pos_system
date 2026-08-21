<?php

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function currentUser() {
    return $_SESSION['user'] ?? null;
}

function isAdmin() {
    return (currentUser()['role'] ?? '') === 'admin';
}

function requireAdminPage() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function redirectWith($url) {
    header('Location: ' . $url);
    exit;
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function requireApiLogin() {
    if (!isset($_SESSION['user'])) {
        jsonResponse(['success' => false, 'message' => 'Please login first.'], 401);
    }
}
