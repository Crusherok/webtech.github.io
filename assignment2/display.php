<?php
header('Content-Type: text/html; charset=UTF-8');
header("Access-Control-Allow-Origin: http://127.0.0.1:5500");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Error logging for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', 'php_error.log'); // Writes errors to a file in the current directory

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
    die("Database connection failed.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100%;
            margin: 20px 0;
        }
        th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        td {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">User Data</h2>

    <?php
    // Fetch users from the database
    $result = $conn->query("SELECT * FROM users");

    if ($result->num_rows > 0) {
        echo "<table class='table table-striped table-hover table-bordered'>";
        echo "<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Age</th><th>Address</th></tr></thead>";
        echo "<tbody>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['fullName']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['age']) . "</td>";
            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No users found.</p>";
    }

    // Close connection
    $conn->close();
    ?>

</div>

<!-- Bootstrap JS and dependencies (optional) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
