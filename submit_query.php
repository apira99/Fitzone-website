<?php
// Database connection details (adjust based on your environment)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitzone"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process the form when it is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $query = mysqli_real_escape_string($conn, $_POST['query']);

    // Simple validation (you can extend it as needed)
    if (empty($name) || empty($email) || empty($query)) {
        echo "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
    } else {
        // Prepare the SQL query to insert the form data into the database
        $sql = "INSERT INTO queries (name, email, query) VALUES ('$name', '$email', '$query')";

        // Check if the query was successful
        if ($conn->query($sql) === TRUE) {
            header("Location: query_success.html");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
