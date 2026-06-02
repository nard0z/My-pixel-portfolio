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

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "No file uploaded or upload error."]);
    exit;
}

$file = $_FILES['image'];
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$maxSize = 5 * 1024 * 1024; // 5MB

// Validate file type
if (!in_array($file['type'], $allowedTypes)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed."]);
    exit;
}

// Validate file size
if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "File too large. Maximum size is 5MB."]);
    exit;
}

// Generate unique filename
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid('img_', true) . '.' . $extension;
$uploadPath = __DIR__ . '/../uploads/' . $filename;

// Move uploaded file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Return the URL path (relative to the API folder)
    $urlPath = '../backend/uploads/' . $filename;
    echo json_encode([
        "success" => true,
        "message" => "File uploaded successfully",
        "url" => $urlPath
    ]);
} else {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to save uploaded file."]);
}
?>
