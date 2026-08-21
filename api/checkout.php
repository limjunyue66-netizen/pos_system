<?php
session_start();
require_once dirname(__DIR__) . '/includes/helpers.php';
require_once dirname(__DIR__) . '/includes/store.php';

requireApiLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method.'], 405);
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!is_array($payload)) {
    jsonResponse(['success' => false, 'message' => 'Invalid checkout data.'], 400);
}

$result = checkoutSale($payload, $_SESSION['user']);
if (isset($result['error'])) {
    jsonResponse(['success' => false, 'message' => $result['error']], 400);
}

jsonResponse(['success' => true, 'sale' => $result]);
