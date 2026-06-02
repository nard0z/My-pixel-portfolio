<?php
require_once 'config.php';

// Route protection: Check if user is logged in for mutations (POST, PUT)
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
        // Fetch profile (publicly accessible)
        $stmt = $pdo->query("SELECT * FROM admin_profile ORDER BY id DESC LIMIT 1");
        $profile = $stmt->fetch();
        
        if ($profile) {
            echo json_encode($profile);
        } else {
            // Return empty profile structure if none exists
            echo json_encode([
                'id' => null,
                'fullname' => '',
                'title' => '',
                'bio' => '',
                'profile_pic_url' => '',
                'skills' => '',
                'email' => '',
                'phone' => '',
                'github' => '',
                'facebook' => '',
                'linkedin' => '',
                'twitter' => '',
                'instagram' => '',
                'resume_url' => ''
            ]);
        }
        break;

    case 'POST':
        // Create or update profile (admin protected)
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Check if profile already exists
        $stmt = $pdo->query("SELECT id FROM admin_profile LIMIT 1");
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update existing profile
            $stmt = $pdo->prepare("UPDATE admin_profile SET fullname = ?, title = ?, bio = ?, profile_pic_url = ?, skills = ?, email = ?, phone = ?, github = ?, facebook = ?, linkedin = ?, twitter = ?, instagram = ?, resume_url = ? WHERE id = ?");
            $stmt->execute([
                $data['fullname'],
                $data['title'],
                $data['bio'] ?? null,
                $data['profile_pic_url'] ?? null,
                $data['skills'] ?? null,
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['github'] ?? null,
                $data['facebook'] ?? null,
                $data['linkedin'] ?? null,
                $data['twitter'] ?? null,
                $data['instagram'] ?? null,
                $data['resume_url'] ?? null,
                $existing['id']
            ]);
            echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
        } else {
            // Create new profile
            $stmt = $pdo->prepare("INSERT INTO admin_profile (fullname, title, bio, profile_pic_url, skills, email, phone, github, facebook, linkedin, twitter, instagram, resume_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['fullname'],
                $data['title'],
                $data['bio'] ?? null,
                $data['profile_pic_url'] ?? null,
                $data['skills'] ?? null,
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['github'] ?? null,
                $data['facebook'] ?? null,
                $data['linkedin'] ?? null,
                $data['twitter'] ?? null,
                $data['instagram'] ?? null,
                $data['resume_url'] ?? null
            ]);
            echo json_encode(["success" => true, "message" => "Profile created successfully."]);
        }
        break;

    case 'PUT':
        // Update profile (admin protected)
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['id'])) {
            // If no ID provided, update the first/only profile
            $stmt = $pdo->query("SELECT id FROM admin_profile LIMIT 1");
            $existing = $stmt->fetch();
            
            if (!$existing) {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "No profile found to update."]);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE admin_profile SET fullname = ?, title = ?, bio = ?, profile_pic_url = ?, skills = ?, email = ?, phone = ?, github = ?, facebook = ?, linkedin = ?, twitter = ?, instagram = ?, resume_url = ? WHERE id = ?");
            $stmt->execute([
                $data['fullname'],
                $data['title'],
                $data['bio'] ?? null,
                $data['profile_pic_url'] ?? null,
                $data['skills'] ?? null,
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['github'] ?? null,
                $data['facebook'] ?? null,
                $data['linkedin'] ?? null,
                $data['twitter'] ?? null,
                $data['instagram'] ?? null,
                $data['resume_url'] ?? null,
                $existing['id']
            ]);
        } else {
            // Update specific profile by ID
            $stmt = $pdo->prepare("UPDATE admin_profile SET fullname = ?, title = ?, bio = ?, profile_pic_url = ?, skills = ?, email = ?, phone = ?, github = ?, facebook = ?, linkedin = ?, twitter = ?, instagram = ?, resume_url = ? WHERE id = ?");
            $stmt->execute([
                $data['fullname'],
                $data['title'],
                $data['bio'] ?? null,
                $data['profile_pic_url'] ?? null,
                $data['skills'] ?? null,
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['github'] ?? null,
                $data['facebook'] ?? null,
                $data['linkedin'] ?? null,
                $data['twitter'] ?? null,
                $data['instagram'] ?? null,
                $data['resume_url'] ?? null,
                $data['id']
            ]);
        }
        
        echo json_encode(["success" => true, "message" => "Profile updated successfully."]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Method not allowed."]);
        break;
}
?>
