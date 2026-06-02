<?php
require_once 'config.php';

// Route protection: Check if user is logged in for mutations (POST, PUT, DELETE)
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
        http_response_code(403);
        echo json_encode(["success" => false, "message" => "Unauthorized access request."]);
        exit;
    }
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Fetch all blog posts ordered by latest first
        $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
        $posts = $stmt->fetchAll();
        echo json_encode($posts);
        break;

    case 'POST':
        // Add new blog post
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("INSERT INTO blog_posts (title, content, image_url, category) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['image_url'] ?? null,
            $data['category'] ?? 'General'
        ]);
        echo json_encode(["success" => true, "message" => "Blog post created successfully."]);
        break;

    case 'PUT':
        // Update blog post
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Missing post ID for update operations."]);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ?, image_url = ?, category = ? WHERE id = ?");
        $stmt->execute([
            $data['title'],
            $data['content'],
            $data['image_url'] ?? null,
            $data['category'] ?? 'General',
            $data['id']
        ]);
        echo json_encode(["success" => true, "message" => "Blog post updated successfully."]);
        break;

    case 'DELETE':
        // Delete blog post
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Missing query identifier parameter."]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(["success" => true, "message" => "Blog post deleted."]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed."]);
        break;
}
?>
