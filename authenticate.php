<?php
session_start();
require_once __DIR__ . '/api/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$userIdentifier = trim($_POST['userIdentifier'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

if (!$userIdentifier || !$password) {
    header('Location: login.php?error=1');
    exit;
}

$result = authenticateUser($userIdentifier, $password);
if (isset($result['error'])) {
    header('Location: login.php?error=1');
    exit;
}

$user = $result;
$_SESSION['user'] = [
    'id' => $user['id'],
    'name' => $user['name'],
    'role' => $user['role'],
    'username' => $user['username'],
    'email' => $user['email'],
];

if ($remember) {
    setcookie('pos_remember', $user['username'], time() + 30 * 24 * 60 * 60, '/');
}

header('Location: index.php');
exit;
