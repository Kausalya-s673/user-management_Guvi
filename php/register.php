<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
    $username = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password_raw = trim($_POST['password']);
    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

    // Server-side validations
    if (empty($username) || empty($email) || empty($password_raw)) {
        echo json_encode(["success" => false, "message" => "Error: All fields are required."]);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Error: Invalid email format."]);
        exit();
    }

    if (strlen($password_raw) < 6) {
        echo json_encode(["success" => false, "message" => "Error: Password must be at least 6 characters long."]);
        exit();
    }

    // Check for duplicate username
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Error: Username already taken."]);
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    // Check for duplicate email
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Error: Email already registered."]);
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("sss", $username, $email, $password_hashed);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}

$conn->close();
?>
