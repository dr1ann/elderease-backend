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

$method = $_SERVER["REQUEST_METHOD"];

if ($method === 'POST') {

    // Validate that 'type' is provided
    if (empty($_POST['type'])) {
        $responseArray = ['status' => 0, 'message' => 'Type (scChapter or dswd) is required.'];
        // Log response to a file or output it
        error_log(print_r($responseArray, true));
        echo json_encode($responseArray);
        exit();
    }

    $type = $_POST['type'];

    // Validate fields for SC Chapter
    if ($type === 'scChapter') {
        // Required fields for SC Chapter
        $fields = [
            'oscaID', 'contrNum', 'firstName', 'lastName', 'middleName', 'suffix', 'birthday_month', 
            'birthday_day', 'birthday_year', 'age', 'civilStat', 'placeOfBirth', 'gender', 'contactNum', 'address'
        ];

        // Check that all fields are provided
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                $responseArray = ['status' => 0, 'message' => "Field $field is required for SC Chapter."];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
                exit();
            }
        }

        // Prepare SC Chapter data
        $oscaID = htmlspecialchars($_POST['oscaID']);
        $contrNum = htmlspecialchars($_POST['contrNum']);
        $firstName = htmlspecialchars($_POST['firstName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $middleName = htmlspecialchars($_POST['middleName']);
        $suffix = htmlspecialchars($_POST['suffix']);
        $birthday_month = htmlspecialchars($_POST['birthday_month']);
        $birthday_day = htmlspecialchars($_POST['birthday_day']);
        $birthday_year = htmlspecialchars($_POST['birthday_year']);
        $age = htmlspecialchars($_POST['age']);
        $civilStat = htmlspecialchars($_POST['civilStat']);
        $placeOfBirth = htmlspecialchars($_POST['placeOfBirth']);  // Fixed typo
        $gender = htmlspecialchars($_POST['gender']);
        $contactNum = htmlspecialchars($_POST['contactNum']);
        $address = htmlspecialchars($_POST['address']);
        $date_created = date('Y-m-d H:i:s');
        $img = null;

        // Combine birthday fields into one date
        $birthday = "$birthday_year-$birthday_month-$birthday_day";

        // Check if the oscaID already exists in the database
        $checkOscaID = "SELECT COUNT(*) FROM scInfo WHERE oscaID = :oscaID";
        $stmt = $conn->prepare($checkOscaID);
        $stmt->bindParam(':oscaID', $oscaID);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        if ($result > 0) {
            $responseArray = ['status' => 0, 'message' => 'Error: The provided OSCA ID already exists. Please use a unique OSCA ID.'];
            // Log response to a file or output it
            error_log(print_r($responseArray, true));
            echo json_encode($responseArray);
            exit();
        }

        // Handle image upload (optional)
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $imageTmpName = $_FILES['img']['tmp_name'];
            $imageName = basename($_FILES['img']['name']);
            $imageType = mime_content_type($imageTmpName);

            if (in_array($imageType, ['image/jpeg', 'image/png'])) {
                $targetDir = 'C:/xampp/htdocs/seniorpayment/profile/';
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $targetFile = $targetDir . $imageName;
                if (move_uploaded_file($imageTmpName, $targetFile)) {
                    $img = "/profile/" . $imageName;
                } else {
                    $responseArray = ['status' => 0, 'message' => 'Failed to upload image for SC Chapter.'];
                    // Log response to a file or output it
                    error_log(print_r($responseArray, true));
                    echo json_encode($responseArray);
                    exit();
                }
            } else {
                $responseArray = ['status' => 0, 'message' => 'Only JPEG and PNG images are allowed for SC Chapter.'];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
                exit();
            }
        }

        // SQL for inserting into scInfo (using the oscaID provided by the front-end)
        $sql = "INSERT INTO scInfo 
                (oscaID, contrNum,firstName, middleName, lastName, suffix, gender, birthday, age, civilStat , placeOfBirth, contactNum, address, date_created, img, type) 
                VALUES 
                (:oscaID, :contrNum, :firstName, :middleName, :lastName, :suffix, :gender, :birthday, :age, :civilStat , :placeOfBirth, :contactNum, :address, :date_created, :img, :type)";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':oscaID', $oscaID);
            $stmt->bindParam(':contrNum', $contrNum);
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
            $stmt->bindParam(':date_created', $date_created);
            $stmt->bindParam(':img', $img);
            $stmt->bindParam(':type', $type);

            if ($stmt->execute()) {
                $responseArray = ['status' => 1, 'message' => 'SC Chapter added successfully.'];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
            } else {
                $responseArray = ['status' => 0, 'message' => 'Failed to add SC Chapter.'];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
            }
        } catch (PDOException $e) {
            $responseArray = ['status' => 0, 'message' => 'SQL Error: ' . $e->getMessage()];
            // Log response to a file or output it
            error_log(print_r($responseArray, true));
            echo json_encode($responseArray);
        }

    }
    
    else if ($type === 'dswd') {

        // Required fields for DSWD (All fields for scInfo including extra ones for dswd)
        $fields = [
            'oscaID', 'firstName', 'lastName', 'middleName', 'suffix', 'birthday_month', 
            'birthday_day', 'birthday_year', 'age', 'civilStat', 'placeOfBirth' ,'gender', 'contactNum', 'address',
            'religion', 'citizenship', 'educAttain', 'tin', 'philHealth', 'dswdPensioner', 'livingArr', 
            'psource', 'psource_desc', 'regSupport'
        ];

        // Check that all fields are provided for DSWD
        foreach ($fields as $field) {
            if (empty($_POST[$field])) {
                $responseArray = ['status' => 0, 'message' => "Field $field is required for DSWD."];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
                exit();
            }
        }

        // Prepare DSWD data
        $oscaID = htmlspecialchars($_POST['oscaID']);
        $firstName = htmlspecialchars($_POST['firstName']);
        $lastName = htmlspecialchars($_POST['lastName']);
        $middleName = htmlspecialchars($_POST['middleName']);
        $suffix = htmlspecialchars($_POST['suffix']);
        $birthday_month = htmlspecialchars($_POST['birthday_month']);
        $birthday_day = htmlspecialchars($_POST['birthday_day']);
        $birthday_year = htmlspecialchars($_POST['birthday_year']);
        $age = htmlspecialchars($_POST['age']);
        $civilStat = htmlspecialchars($_POST['civilStat']);
        $placeOfBirth = htmlspecialchars($_POST['placeOfBirth']);
        $gender = htmlspecialchars($_POST['gender']);
        $contactNum = htmlspecialchars($_POST['contactNum']);
        $address = htmlspecialchars($_POST['address']);
        $religion = htmlspecialchars($_POST['religion']);
        $citizenship = htmlspecialchars($_POST['citizenship']);
        $educAttain = htmlspecialchars($_POST['educAttain']);
        $tin = htmlspecialchars($_POST['tin']);
        $philHealth = htmlspecialchars($_POST['philHealth']);
        $dswdPensioner = htmlspecialchars($_POST['dswdPensioner']);
        $livingArr = htmlspecialchars($_POST['livingArr']);
        $psource = htmlspecialchars($_POST['psource']);
        $psource_desc = htmlspecialchars($_POST['psource_desc']);
        $regSupport = htmlspecialchars($_POST['regSupport']);
        $date_created = date('Y-m-d H:i:s');
        $img = null;

        // Combine birthday fields into one date
        $birthday = "$birthday_year-$birthday_month-$birthday_day";

        // Check if the oscaID already exists in the database for DSWD
        $checkOscaID = "SELECT COUNT(*) FROM scInfo WHERE oscaID = :oscaID";
        $stmt = $conn->prepare($checkOscaID);
        $stmt->bindParam(':oscaID', $oscaID);
        $stmt->execute();
        $result = $stmt->fetchColumn();

        if ($result > 0) {
            $responseArray = ['status' => 0, 'message' => 'Error: The provided OSCA ID already exists for DSWD. Please use a unique OSCA ID.'];
            // Log response to a file or output it
            error_log(print_r($responseArray, true));
            echo json_encode($responseArray);
            exit();
        }

        // Handle image upload (optional)
        if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
            $imageTmpName = $_FILES['img']['tmp_name'];
            $imageName = basename($_FILES['img']['name']);
            $imageType = mime_content_type($imageTmpName);

            if (in_array($imageType, ['image/jpeg', 'image/png'])) {
                $targetDir = 'C:/xampp/htdocs/seniorpayment/profile/';
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $targetFile = $targetDir . $imageName;
                if (move_uploaded_file($imageTmpName, $targetFile)) {
                    $img = "/profile/" . $imageName;
                } else {
                    $responseArray = ['status' => 0, 'message' => 'Failed to upload image for DSWD.'];
                    // Log response to a file or output it
                    error_log(print_r($responseArray, true));
                    echo json_encode($responseArray);
                    exit();
                }
            } else {
                $responseArray = ['status' => 0, 'message' => 'Only JPEG and PNG images are allowed for DSWD.'];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
                exit();
            }
        }

        // SQL for inserting into scInfo for DSWD
        $sql = "INSERT INTO scInfo 
                (oscaID, firstName, middleName, lastName, suffix, gender, birthday, age, civilStat , placeOfBirth, contactNum, address, religion, citizenship, educAttain, tin, philHealth, dswdPensioner, livingArr, psource, psource_desc, regSupport, date_created, img, type) 
                VALUES 
                (:oscaID, :firstName, :middleName, :lastName, :suffix, :gender, :birthday, :age, :civilStat , :placeOfBirth, :contactNum, :address, :religion, :citizenship, :educAttain, :tin, :philHealth, :dswdPensioner, :livingArr, :psource, :psource_desc, :regSupport, :date_created, :img, :type)";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':oscaID', $oscaID);
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
            $stmt->bindParam(':regSupport', $regSupport);
            $stmt->bindParam(':date_created', $date_created);
            $stmt->bindParam(':img', $img);
            $stmt->bindParam(':type', $type);

            if ($stmt->execute()) {
                $responseArray = ['status' => 1, 'message' => 'DSWD Data added successfully.'];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
            } else {
                $responseArray = ['status' => 0, 'message' => 'Failed to add DSWD Data.'];
                // Log response to a file or output it
                error_log(print_r($responseArray, true));
                echo json_encode($responseArray);
            }
        } catch (PDOException $e) {
            $responseArray = ['status' => 0, 'message' => 'SQL Error: ' . $e->getMessage()];
            // Log response to a file or output it
            error_log(print_r($responseArray, true));
            echo json_encode($responseArray);
        }

    }

} else {
    $responseArray = ['status' => 0, 'message' => 'Invalid request method. Please use POST.'];
    // Log response to a file or output it
    error_log(print_r($responseArray, true));
    echo json_encode($responseArray);
}
?>
