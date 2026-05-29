<?php
/**
 * Members API - CRUD operations for band members (Characters)
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once '../config/database.php';
include_once '../models/Member.php';

$database = new Database();
$db = $database->getConnection();
$member = new Member($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($action === 'getById' && isset($_GET['id'])) {
                // Get single member
                $result = $member->getById($_GET['id']);
                if ($result) {
                    http_response_code(200);
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'Member not found']);
                }
            } else {
                // Get all members
                $stmt = $member->getAll();
                $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'data' => $members,
                    'count' => count($members)
                ]);
            }
            break;

        case 'POST':
            if ($action === 'delete' && isset($_GET['id'])) {
                // Delete member
                if ($member->delete($_GET['id'])) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Member deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete member']);
                }
            } else {
                // Create new member
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (!empty($data['full_name'])) {
                    if ($member->create($data)) {
                        http_response_code(201);
                        echo json_encode(['success' => true, 'message' => 'Member created successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to create member']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid data. Full name is required.']);
                }
            }
            break;

        case 'PUT':
            if (isset($_GET['id'])) {
                // Update member
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (!empty($data['full_name'])) {
                    if ($member->update($_GET['id'], $data)) {
                        http_response_code(200);
                        echo json_encode(['success' => true, 'message' => 'Member updated successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to update member']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid data. Full name is required.']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Member ID is required']);
            }
            break;

        case 'DELETE':
            if (isset($_GET['id'])) {
                // Delete member
                if ($member->delete($_GET['id'])) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'Member deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete member']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Member ID is required']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
