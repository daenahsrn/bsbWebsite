<?php
/**
 * Albums API - CRUD operations for albums with search functionality
 */

header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../models/Album.php';

$database = new Database();
$db = $database->getConnection(); // FIX #2: was $database->connect() which does not exist
$album = new Album($db);

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $album->album_id = $_GET['id'];
            $stmt = $album->getSingle();
            if ($stmt->rowCount() > 0) {
                echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Album not found']);
            }
        } else {
            $search = isset($_GET['search']) ? $_GET['search'] : '';
            $stmt = $album->getAll($search);
            $albums = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $albums[] = $row;
            }
            echo json_encode($albums);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        // Handle Image Upload if present in $_FILES
        $cover_img = $data->cover_img ?? '';
        if (!empty($_FILES['cover_img']['name'])) {
            $target_dir = "../uploads/albums/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            $target_file = $target_dir . basename($_FILES["cover_img"]["name"]);
            
            if(move_uploaded_file($_FILES["cover_img"]["tmp_name"], $target_file)) {
                $cover_img = "uploads/albums/" . basename($_FILES["cover_img"]["name"]);
            }
        }

        if (isset($data->action) && $data->action == 'update') {
            // Update
            $album->album_id = $data->album_id;
            $album->title = $data->title;
            $album->release = $data->release;
            $album->cover_img = $cover_img;
            $album->description = $data->description;

            if ($album->update()) {
                echo json_encode(['message' => 'Album updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to update album']);
            }
        } else {
            // Create
            $album->title = $data->title;
            $album->release = $data->release;
            $album->cover_img = $cover_img;
            $album->description = $data->description;

            if ($album->create()) {
                echo json_encode(['message' => 'Album created successfully', 'id' => $db->lastInsertId()]);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to create album']);
            }
        }
        break;

    case 'PUT':
    case 'PATCH':
        $data = json_decode(file_get_contents("php://input"));
        $album->album_id = $data->album_id;
        $album->title = $data->title;
        $album->release = $data->release;
        $album->cover_img = $data->cover_img;
        $album->description = $data->description;

        if ($album->update()) {
            echo json_encode(['message' => 'Album updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Failed to update album']);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $album->album_id = $_GET['id'];
            if ($album->delete()) {
                echo json_encode(['message' => 'Album deleted successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => 'Failed to delete album']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'ID required']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>
