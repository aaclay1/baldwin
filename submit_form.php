<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '0');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    global $response;
    $response['error'] = "PHP Error ($errno): $errstr";
    http_response_code(500);
    echo json_encode($response);
    exit;
});

$response = [];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['error'] = 'Not a POST request';
    http_response_code(400);
    echo json_encode($response);
    exit;
}

$input = file_get_contents('php://input');
if (empty($input)) {
    $response['error'] = 'No input received';
    http_response_code(400);
    echo json_encode($response);
    exit;
}

$data = json_decode($input, true);
if ($data === null) {
    $response['error'] = 'Invalid JSON: ' . json_last_error_msg();
    http_response_code(400);
    echo json_encode($response);
    exit;
}

// Validate token if provided
$invitation_id = null;
if (!empty($data['token'])) {
    require_once 'config.php';
    
    try {
        $stmt = $pdo->prepare('
            SELECT id FROM form_invitations 
            WHERE token = ?
            AND used = FALSE 
            AND expires_at > NOW()
            LIMIT 1
        ');
        $stmt->execute([$data['token']]);
        $invitation = $stmt->fetch();
        
        if (!$invitation) {
            $response['error'] = 'Invalid or expired invitation token';
            http_response_code(401);
            echo json_encode($response);
            exit;
        }
        $invitation_id = $invitation['id'];
    } catch (PDOException $e) {
        $response['error'] = 'Database error validating token';
        http_response_code(500);
        echo json_encode($response);
        exit;
    }
} else {
    require_once 'config.php';
}

if (!isset($pdo) || !$pdo) {
    $response['error'] = 'Database connection failed';
    http_response_code(500);
    echo json_encode($response);
    exit;
}

try {
    // Save basic info + all data as JSON
    $stmt = $pdo->prepare('
        INSERT INTO form_submissions (
            name, email, phone1, phone2, mailing_address, physical_address, 
            own_rent, county, form_data, ip_address, user_agent, submitted_at, viewed_by_admin
        ) VALUES (
            :name, :email, :phone1, :phone2, :mailing_address, :physical_address, 
            :own_rent, :county, :form_data, :ip_address, :user_agent, NOW(), FALSE
        )
    ');
    
    $stmt->execute([
        ':name' => $data['name'] ?? null,
        ':email' => $data['email'] ?? null,
        ':phone1' => $data['phone1'] ?? null,
        ':phone2' => $data['phone2'] ?? null,
        ':mailing_address' => $data['mailing_address'] ?? null,
        ':physical_address' => $data['physical_address'] ?? null,
        ':own_rent' => $data['own_rent'] ?? null,
        ':county' => $data['county'] ?? null,
        ':form_data' => json_encode($data),  // Store ALL data as JSON
        ':ip_address' => $_SERVER['REMOTE_ADDR'],
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    
    $submission_id = $pdo->lastInsertId();
    
    // Mark invitation as used if it was provided
    if (!empty($invitation_id)) {
        $update_stmt = $pdo->prepare('
            UPDATE form_invitations 
            SET used = TRUE, used_at = NOW(), submission_id = ?
            WHERE id = ?
        ');
        $update_stmt->execute([$submission_id, $invitation_id]);
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Form submitted successfully!',
        'submission_id' => $submission_id
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
}
?>
