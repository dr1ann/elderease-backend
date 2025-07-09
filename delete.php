
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

$data = json_decode(file_get_contents("php://input"), true); // Get JSON data from POST request

// Extract oscaID from the request
$oscaID = htmlspecialchars($data['oscaID']);

// SQL to delete the record
$sql = "DELETE FROM scInfo WHERE oscaID = :oscaID";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':oscaID', $oscaID);

if ($stmt->execute()) {
    echo json_encode(['status' => 1, 'message' => 'Record deleted successfully.']);
} else {
    echo json_encode(['status' => 0, 'message' => 'Failed to delete the record.']);
}
?>
