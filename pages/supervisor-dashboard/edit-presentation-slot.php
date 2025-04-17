<?php
session_start();
require_once '../../auth/role_check.php';
$hasAccess = checkSupervisorRole();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once '../../db_connection.php';

if (!isset($_GET['id'])) {
    header("Location: manage-presentations.php");
    exit();
}

$supervisor_id = $_SESSION['user_id'];
$slot_id = $_GET['id'];

// Verify ownership
$conn = OpenCon();
$check_query = "SELECT * FROM presentations_slots 
                WHERE presentation_slot_id = ? AND supervisor_id = ?";
$stmt = mysqli_prepare($conn, $check_query);
mysqli_stmt_bind_param($stmt, "ii", $slot_id, $supervisor_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error_message'] = "Access denied or slot not found.";
    header("Location: manage-presentations.php");
    exit();
}

// Fetch the slot details
$query = "SELECT * FROM presentations_slots WHERE presentation_slot_id = ? AND status = 'available'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $slot_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$slot = mysqli_fetch_assoc($result);

if (!$slot) {
    $_SESSION['error_message'] = "Invalid slot or slot is already booked.";
    header("Location: manage-presentations.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Edit Presentation Slot</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./supervisor-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php echo getNavbar('supervisor'); ?>

    <?php if ($hasAccess): ?>
    <div class="section">
        <h2>Edit Presentation Slot</h2>
        
        <div class="form-container">
            <form action="update-presentation-slot.php" method="POST" class="standard-form">
                <input type="hidden" name="slot_id" value="<?php echo htmlspecialchars($slot['presentation_slot_id']); ?>">
                
                <div class="form-group">
                    <label for="slot_date">Date:</label>
                    <input type="date" id="slot_date" name="slot_date" 
                           value="<?php echo htmlspecialchars($slot['slot_date']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="slot_time">Time:</label>
                    <input type="time" id="slot_time" name="slot_time" 
                           value="<?php echo htmlspecialchars($slot['slot_time']); ?>" required>
                </div>
                
                <div class="button-group">
                    <button type="submit" class="primary-button">
                        <i class="fas fa-save"></i> Update Slot
                    </button>
                    <a href="manage-presentations.php" class="secondary-button">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <style>
    .section {
        padding: 20px;
        max-width: 800px;
        margin: 0 auto;
    }

    .form-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .standard-form {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }

    .primary-button {
        background-color: #0066cc;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .secondary-button {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .primary-button:hover {
        background-color: #0052a3;
    }

    .secondary-button:hover {
        background-color: #5a6268;
    }
    </style>

    <?php endif; ?>
    <?php CloseCon($conn); ?>
</body>
</html> 