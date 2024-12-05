<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Error logging for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', 'php_error.log'); // Writes errors to a file in the current directory

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Database connection details
$host = 'localhost';
$username = 'root';
$password = ''; // Default password for XAMPP
$dbname = 'registration';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed.'])); 
}

// Handle different request methods
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle POST requests (same as before)
    $fullName = sanitizeInput($_POST['fullName']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $age = sanitizeInput($_POST['age']);
    $address = sanitizeInput($_POST['address']);

    $errors = [];
    if (empty($fullName)) $errors[] = "Name is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";
    if (empty($phone)) $errors[] = "Phone number is required";
    if (empty($age) || $age < 18) $errors[] = "Age must be 18 or above";

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO users (fullName, email, phone, age, address) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed.']);
            exit();
        }

        $stmt->bind_param("sssis", $fullName, $email, $phone, $age, $address);

        if ($stmt->execute()) {
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'fullName' => $fullName,
                    'email' => $email,
                    'phone' => $phone,
                    'age' => $age,
                    'address' => $address
                ]
            ]);
        } else {
            error_log("Execution failed: " . $stmt->error);
            echo json_encode(['status' => 'error', 'message' => 'Error inserting data.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
    }
    $conn->close();
    exit();
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Handle GET requests (fetch and return data)
    $result = $conn->query("SELECT * FROM users");

    if ($result->num_rows > 0) {
        $users = [];
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode(['status' => 'success', 'data' => $users]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No users found']);
    }
    $conn->close();
    exit();
} else {
    // Handle other request methods
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method. Use POST to submit data or GET to fetch data.']);
    exit();
}
?>
