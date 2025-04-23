<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch queries
$sql = "SELECT id, name, email, query, submitted_at, response, responded_at FROM queries";
$result = $conn->query($sql);

$queries = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $queries[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($queries);

$conn->close();
?>
