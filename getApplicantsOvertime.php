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
    // SQL to get the monthly count of applicants based on date_created for both types
    $sql = "SELECT 
                DATE_FORMAT(date_created, '%Y-%m-%d') AS day,  -- Grouping by exact date (YYYY-MM-DD)
                type, 
                COUNT(*) AS count 
            FROM scinfo
            WHERE type IN ('scChapter', 'dswd')  -- Fetching both types
            GROUP BY day, type  -- Group by both day and type
            ORDER BY day DESC";  // Ordering by the most recent day first

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            echo json_encode(['status' => 1, 'data' => $results]);
        } else {
            echo json_encode(['status' => 0, 'message' => 'No records found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 0, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 0, 'message' => 'Invalid request method. Only GET is allowed.']);
}
?>
