<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';

// Redis connection configuration
$redis = new Predis\Client([
    'scheme' => 'tcp',
    'host'   => 'red-cu98d2aj1k6c73f64t20', // Use the hostname part of your internal URL
    'port'   => 6379                        // The port part of your internal URL
]);

$servername = "mysql-139adef5-kausalyas673.l.aivencloud.com"; 
$username = "avnadmin";                 
$password = "AVNS_qTmZSaWJRKOWR7fdfCs";                  
$dbname = "user_management";                   
$port = 28464;                              

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password_raw = trim($_POST['password']);

    if (empty($email) || empty($password_raw)) {
        die("Error: All fields are required.");
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (password_verify($password_raw, $hashed_password)) {
        session_start();
        $sessionId = session_id();
        $redis->set($sessionId, $email);
        $redis->expire($sessionId, 3600); // Session expires after 1 hour
        echo json_encode(["success" => true, "sessionId" => $sessionId]);
    } else {
        echo json_encode(["success" => false]);
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
