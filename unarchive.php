<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");

include 'DbConnect.php';

$objDb = new DbConnect();
$conn = $objDb->connect();

$method = $_SERVER["REQUEST_METHOD"];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (empty($data['oscaID'])) {
        echo json_encode(['status' => 0, 'message' => 'OSCA ID is required']);
        exit();
    }

    $oscaID = $data['oscaID'];

    // Update the record to set it as archived
    $sql = "UPDATE scInfo SET archived = 0 WHERE oscaID = :oscaID";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':oscaID', $oscaID);

    try {
        if ($stmt->execute()) {
            echo json_encode(['status' => 1, 'message' => 'Record unarchived successfully']);
        } else {
            echo json_encode(['status' => 0, 'message' => 'Failed to archive record']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request method']);
}
