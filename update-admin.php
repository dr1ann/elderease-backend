<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'DbConnect.php';

$objDb = new DbConnect();
$conn = $objDb->connect();

$method = $_SERVER["REQUEST_METHOD"];

// Set content type to JSON
header("Content-Type: application/json");

if ($method === 'POST') {

    // Get the posted data (username, password, oscaID)
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check for empty input data or missing OSCA ID
    if (empty($inputData['oscaID'])) {
        echo json_encode(['status' => 0, 'message' => 'OSCA ID is required.']);
        exit();
    }

    // Sanitize input to prevent SQL injection
    $oscaID = htmlspecialchars($inputData['oscaID']);
    $username = isset($inputData['username']) ? htmlspecialchars($inputData['username']) : null;
    $password = isset($inputData['password']) ? htmlspecialchars($inputData['password']) : null;

    // Prepare SQL for updating fields only if they are not empty
    $updateQuery = "UPDATE admins SET ";

    $fieldsToUpdate = [];
    if ($username) {
        $fieldsToUpdate[] = "username = :username";
    }
    if ($password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $fieldsToUpdate[] = "password = :password";
    }

    if (count($fieldsToUpdate) > 0) {
        $updateQuery .= implode(", ", $fieldsToUpdate) . " WHERE oscaID = :oscaID";

        // Prepare and execute the update statement
        $stmt = $conn->prepare($updateQuery);

        if ($username) {
            $stmt->bindParam(':username', $username);
        }
        if ($password) {
            $stmt->bindParam(':password', $hashedPassword);
        }
        $stmt->bindParam(':oscaID', $oscaID);

        if ($stmt->execute()) {
            echo json_encode(['status' => 1, 'message' => 'Account updated successfully']);
        } else {
            echo json_encode(['status' => 0, 'message' => 'Failed to update account']);
        }
    } else {
        echo json_encode(['status' => 0, 'message' => 'No fields to update']);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request method']);
}
?>
