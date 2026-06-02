<?php
require_once 'config.php';

$data = json_decode(file_get_contents("php://input"), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing username or password fields."]);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch();

if ($admin && password_verify($password, $admin['password_hash'])) {
    $_SESSION['isAdmin'] = true;
    $_SESSION['adminId'] = $admin['id'];
    echo json_encode(["success" => true, "message" => "Access authorized."]);
} else {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Invalid credentials entered."]);
}
?>
