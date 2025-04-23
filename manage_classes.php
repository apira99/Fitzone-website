<?php
session_start();
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'fitzone';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure user is logged in as manager
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'manager') {
    header("Location: index.html");
    exit();
}

// Handle adding a new class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_class'])) {
    $class_name = $_POST['class_name'];
    $description = $_POST['description'];

    $query = "INSERT INTO classes (class_name, description) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $class_name, $description);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_classes.php");
        exit();
    } else {
        echo "Error adding class.";
    }
}

// Handle deleting a class
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    $delete_query = "DELETE FROM classes WHERE class_id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        $delete_stmt->close();
        header("Location: manage_classes.php");
        exit();
    } else {
        echo "Error deleting class.";
    }
}

// Fetch and display existing classes
function get_classes() {
    global $conn;
    $query = "SELECT * FROM classes";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($class = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($class['class_name']) . "</td>
                    <td>" . htmlspecialchars($class['description']) . "</td>
                    <td><a href='manage_classes.php?delete_id=" . $class['class_id'] . "'><button class='delete'>Delete</button></a></td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No classes found</td></tr>";
    }
}
?>
