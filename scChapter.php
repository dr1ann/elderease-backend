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

// Handle GET request
if ($_SERVER["REQUEST_METHOD"] === 'GET') {
    // SQL to get all records where type is 'scChapter'
    $sql = "SELECT oscaID, contrNum, firstName, middleName, lastName, gender, age, birthday, civilStat, placeOfBirth, contactNum, address, archived, img 
            FROM scInfo 
            WHERE type = 'scChapter'";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo json_encode(['status' => 1, 'data' => $results]);
        } else {
            echo json_encode(['status' => 0, 'message' => 'No SC Chapter records found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request method. Only GET is allowed.']);
}
?>
