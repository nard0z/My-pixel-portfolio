<?php
// Database Setup Script for MyPortfolio
// Run this file once to create all required tables

$host = '127.0.0.1';
$db   = 'my_portfolio';
$user = 'root';
$pass = '';

try {
    // Connect to MySQL server (without database selected)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db`");
    echo "Database '$db' created or already exists.\n";
    
    // Select the database
    $pdo->exec("USE `$db`");
    
    // Create admins table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Table 'admins' created or already exists.\n";
    
    // Create portfolio_items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS portfolio_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image_url VARCHAR(500),
        primary_link VARCHAR(500),
        secondary_link VARCHAR(500),
        link_label_1 VARCHAR(100) DEFAULT 'View Code',
        link_label_2 VARCHAR(100) DEFAULT 'Live Demo',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Table 'portfolio_items' created or already exists.\n";
    
    // Create blog_posts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        image_url VARCHAR(500),
        category VARCHAR(50) DEFAULT 'General',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Table 'blog_posts' created or already exists.\n";
    
    // Create admin_profile table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_profile (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(100),
        title VARCHAR(100),
        bio TEXT,
        profile_pic_url VARCHAR(500),
        skills TEXT,
        email VARCHAR(255),
        phone VARCHAR(50),
        github VARCHAR(255),
        facebook VARCHAR(255),
        linkedin VARCHAR(255),
        twitter VARCHAR(255),
        instagram VARCHAR(255),
        resume_url VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Table 'admin_profile' created or already exists.\n";
    
    // Create default admin user if not exists
    $stmt = $pdo->query("SELECT id FROM admins WHERE username = 'admin'");
    if (!$stmt->fetch()) {
        $default_pass = 'admin123';
        $hashed = password_hash($default_pass, PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES ('admin', ?)");
        $insert->execute([$hashed]);
        echo "Default admin user created (username: admin, password: admin123)\n";
    } else {
        echo "Default admin user already exists.\n";
    }
    
    echo "\n✓ Database setup completed successfully!\n";
    echo "You can now access the application.\n";
    echo "Default login: username 'admin', password 'admin123'\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
