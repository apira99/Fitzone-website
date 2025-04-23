<?php
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone";

// Create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check if the connection was successful
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Get the response and query ID from the form (POST request)
$response = $_POST['response'];  // Response from manager
$id = $_POST['id'];  // The ID of the query being responded to

// Check if response and ID are provided
if (!empty($response) && !empty($id)) {
    // Prepare the SQL query
    $sql = "UPDATE queries SET response = ?, responded_at = NOW() WHERE id = ?";
    
    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters to the query
        $stmt->bind_param("si", $response, $id);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Response saved successfully!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error saving response: " . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Response or query ID is missing!"]);
}

// Close the database connection
$conn->close();
?>
