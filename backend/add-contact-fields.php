<?php
// Migration script to add contact fields to admin_profile table

$host = '127.0.0.1';
$db   = 'my_portfolio';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Add contact fields if they don't exist
    $columns = [
        'email' => "VARCHAR(255) DEFAULT NULL",
        'phone' => "VARCHAR(50) DEFAULT NULL",
        'github' => "VARCHAR(255) DEFAULT NULL",
        'facebook' => "VARCHAR(255) DEFAULT NULL",
        'linkedin' => "VARCHAR(255) DEFAULT NULL",
        'twitter' => "VARCHAR(255) DEFAULT NULL",
        'instagram' => "VARCHAR(255) DEFAULT NULL",
        'resume_url' => "VARCHAR(500) DEFAULT NULL"
    ];
    
    foreach ($columns as $column => $definition) {
        // Check if column exists
        $stmt = $pdo->query("SHOW COLUMNS FROM admin_profile LIKE '$column'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE admin_profile ADD COLUMN $column $definition");
            echo "✓ Added column: $column\n";
        } else {
            echo "− Column already exists: $column\n";
        }
    }
    
    echo "\n✓ Contact fields migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
