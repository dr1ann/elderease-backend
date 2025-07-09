<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");
header("Content-Type: application/json");

include 'DbConnect.php'; // Database connection file
$objDb = new DbConnect();
$conn = $objDb->connect();

$data = json_decode(file_get_contents("php://input"), true); // Get JSON data from POST request

// Extract variables from the received data
$oscaID = htmlspecialchars($data['oscaID']);
$firstName = htmlspecialchars($data['firstName']);
$middleName = htmlspecialchars($data['middleName']);
$lastName = htmlspecialchars($data['lastName']);
$gender = htmlspecialchars($data['gender']);
$age = htmlspecialchars($data['age']);
$birthday = htmlspecialchars($data['birthday']);
$civilStat = htmlspecialchars($data['civilStat']);
$placeOfBirth = htmlspecialchars($data['placeOfBirth']);
$contactNum = htmlspecialchars($data['contactNum']);
$address = htmlspecialchars($data['address']);

// SQL query to update the record in the database
$sql = "UPDATE scInfo 
        SET firstName = :firstName, middleName = :middleName, lastName = :lastName, 
            gender = :gender, age = :age, birthday = :birthday, civilStat = :civilStat, 
            placeOfBirth = :placeOfBirth, contactNum = :contactNum, address = :address 
        WHERE oscaID = :oscaID";

$stmt = $conn->prepare($sql);

// Bind the parameters to the prepared statement
$stmt->bindParam(':firstName', $firstName);
$stmt->bindParam(':middleName', $middleName);
$stmt->bindParam(':lastName', $lastName);
$stmt->bindParam(':gender', $gender);
$stmt->bindParam(':age', $age);
$stmt->bindParam(':birthday', $birthday);
$stmt->bindParam(':civilStat', $civilStat);
$stmt->bindParam(':placeOfBirth', $placeOfBirth);
$stmt->bindParam(':contactNum', $contactNum);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':oscaID', $oscaID);

if ($stmt->execute()) {
    echo json_encode(['status' => 1, 'message' => 'Record updated successfully.']);
} else {
    echo json_encode(['status' => 0, 'message' => 'Failed to update the record.']);
}
?>
