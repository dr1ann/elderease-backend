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

    // Check if all the necessary fields are set
    if (empty($_POST['firstname']) || empty($_POST['middlename']) || empty($_POST['surname']) || empty($_POST['birthday']) || empty($_POST['age']) || empty($_POST['gender']) || empty($_POST['contact_number']) || empty($_POST['role']) || empty($_POST['address'])) {
        echo json_encode(['status' => 0, 'message' => 'All fields are required.']);
        exit();
    }

    // Sanitize the data to avoid SQL injection
    $firstname = htmlspecialchars($_POST['firstname']);
    $middlename = htmlspecialchars($_POST['middlename']);
    $surname = htmlspecialchars($_POST['surname']);
    $birthday = htmlspecialchars($_POST['birthday']); // Format: YYYY-MM-DD
    $age = htmlspecialchars($_POST['age']);
    $gender = htmlspecialchars($_POST['gender']);
    $contact_number = htmlspecialchars($_POST['contact_number']);
    $role = htmlspecialchars($_POST['role']);
    $address = htmlspecialchars($_POST['address']);
    
    // Handle Image Upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Check if the uploaded file is an image
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imageSize = $_FILES['image']['size'];
        $imageType = mime_content_type($imageTmpName);

        // Validate file type (e.g., only allow JPEG, PNG)
        if (in_array($imageType, ['image/jpeg', 'image/png'])) {
            // Move the uploaded file to a permanent directory
            $targetDir = 'uploads/';
            $targetFile = $targetDir . $imageName;

            if (move_uploaded_file($imageTmpName, $targetFile)) {
                $image = $targetFile; // Store the file path in the database
            } else {
                echo json_encode(['status' => 0, 'message' => 'Failed to upload image.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Only JPEG and PNG images are allowed.']);
            exit();
        }
    }

    // Prepare the SQL query to insert a new admin
    $sql = "INSERT INTO admins (firstname, middlename, surname, birthday, age, gender, contact_number, role, address, image)
            VALUES (:firstname, :middlename, :surname, :birthday, :age, :gender, :contact_number, :role, :address, :image)";

    $stmt = $conn->prepare($sql);

    // Bind the parameters to the query
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':middlename', $middlename);
    $stmt->bindParam(':surname', $surname);
    $stmt->bindParam(':birthday', $birthday);
    $stmt->bindParam(':age', $age);
    $stmt->bindParam(':gender', $gender);
    $stmt->bindParam(':contact_number', $contact_number);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':image', $image); // Bind the image file path

    // Execute the query and check if the insertion is successful
    if ($stmt->execute()) {
        echo json_encode([
            'status' => 1,
            'message' => 'Admin registered successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 0,
            'message' => 'Failed to register admin.'
        ]);
    }
}
?>
