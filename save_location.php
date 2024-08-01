<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load database configuration
$config = include('db_config.php');

// Database configuration
$servername = $config['servername'];
$username   = $config['username'];
$password   = $config['password'];
$dbname     = $config['dbname'];

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get the JSON data from POST request
$data = json_decode(file_get_contents("php://input"), true);

// Validate JSON data
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["error" => "Invalid JSON data"]);
    $conn->close();
    exit();
}

$latitude = $data['latitude'];
$longitude = $data['longitude'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO locations (latitude, longitude) VALUES (?, ?)");
if ($stmt === false) {
    echo json_encode(["error" => "Prepare failed: " . $conn->error]);
    $conn->close();
    exit();
}

$stmt->bind_param("dd", $latitude, $longitude);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(["message" => "Location saved successfully!"]);
} else {
    echo json_encode(["error" => "Execute failed: " . $stmt->error]);
}

// Close connections
$stmt->close();
$conn->close();
?>
