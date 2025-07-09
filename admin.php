<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

// Set content type to JSON
header("Content-Type: application/json");

include 'DbConnect.php';

$objDb = new DbConnect();
$conn = $objDb->connect();

$method = $_SERVER["REQUEST_METHOD"];

if ($method === 'POST') {
    // Check if all the necessary fields are set (excluding oscaID)
    if (empty($_POST['firstname']) || empty($_POST['middlename']) || empty($_POST['surname']) || empty($_POST['birthday']) || empty($_POST['age']) || empty($_POST['gender']) || empty($_POST['contact_number']) || empty($_POST['role']) || empty($_POST['address']) || empty($_POST['username']) || empty($_POST['password'])) {
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
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $confpassword = htmlspecialchars($_POST['confpassword']); // Not used for DB, just validation

    // Validate password and confirm password match
    if ($password !== $confpassword) {
        echo json_encode(['status' => 0, 'message' => 'Password and confirm password do not match.']);
        exit();
    }

    // Hash the password before saving to the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Handle Image Upload (optional)
    // $image = null;

    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        echo json_encode(['status' => 0, 'message' => 'Image is required.']);
        exit();
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Check if the uploaded file is an image
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imageSize = $_FILES['image']['size'];
        $imageType = mime_content_type($imageTmpName);

        // Validate file type (e.g., only allow JPEG, PNG)
        if (in_array($imageType, ['image/jpeg', 'image/png'])) {
            // Set the target directory to store the uploaded image
            $targetDir = 'C:/xampp/htdocs/seniorpayment/profile/';
            
            // Ensure the upload directory exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);  // Ensure the upload directory exists (with proper permissions)
            }
            
            // Target file path where the image will be stored
            $targetFile = $targetDir . $imageName;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($imageTmpName, $targetFile)) {
                // Set the image to a public URL relative to the document root
                $image = "/profile/" . $imageName;
            } else {
                echo json_encode(['status' => 0, 'message' => 'Failed to upload image.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 0, 'message' => 'Only JPEG and PNG images are allowed.']);
            exit();
        }
    }

    // Prepare the SQL query to insert a new admin (including username and hashed password)
    $sql = "INSERT INTO admins (firstname, middlename, surname, birthday, age, gender, contact_number, role, address, image, username, password)
            VALUES (:firstname, :middlename, :surname, :birthday, :age, :gender, :contact_number, :role, :address, :image, :username, :password)";

    try {
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
        $stmt->bindParam(':username', $username); // Bind the username
        $stmt->bindParam(':password', $hashedPassword); // Bind the hashed password

        // Execute the query
        if ($stmt->execute()) {
            // Get the last inserted ID (oscaID) from the insert query
            $oscaID = $conn->lastInsertId();

            // Prepare the response with the oscaID and other admin data
            $response = [
                'status' => 1,
                'message' => 'Admin registered successfully.',
                'data' => [
                    'oscaID' => $oscaID,  // Send the auto-generated oscaID back to the frontend
                    'firstname' => $firstname,
                    'middlename' => $middlename,
                    'surname' => $surname,
                    'birthday' => $birthday,
                    'age' => $age,
                    'gender' => $gender,
                    'contact_number' => $contact_number,
                    'role' => $role,
                    'address' => $address,
                    'image' => $image,
                    'username' => $username
                ]
            ];

            // Send the response to the frontend
            echo json_encode($response);
        } else {
            $errorInfo = $stmt->errorInfo();
            echo json_encode([ 
                'status' => 0,
                'message' => 'Failed to register admin.',
                'error' => $errorInfo
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 0,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 0,
        'message' => 'Invalid request method. Only POST is allowed.'
    ]);
}
?>
