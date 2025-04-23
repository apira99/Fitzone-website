<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'fitzone';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if email and password are set
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Query database for clients
        $query_clients = "SELECT * FROM clients WHERE email = ?";
        $stmt_clients = $conn->prepare($query_clients);
        $stmt_clients->bind_param("s", $email);
        $stmt_clients->execute();
        $result_clients = $stmt_clients->get_result();

        if ($result_clients->num_rows > 0) {
            $row = $result_clients->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user'] = $row['email']; // Store email for session
                header("Location: profile.php");
                exit();
            } else {
                // Redirect to error page with error message
                header("Location: error.php?error=Invalid email or password.");
                exit();
            }
        } else {
            // If no match found
            header("Location: error.php?error=Invalid email or password.");
            exit();
        }
    } else {
        // Handle missing form data
        header("Location: error.php?error=Both email and password are required.");
        exit();
    }
}
?>
