<?php
require_once 'config.php';

// Route protection: Check if user is logged in
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Unauthorized access request."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['currentPassword']) || !isset($data['newPassword'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Current password and new password are required."]);
    exit;
}

// Get current admin user
$stmt = $pdo->query("SELECT * FROM admins LIMIT 1");
$admin = $stmt->fetch();

if (!$admin) {
    http_response_code(404);
    echo json_encode(["success" => false, "message" => "Admin user not found."]);
    exit;
}

// Verify current password
if (!password_verify($data['currentPassword'], $admin['password_hash'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Current password is incorrect."]);
    exit;
}

// Validate new password
if (strlen($data['newPassword']) < 6) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "New password must be at least 6 characters long."]);
    exit;
}

// Hash new password
$newPasswordHash = password_hash($data['newPassword'], PASSWORD_BCRYPT);

// Update password
$stmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
$stmt->execute([$newPasswordHash, $admin['id']]);

echo json_encode(["success" => true, "message" => "Password changed successfully."]);
?>
