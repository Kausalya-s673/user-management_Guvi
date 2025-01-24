<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../vendor/autoload.php';


$redis = new Predis\Client([
    'scheme' => 'tcp',
    'host'   => 'red-cu98d2aj1k6c73f64t20', 
    'port'   => 6379                        
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
        echo json_encode(["success" => false, "message" => "Error: All fields are required."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
    
        echo json_encode(["success" => false, "message" => "User not registered. Please register first."]);
        $stmt->close();
        $conn->close();
        exit();
    }


    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if (password_verify($password_raw, $hashed_password)) {
        session_start();
        $sessionId = session_id();
        $redis->set($sessionId, $email);
        $redis->expire($sessionId, 3600); 
        echo json_encode(["success" => true, "sessionId" => $sessionId]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
