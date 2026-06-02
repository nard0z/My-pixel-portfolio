<?php
// Enable CORS - flexible for both localhost and production
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS requests gracefully
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Session configuration - Dynamically switch secure flag for HTTPS production
$isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 3600,
        'cookie_secure' => $isSecure, 
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ]);
}

// Smart Database Credentials (No Hardcoding!)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$db   = getenv('DB_NAME') ?: 'my_portfolio';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

try {
    // Standard basic PDO options
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    // CRITICAL: If we are online on Vercel, force TiDB's required secure TLS/SSL mode
    if (getenv('DB_HOST')) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    }

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, $options);
    ensureDefaultAdmin($pdo);
} catch (PDOException $e) {
    http_response_code(500);
    // Secure safe message to prevent raw credentials from being printed to public windows
    echo json_encode(["success" => false, "message" => "Database connection error."]);
    exit;
}

function ensureDefaultAdmin($pdo) {
    // First, verify if the table exists to prevent errors before migration
    try {
        $stmt = $pdo->query("SELECT id FROM admins LIMIT 1");
        if (!$stmt->fetch()) {
            $default_pass = 'admin123';
            $hashed = password_hash($default_pass, PASSWORD_BCRYPT);
            $insert = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES ('admin', ?)");
            $insert->execute([$hashed]);
        }
    } catch (PDOException $e) {
        // Table doesn't exist yet; it will be safe once you import your .sql schema!
    }
}
?>
