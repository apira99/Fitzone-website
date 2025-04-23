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
$plan_name = $benefits = $price = "";
$edit_mode = false;

// Handle Add/Edit Membership Plan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plan_name = $_POST['plan_name'];
    $benefits = $_POST['benefits'];
    $price = $_POST['price'];

    if (isset($_POST['edit_id']) && !empty($_POST['edit_id'])) {
        // Update membership plan
        try {
            $stmt = $pdo->prepare("UPDATE memberships SET plan_name=?, benefits=?, price=? WHERE membership_id=?");
            $stmt->execute([$plan_name, $benefits, $price, $_POST['edit_id']]);
            $_SESSION['message'][] = ["type" => "success", "text" => "Membership plan updated successfully!"];
        } catch (PDOException $e) {
            $_SESSION['message'][] = ["type" => "error", "text" => "Error updating membership plan: " . $e->getMessage()];
        }
    } else {
        // Add new membership plan
        try {
            $stmt = $pdo->prepare("INSERT INTO memberships (plan_name, benefits, price) VALUES (?, ?, ?)");
            $stmt->execute([$plan_name, $benefits, $price]);
            $_SESSION['message'][] = ["type" => "success", "text" => "Membership plan added successfully!"];
        } catch (PDOException $e) {
            $_SESSION['message'][] = ["type" => "error", "text" => "Error adding membership plan: " . $e->getMessage()];
        }
    }
    header("Location: membership.php");
    exit();
}

// Handle Delete Membership Plan
if (isset($_GET['delete'])) {
    $membership_id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM memberships WHERE membership_id=?");
        $stmt->execute([$membership_id]);
        $_SESSION['message'][] = ["type" => "success", "text" => "Membership plan deleted successfully!"];
    } catch (PDOException $e) {
        $_SESSION['message'][] = ["type" => "error", "text" => "Error deleting membership plan: " . $e->getMessage()];
    }
    header("Location: membership.php");
    exit();
}

// Handle Edit Mode
if (isset($_GET['edit'])) {
    $membership_id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM memberships WHERE membership_id=?");
    $stmt->execute([$membership_id]);
    $membership = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($membership) {
        $plan_name = $membership['plan_name'];
        $benefits = $membership['benefits'];
        $price = $membership['price'];
        $edit_mode = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Membership Plans</title>
    <link rel="stylesheet" href="class.css">
    <style>
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

        /* Message Styling */
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

<h1 class="centered-title">Manage Membership Plans</h1>

<!-- Message Display Section -->
<?php if (isset($_SESSION['message']) && !empty($_SESSION['message'])): ?>
    <?php foreach ($_SESSION['message'] as $msg): ?>
        <div class="message <?= $msg['type'] ?>"><?= $msg['text'] ?></div>
    <?php endforeach; ?>
    <?php unset($_SESSION['message']); // Clear messages after displaying ?>
<?php endif; ?>

<form method="post" action="">
    <input type="hidden" name="edit_id" value="<?= $edit_mode ? $_GET['edit'] : '' ?>">
    <label>Plan Name</label>
    <input type="text" name="plan_name" value="<?= $plan_name ?>" required>
    <label>Benefits</label>
    <textarea name="benefits" required><?= $benefits ?></textarea>
    <label>Price</label>
    <input type="number" name="price" value="<?= $price ?>" required>
    <button type="submit"><?= $edit_mode ? 'Update Plan' : 'Add Plan' ?></button>
</form>

<table>
    <thead>
        <tr>
            <th>Plan ID</th>
            <th>Plan Name</th>
            <th>Benefits</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM memberships");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['membership_id']}</td>
                    <td>{$row['plan_name']}</td>
                    <td>{$row['benefits']}</td>
                    <td>{$row['price']}</td>
                    <td>
                        <form method='get' action='' class='edit-form' style='display:inline;'>
                            <input type='hidden' name='edit' value='{$row['membership_id']}'>
                            <button type='submit' class='edit-btn'>Edit</button>
                        </form>
                        <form method='get' action='' class='delete-form'>
                            <input type='hidden' name='delete' value='{$row['membership_id']}'>
                            <button type='button' class='delete-btn'>Delete</button>
                        </form>
                    </td>
                </tr>";
        }
        ?>
    </tbody>
</table>

<!-- Confirmation Dialog -->
<div class="confirm-dialog">
    <p>Are you sure you want to delete this membership plan?</p>
    <button class="confirm-yes">Yes</button>
    <button class="confirm-no">No</button>
</div>

<!-- Overlay -->
<div class="overlay"></div>

<script>
    // Variables for Confirmation Dialog
    const editBtns = document.querySelectorAll('.edit-btn');
    const deleteBtns = document.querySelectorAll('.delete-btn');
    const confirmDialog = document.querySelector('.confirm-dialog');
    const overlay = document.querySelector('.overlay');
    const confirmYes = document.querySelector('.confirm-yes');
    const confirmNo = document.querySelector('.confirm-no');
    let deleteForm; // To track which form to submit

    // Show confirmation dialog when Delete is clicked
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            deleteForm = e.target.closest('.delete-form'); // Get the associated form
            confirmDialog.style.display = 'block';
            overlay.style.display = 'block';
        });
    });

    // Cancel deletion
    confirmNo.addEventListener('click', () => {
        confirmDialog.style.display = 'none';
        overlay.style.display = 'none';
    });

    // Confirm deletion
    confirmYes.addEventListener('click', () => {
        confirmDialog.style.display = 'none';
        overlay.style.display = 'none';
        deleteForm.submit(); // Submit the associated form
    });

    // Close the dialog when clicking outside it
    overlay.addEventListener('click', () => {
        confirmDialog.style.display = 'none';
        overlay.style.display = 'none';
    });
</script>
</body>
</html>
