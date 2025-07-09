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

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true); // Get JSON data from POST request

// Extract variables from the received data
$oscaID = htmlspecialchars($data['oscaID']);
$firstName = htmlspecialchars($data['firstName']);
$middleName = htmlspecialchars($data['middleName']);
$lastName = htmlspecialchars($data['lastName']);
$suffix = htmlspecialchars($data['suffix']);
$gender = htmlspecialchars($data['gender']);
$birthday = htmlspecialchars($data['birthday']);
$age = htmlspecialchars($data['age']);
$civilStat = htmlspecialchars($data['civilStat']);
$placeOfBirth = htmlspecialchars($data['placeOfBirth']);
$contactNum = htmlspecialchars($data['contactNum']);
$address = htmlspecialchars($data['address']);
$religion = htmlspecialchars($data['religion']);
$citizenship = htmlspecialchars($data['citizenship']);
$educAttain = htmlspecialchars($data['educAttain']);
$tin = htmlspecialchars($data['tin']);
$philHealth = htmlspecialchars($data['philHealth']);
$dswdPensioner = htmlspecialchars($data['dswdPensioner']);
$livingArr = htmlspecialchars($data['livingArr']);
$psource = htmlspecialchars($data['psource']);
$psource_desc = htmlspecialchars($data['psource_desc']);
$contrNum = htmlspecialchars($data['contrNum']);
$regSupport = htmlspecialchars($data['regSupport']);
$img = isset($data['img']) ? htmlspecialchars($data['img']) : null; // Image path, optional

// SQL query to update the record in the database
$sql = "UPDATE scInfo 
        SET firstName = :firstName, middleName = :middleName, lastName = :lastName, suffix = :suffix,
            gender = :gender, birthday = :birthday, age = :age, civilStat = :civilStat, 
            placeOfBirth = :placeOfBirth, contactNum = :contactNum, address = :address, religion = :religion, 
            citizenship = :citizenship, educAttain = :educAttain, tin = :tin, philHealth = :philHealth, 
            dswdPensioner = :dswdPensioner, livingArr = :livingArr, psource = :psource, 
            psource_desc = :psource_desc, contrNum = :contrNum, regSupport = :regSupport, img = :img
        WHERE oscaID = :oscaID";

$stmt = $conn->prepare($sql);

// Bind the parameters to the prepared statement
$stmt->bindParam(':firstName', $firstName);
$stmt->bindParam(':middleName', $middleName);
$stmt->bindParam(':lastName', $lastName);
$stmt->bindParam(':suffix', $suffix);
$stmt->bindParam(':gender', $gender);
$stmt->bindParam(':birthday', $birthday);
$stmt->bindParam(':age', $age);
$stmt->bindParam(':civilStat', $civilStat);
$stmt->bindParam(':placeOfBirth', $placeOfBirth);
$stmt->bindParam(':contactNum', $contactNum);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':religion', $religion);
$stmt->bindParam(':citizenship', $citizenship);
$stmt->bindParam(':educAttain', $educAttain);
$stmt->bindParam(':tin', $tin);
$stmt->bindParam(':philHealth', $philHealth);
$stmt->bindParam(':dswdPensioner', $dswdPensioner);
$stmt->bindParam(':livingArr', $livingArr);
$stmt->bindParam(':psource', $psource);
$stmt->bindParam(':psource_desc', $psource_desc);
$stmt->bindParam(':contrNum', $contrNum);
$stmt->bindParam(':regSupport', $regSupport);
$stmt->bindParam(':img', $img);
$stmt->bindParam(':oscaID', $oscaID);

// Execute the statement and return the result
if ($stmt->execute()) {
    echo json_encode(['status' => 1, 'message' => 'DSWD record updated successfully.']);
} else {
    echo json_encode(['status' => 0, 'message' => 'Failed to update DSWD record.']);
}
?>
