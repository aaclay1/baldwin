<?php
header('Content-Type: application/json');
session_start();

require_once 'config.php';

if (empty($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? '';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD);

    if ($action === 'get_submissions') {
        $stmt = $pdo->query("
            SELECT 
                fs.id, 
                fs.form_type, 
                fi.name as invite_name, 
                fi.email as invite_email, 
                fi.phone as invite_phone,
                fs.submitted_at
            FROM form_submissions fs
            LEFT JOIN form_invitations fi ON fs.id = fi.submission_id
            ORDER BY fs.submitted_at DESC
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    
    elseif ($action === 'get_submission') {
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("SELECT * FROM form_submissions WHERE id = ?");
        $stmt->execute([$id]);
        $submission = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($submission && $submission['form_data']) {
            $submission['form_data_parsed'] = json_decode($submission['form_data'], true);
        }
        
        echo json_encode($submission);
    }
    
    elseif ($action === 'delete_submission') {
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM form_submissions WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
    
    else {
        echo json_encode(['error' => 'Unknown action']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
