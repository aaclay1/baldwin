<?php
header('Content-Type: application/json');
session_start();

require_once 'config.php';

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || empty($input['email']) || empty($input['name'])) {
        throw new Exception('Missing required fields');
    }

    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);
    
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + TOKEN_EXPIRY);
    
    $stmt = $pdo->prepare("
        INSERT INTO form_invitations (token, email, phone, name, form_type, expires_at)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $token,
        $input['email'],
        $input['phone'] ?? '',
        $input['name'],
        $input['form_type'] ?? 'customer',
        $expires
    ]);

    $link = SITE_URL . '/customer.html?token=' . $token;
    
    // Send email
    $to = $input['email'];
    $subject = 'Baldwin Insurance - Your Quote Request';
    $message = "Hi " . $input['name'] . ",\n\n";
    $message .= "Thank you for your interest in Baldwin Insurance. Please complete your customer information form using the link below:\n\n";
    $message .= $link . "\n\n";
    $message .= "This link will expire in 1 hour.\n\n";
    $message .= "Best regards,\n";
    $message .= "Baldwin Insurance\n";
    $message .= SITE_URL . "\n";
    
    $headers = "From: " . ADMIN_EMAIL . "\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    mail($to, $subject, $message, $headers);

    echo json_encode([
        'success' => true,
        'token' => $token,
        'link' => $link,
        'message' => 'Invitation created successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
