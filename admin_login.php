<?php
header('Content-Type: application/json');
session_start();

require_once 'config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['username']) || empty($input['password'])) {
        throw new Exception('Invalid credentials');
    }

    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    
    $stmt = $pdo->prepare("SELECT id, username FROM admin_users WHERE username = ? AND password = ?");
    $stmt->execute([$input['username'], md5($input['password'])]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_user'] = $user['username'];
        $_SESSION['login_time'] = time();
        
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        throw new Exception('Invalid credentials');
    }

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
