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
$trainer_name = $class = $experience = $photo = "";
$edit_mode = false;

// Handle Add/Edit Trainer
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trainer_name = $_POST['trainer_name'];
    $class = $_POST['class'];
    $experience = $_POST['experience'];
    $photo = $_FILES['photo']['name'];
    $photo_temp = $_FILES['photo']['tmp_name'];

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Update trainer
        try {
            if (!empty($photo)) {
                move_uploaded_file($photo_temp, 'uploads/' . $photo); // Move photo to the uploads folder
                $stmt = $pdo->prepare("UPDATE trainers SET name=?, class=?, experience=?, photo=? WHERE trainer_id=?");
                $stmt->execute([$trainer_name, $class, $experience, $photo, $_POST['edit_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE trainers SET name=?, class=?, experience=? WHERE trainer_id=?");
                $stmt->execute([$trainer_name, $class, $experience, $_POST['edit_id']]);
            }
            $_SESSION['message'][] = ["type" => "success", "text" => "Trainer updated successfully!"];
        } catch (PDOException $e) {
            $_SESSION['message'][] = ["type" => "error", "text" => "Error updating trainer: " . $e->getMessage()];
        }
    } else {
        // Add new trainer
        try {
            move_uploaded_file($photo_temp, 'uploads/' . $photo); // Move photo to the uploads folder
            $stmt = $pdo->prepare("INSERT INTO trainers (name, class, experience, photo) VALUES (?, ?, ?, ?)");
            $stmt->execute([$trainer_name, $class, $experience, $photo]);
            $_SESSION['message'][] = ["type" => "success", "text" => "Trainer added successfully!"];
        } catch (PDOException $e) {
            $_SESSION['message'][] = ["type" => "error", "text" => "Error adding trainer: " . $e->getMessage()];
        }
    }
    header("Location: trainer.php");
    exit();
}

// Handle Delete Trainer
if (isset($_GET['delete'])) {
    $trainer_id = $_GET['delete'];
    try {
        // Fetch and delete photo
        $stmt = $pdo->prepare("SELECT photo FROM trainers WHERE trainer_id=?");
        $stmt->execute([$trainer_id]);
        $trainer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($trainer && file_exists('uploads/' . $trainer['photo'])) {
            unlink('uploads/' . $trainer['photo']);
        }

        // Delete trainer
        $stmt = $pdo->prepare("DELETE FROM trainers WHERE trainer_id=?");
        $stmt->execute([$trainer_id]);
        
        $_SESSION['message'][] = ["type" => "success", "text" => "Trainer deleted successfully!"];
    } catch (PDOException $e) {
        $_SESSION['message'][] = ["type" => "error", "text" => "Error deleting trainer: " . $e->getMessage()];
    }
    header("Location: trainer.php");
    exit();
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $trainer_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM trainers WHERE trainer_id=?");
    $stmt->execute([$trainer_id]);
    $trainer = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($trainer) {
        $trainer_name = $trainer['name'];
        $class = $trainer['class'];
        $experience = $trainer['experience'];
        $photo = $trainer['photo'];
        $edit_mode = true;
    }
}

// Fetch available classes
$classes_stmt = $pdo->query("SELECT * FROM classes");
$classes = $classes_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trainers</title>
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

        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
<header>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <nav>
            <a href="manage_user.php">Manage user</a>
            <a href="membership.php">Manage membership</a>
            <a href="trainer.php">Manage trainers</a>
            <a href="index.html">Logout</a>
        </nav>
    </div>
</header>
<h1 class="centered-title">Manage Gym Trainers</h1>

<!-- Message Display Section -->
<?php if (isset($_SESSION['message']) && !empty($_SESSION['message'])): ?>
    <?php foreach ($_SESSION['message'] as $msg): ?>
        <div class="message <?= $msg['type'] ?>"><?= $msg['text'] ?></div>
    <?php endforeach; ?>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<form method="post" action="" enctype="multipart/form-data">
    <input type="hidden" name="edit_id" value="<?= $edit_mode ? $_GET['edit'] : '' ?>">
    <label>Trainer Name</label>
    <input type="text" name="trainer_name" value="<?= $trainer_name ?>" required>
    
    <label>Class</label>
    <select name="class" required>
        <option value="" disabled selected>Select a Class</option>
        <?php foreach ($classes as $class_option) : ?>
            <option value="<?= $class_option['class_name'] ?>" <?= $class == $class_option['class_name'] ? 'selected' : '' ?>><?= $class_option['class_name'] ?></option>
        <?php endforeach; ?>
    </select>
    
    <label>Experience</label>
    <input type="text" name="experience" value="<?= $experience ?>" required>
    
    <label>Photo</label>
    <input type="file" name="photo" accept="image/*">
    
    <button type="submit"><?= $edit_mode ? 'Update Trainer' : 'Add Trainer' ?></button>
</form>

<table>
    <thead>
        <tr>
            <th>Trainer ID</th>
            <th>Name</th>
            <th>Class</th>
            <th>Experience</th>
            <th>Photo</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM trainers");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['trainer_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['class']}</td>
                    <td>{$row['experience']}</td>
                    <td><img src='uploads/{$row['photo']}' width='50' height='50'></td>
                    <td>
                       <form method='get' action='' style='display:inline;'>
                            <input type='hidden' name='edit' value='{$row['trainer_id']}'>
                            <button type='submit'>Edit</button>
                        </form>
                        <form method='get' action='' class='delete-form' style='display:inline;'>
                            <input type='hidden' name='delete' value='{$row['trainer_id']}'>
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
    <p>Are you sure you want to delete this trainer?</p>
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
