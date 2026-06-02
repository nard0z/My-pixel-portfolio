<?php
// Enable CORS - flexible for both localhost and production
// Using relative paths means same-origin requests, but headers kept for compatibility
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS requests gracefully
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 3600,
        'cookie_secure' => false, // Set to true if using HTTPS online
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Database Credentials
$host = '127.0.0.1';
$db   = 'my_portfolio';
$user = 'root';
$pass = ''; // Your MariaDB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    ensureDefaultAdmin($pdo);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database link broken: " . $e->getMessage()]);
    exit;
}

function ensureDefaultAdmin($pdo) {
    $stmt = $pdo->query("SELECT id FROM admins LIMIT 1");
    if (!$stmt->fetch()) {
        $default_pass = 'admin123';
        $hashed = password_hash($default_pass, PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES ('admin', ?)");
        $insert->execute([$hashed]);
    }
}
?>
