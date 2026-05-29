<?php
/**
 * History API - CRUD operations for band history timeline events
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
include_once '../models/History.php';

$database = new Database();
$db = $database->getConnection();
$history = new History($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if ($action === 'getById' && isset($_GET['id'])) {
                // Get single history event
                $history->timeline_id = $_GET['id'];
                $result = $history->getSingle()->fetch(PDO::FETCH_ASSOC);
                if ($result) {
                    http_response_code(200);
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(['message' => 'History event not found']);
                }
            } else {
                // Get all history events
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $stmt = $history->getAll($search);
                $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
                echo json_encode([
                    'success' => true,
                    'data' => $events,
                    'count' => count($events)
                ]);
            }
            break;

        case 'POST':
            if ($action === 'delete' && isset($_GET['id'])) {
                // Delete history event
                $history->timeline_id = $_GET['id'];
                if ($history->delete()) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'History event deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete history event']);
                }
            } else {
                // Create or update history event
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (!empty($data['year']) && !empty($data['title'])) {
                    if (isset($data['timeline_id']) && !empty($data['timeline_id'])) {
                        // Update existing event
                        $history->timeline_id = $data['timeline_id'];
                        $history->year = $data['year'];
                        $history->title = $data['title'];
                        $history->description = isset($data['description']) ? $data['description'] : '';
                        
                        if ($history->update()) {
                            http_response_code(200);
                            echo json_encode(['success' => true, 'message' => 'History event updated successfully']);
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => 'Failed to update history event']);
                        }
                    } else {
                        // Create new event
                        $history->year = $data['year'];
                        $history->title = $data['title'];
                        $history->description = isset($data['description']) ? $data['description'] : '';
                        
                        if ($history->create()) {
                            http_response_code(201);
                            echo json_encode(['success' => true, 'message' => 'History event created successfully']);
                        } else {
                            http_response_code(500);
                            echo json_encode(['success' => false, 'message' => 'Failed to create history event']);
                        }
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid data. Year and title are required.']);
                }
            }
            break;

        case 'PUT':
            if (isset($_GET['id'])) {
                // Update history event
                $data = json_decode(file_get_contents("php://input"), true);
                
                if (!empty($data['year']) && !empty($data['title'])) {
                    $history->timeline_id = $_GET['id'];
                    $history->year = $data['year'];
                    $history->title = $data['title'];
                    $history->description = isset($data['description']) ? $data['description'] : '';
                    
                    if ($history->update()) {
                        http_response_code(200);
                        echo json_encode(['success' => true, 'message' => 'History event updated successfully']);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to update history event']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid data. Year and title are required.']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'History event ID is required']);
            }
            break;

        case 'DELETE':
            if (isset($_GET['id'])) {
                // Delete history event
                $history->timeline_id = $_GET['id'];
                if ($history->delete()) {
                    http_response_code(200);
                    echo json_encode(['success' => true, 'message' => 'History event deleted successfully']);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to delete history event']);
                }
            } else {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'History event ID is required']);
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
