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

    // Get the posted data (username and password)
    $inputData = json_decode(file_get_contents('php://input'), true);

    if (empty($inputData['username']) || empty($inputData['password'])) {
        echo json_encode(['status' => 0, 'message' => 'Username and password are required.']);
        exit();
    }

    // Sanitize input to prevent SQL injection
    $username = htmlspecialchars($inputData['username']);
    $password = htmlspecialchars($inputData['password']);

    // Prepare the SQL query to check for the username
    $sql = "SELECT * FROM admins WHERE username = :username";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    
    // Execute the query and fetch the result
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        // Verify the password using password_verify()
        if (password_verify($password, $admin['password'])) {
            echo json_encode([
                'status' => 1,
                'message' => 'Login successful',
                'admin_data' => [
                    'oscaID' => $admin['oscaID'],
                    'firstname' => $admin['firstname'],
                    'middlename' => $admin['middlename'],
                    'surname' => $admin['surname'],
                    'role' => $admin['role'],
                    'image' => $admin['image'] ? $admin['image'] : null,
                ]
            ]);
        } else {
            echo json_encode(['status' => 0, 'message' => 'Invalid username or password.']);
        }
    } else {
        echo json_encode(['status' => 0, 'message' => 'Invalid username or password.']);
    }
}
?>
