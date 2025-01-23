<?php
require '../vendor/autoload.php';
$redis = new Predis\Client();

$uri = "mongodb+srv://kausi_673:kausi_673@cluster0.p3bmmo2.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

try {
 
    $client = new MongoDB\Client($uri);
    $collection = $client->user_management->profiles;
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'];
    $profile = $collection->findOne(['email' => $email]);

    if ($profile) {
        if (isset($profile['profilePicture']) && $profile['profilePicture'] instanceof MongoDB\BSON\Binary) {
            $profile['profilePicture'] = base64_encode($profile['profilePicture']->getData());
        }
        echo json_encode($profile);
    } else {
        echo json_encode(null);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $profilePicture = null;

    if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../assets/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        $targetFile = $targetDir . basename($_FILES['profilePicture']['name']);
        if (move_uploaded_file($_FILES['profilePicture']['tmp_name'], $targetFile)) {
            $profilePicture = $targetFile;
        } else {
            echo "Failed to move uploaded file.";
            exit;
        }
    }

    if ($profilePicture !== null) {
        $profilePictureBinary = new MongoDB\BSON\Binary(file_get_contents($profilePicture), MongoDB\BSON\Binary::TYPE_GENERIC);
    } else {
        $profilePictureBinary = null;
    }

    $updateResult = $collection->updateOne(
        ['email' => $email],
        ['$set' => [
            'age' => $age,
            'dob' => $dob,
            'contact' => $contact,
            'profilePicture' => $profilePictureBinary
        ]],
        ['upsert' => true]
    );

    if ($updateResult->getModifiedCount() > 0 || $updateResult->getUpsertedCount() > 0) {
        echo "Profile updated successfully";
    } else {
        echo "No changes made to the profile.";
    }
}
?>
