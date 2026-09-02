<?php
header('Content-Type: application/json');
session_start();

require_once 'config.php';

if (empty($_SESSION['admin_id']) || empty($_SESSION['login_time'])) {
    http_response_code(401);
    echo json_encode(['valid' => false]);
    exit;
}

$elapsed = time() - $_SESSION['login_time'];
if ($elapsed > SESSION_TIMEOUT) {
    session_destroy();
    http_response_code(401);
    echo json_encode(['valid' => false, 'message' => 'Session expired']);
    exit;
}

$_SESSION['login_time'] = time();

echo json_encode([
    'valid' => true,
    'admin_user' => $_SESSION['admin_user'] ?? 'admin'
]);
?>
