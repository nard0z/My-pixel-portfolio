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
        // Fetch all elements
        $stmt = $pdo->query("SELECT * FROM portfolio_items ORDER BY created_at DESC");
        $items = $stmt->fetchAll();
        echo json_encode($items);
        break;

    case 'POST':
        // Add new project records
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $pdo->prepare("INSERT INTO portfolio_items (title, description, image_url, primary_link, secondary_link, link_label_1, link_label_2) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['title'], $data['description'] ?? null, $data['image_url'] ?? null,
            $data['primary_link'] ?? null, $data['secondary_link'] ?? null,
            $data['link_label_1'] ?? 'View Code', $data['link_label_2'] ?? 'Live Demo'
        ]);
        echo json_encode(["success" => true, "message" => "Portfolio item created successfully."]);
        break;

    case 'PUT':
        // Edit and update records
        $data = json_decode(file_get_contents("php://input"), true);
        if (!isset($data['id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Missing item ID for update operations."]);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE portfolio_items SET title = ?, description = ?, image_url = ?, primary_link = ?, secondary_link = ?, link_label_1 = ?, link_label_2 = ? WHERE id = ?");
        $stmt->execute([
            $data['title'], $data['description'] ?? null, $data['image_url'] ?? null,
            $data['primary_link'] ?? null, $data['secondary_link'] ?? null,
            $data['link_label_1'] ?? 'View Code', $data['link_label_2'] ?? 'Live Demo',
            $data['id']
        ]);
        echo json_encode(["success" => true, "message" => "Portfolio item updated successfully."]);
        break;

    case 'DELETE':
        // Remove project items
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Missing query identifier parameter."]);
            exit;
        }
        $stmt = $pdo->prepare("DELETE FROM portfolio_items WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(["success" => true, "message" => "Portfolio item deleted."]);
        break;
}
?>
