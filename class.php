<?php
session_start(); // Start the session for storing messages

$host = 'localhost'; // Change if not running locally
$db = 'fitzone';     // Replace with your database name
$user = 'root';      // Replace with your DB username
$pass = '';          // Replace with your DB password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Initialize variables
$class_name = $description = $trainer_id = "";
$edit_mode = false;

// Fetch all trainers for the dropdown
$trainersStmt = $pdo->query("SELECT trainer_id, name FROM trainers");
$trainers = $trainersStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle Add/Edit Class
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_name = $_POST['class_name'];
    $description = $_POST['description'];
    $trainer_id = $_POST['trainer_id'];

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Update class
        try {
            $stmt = $pdo->prepare("UPDATE classes SET class_name=?, description=?, trainer_id=? WHERE class_id=?");
            $stmt->execute([$class_name, $description, $trainer_id, $_POST['edit_id']]);
            $_SESSION['message'] = "Class updated successfully!";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error updating class: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    } else {
        // Add new class
        try {
            $stmt = $pdo->prepare("INSERT INTO classes (class_name, description, trainer_id) VALUES (?, ?, ?)");
            $stmt->execute([$class_name, $description, $trainer_id]);
            $_SESSION['message'] = "Class added successfully!";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Error adding class: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
    }
    header("Location: class.php");
    exit();
}

// Handle Delete Class
if (isset($_GET['delete'])) {
    $class_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM classes WHERE class_id=?");
        $stmt->execute([$class_id]);
        $_SESSION['message'] = "Class deleted successfully!";
        $_SESSION['message_type'] = "success";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting class: " . $e->getMessage();
        $_SESSION['message_type'] = "error";
    }
    header("Location: class.php");
    exit();
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $class_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE class_id=?");
    $stmt->execute([$class_id]);
    $class = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($class) {
        $class_name = $class['class_name'];
        $description = $class['description'];
        $trainer_id = $class['trainer_id'];
        $edit_mode = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="class.css">
    <style>
        /* Custom Confirmation Dialog */
        .confirm-dialog {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            width: 300px;
            padding: 20px;
            text-align: center;
            z-index: 1000;
        }

        .confirm-dialog button {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .confirm-dialog .confirm-yes {
            background-color: #dc3545;
            color: white;
        }

        .confirm-dialog .confirm-no {
            background-color: #6c757d;
            color: white;
        }

        /* Overlay */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Logout Button */
        .logout-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background-color: #0056b3;
        }

        /* Navigation Bar */
header {
    background: #333;
    color: #fff;
    padding: 1rem 0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
}

.navbar h1 {
    font-size: 1.5rem;
}

.navbar nav a {
    color: #fff;
    text-decoration: none;
    margin: 0 10px;
}

.navbar nav a:hover {
    text-decoration: underline;
}

.centered-title {
    text-align: center;
}

    </style>
</head>
<body>
<header>
        <div class="navbar">
            <h1>Manager Dashboard</h1>
            <nav>
                <a href="class.php">Manage classes</a>
                <a href="manager_appointments.php" >Manage appoinments</a>
                <a href="respond-queries.html" >Respond to Queries</a>
                <a href="index.html">Logout</a>
            </nav>
        </div>
    </header>

<h1 class="centered-title">Manage Gym Classes</h1>

<form method="post" action="">
    <input type="hidden" name="edit_id" value="<?= $edit_mode ? $_GET['edit'] : '' ?>">
    <label>Class Name</label>
    <input type="text" name="class_name" value="<?= $class_name ?>" required>
    <label>Description</label>
    <textarea name="description" required><?= $description ?></textarea>
    <label>Trainer</label>
    <select name="trainer_id" required>
        <option value="">Select Trainer</option>
        <?php foreach ($trainers as $trainer): ?>
            <option value="<?= $trainer['trainer_id'] ?>" <?= $trainer['trainer_id'] == $trainer_id ? 'selected' : '' ?>>
                <?= $trainer['name'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit"><?= $edit_mode ? 'Update Class' : 'Add Class' ?></button>
</form>

<table>
    <thead>
        <tr>
            <th>Class ID</th>
            <th>Class Name</th>
            <th>Description</th>
            <th>Trainer Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT classes.class_id, classes.class_name, classes.description, trainers.name as trainer_name
                             FROM classes
                             JOIN trainers ON classes.trainer_id = trainers.trainer_id");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['class_id']}</td>
                    <td>{$row['class_name']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['trainer_name']}</td>
                    <td>
                        <form method='get' action='' style='display:inline;'>
                            <input type='hidden' name='edit' value='{$row['class_id']}'>
                            <button type='submit' class='edit-btn'>Edit</button>
                        </form>
                        <form method='get' action='' class='delete-form'>
                            <input type='hidden' name='delete' value='{$row['class_id']}'>
                            <button type='button' class='delete-btn'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<!-- Confirmation Dialog -->
<div class="overlay"></div>
<div class="confirm-dialog">
    <p>Are you sure you want to delete this class?</p>
    <button class="confirm-yes">Yes</button>
    <button class="confirm-no">No</button>
</div>

<script>
    const deleteBtns = document.querySelectorAll('.delete-btn');
    const confirmDialog = document.querySelector('.confirm-dialog');
    const overlay = document.querySelector('.overlay');
    const confirmYes = document.querySelector('.confirm-yes');
    const confirmNo = document.querySelector('.confirm-no');
    let deleteForm;

    deleteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            deleteForm = e.target.closest('form');
            confirmDialog.style.display = 'block';
            overlay.style.display = 'block';
        });
    });

    confirmNo.addEventListener('click', () => {
        confirmDialog.style.display = 'none';
        overlay.style.display = 'none';
    });

    confirmYes.addEventListener('click', () => {
        deleteForm.submit();
    });
</script>
</body>
</html>
