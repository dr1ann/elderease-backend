<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include 'DbConnect.php';
$objDb = new DbConnect;
$conn = $objDb->connect();

$method = $_SERVER["REQUEST_METHOD"];

switch($method) {
    case "GET":
        $sql = "SELECT oscaID, username FROM citizen";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        if (isset($path[3]) && is_numeric($path[3])) {
            $sql .= " WHERE oscaID = :oscaID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':oscaID', $path[3]);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode($user);
        } else {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($users);
        }
        break;

    case "POST":
        // Check if this is a login request or a registration request
        $user = json_decode(file_get_contents('php://input'));
        if (isset($user->action) && $user->action === 'login') {
            // Login logic
            $sql = "SELECT oscaID, username, hashpassword FROM citizen WHERE username = :username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $user->username);
            $stmt->execute();
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser && password_verify($user->password, $existingUser['hashpassword'])) {
                $response = [
                    'status' => 1,
                    'message' => 'Login successful.',
                    'user' => [
                        'oscaID' => $existingUser['oscaID'],
                        'username' => $existingUser['username']
                    ]
                ];
            } else {
                $response = ['status' => 0, 'message' => 'Invalid username or password.'];
            }
            echo json_encode($response);
        } else {
            // Registration logic
            $sql = "INSERT INTO citizen (username, hashpassword) VALUES (:username, :hashpassword)";
            $stmt = $conn->prepare($sql);
            $hashpassword = password_hash($user->hashpassword, PASSWORD_BCRYPT);

            $stmt->bindParam(':username', $user->username);
            $stmt->bindParam(':hashpassword', $hashpassword);

            if ($stmt->execute()) {
                $response = ['status' => 1, 'message' => 'Record created successfully.'];
            } else {
                $response = ['status' => 0, 'message' => 'Failed to create record.'];
            }
            echo json_encode($response);
        }
        break;

    case "PUT":
        $user = json_decode(file_get_contents('php://input'));
        $sql = "UPDATE citizen SET username = :username, hashpassword = :hashpassword, updated_at = NOW() WHERE oscaID = :oscaID";
        $stmt = $conn->prepare($sql);
        $hashpassword = password_hash($user->hashpassword, PASSWORD_BCRYPT);

        $stmt->bindParam(':oscaID', $user->oscaID);
        $stmt->bindParam(':username', $user->username);
        $stmt->bindParam(':hashpassword', $hashpassword);

        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record updated successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to update record.'];
        }
        echo json_encode($response);
        break;

    case "DELETE":
        $sql = "DELETE FROM citizen WHERE oscaID = :oscaID";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':oscaID', $path[3]);

        if ($stmt->execute()) {
            $response = ['status' => 1, 'message' => 'Record deleted successfully.'];
        } else {
            $response = ['status' => 0, 'message' => 'Failed to delete record.'];
        }
        echo json_encode($response);
        break;
}
?>
