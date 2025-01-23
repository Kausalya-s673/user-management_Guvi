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
  
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';


    $username = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password_raw = $_POST['password'];
    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

  
    if (empty($username) || empty($email) || empty($password_raw)) {
        die("Error: All fields are required.");
    }

    echo "Received Username: $username<br>";
    echo "Received Email: $email<br>";
    echo "Received Raw Password: $password_raw<br>";
    echo "Received Hashed Password: $password_hashed<br>";


    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $email, $password_hashed);

    if ($stmt->execute()) {
        echo "Registration successful<br>";
    } else {
        if ($stmt->errno === 1062) { // Duplicate entry error
            echo "Error: Email already registered.<br>";
        } else {
            echo "Error: " . $stmt->error . "<br>";
        }
    }

    echo "SQL Query: INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password_hashed')<br>";

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
