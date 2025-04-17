<?php
session_start();
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once './fetch-forum.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    addTopic($title, $content);
    header("Location: /fyp-system/pages/communication/forum.php");
}

function addTopic($title, $content) {
    require_once '../../db_connection.php';
    $conn = OpenCon();
    session_start();
    
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session

    // Use a prepared statement
    $stmt = $conn->prepare("INSERT INTO forums (user_id, title, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $title, $content);

    if ($stmt->execute()) {
        echo "Topic added successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    CloseCon($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Forum</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./shared.css">
    <link rel="stylesheet" href="./forum.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="../../assets/js/auth.js" defer></script>
</head>
<body>
<?php echo getNavbar('communication'); ?>
    <!-- Forum Section -->
    <div class="section" id="communication">
        <div class="communication-container">
            <!-- Communication Tabs -->
            <div class="comm-tabs">
                <a href="communication-page.php" class="tab-btn">
                    <i class="fas fa-comments"></i> Chat Room
                </a>
                <a href="email.php" class="tab-btn">
                    <i class="fas fa-envelope"></i> Email
                </a>
                <a href="forum.php" class="tab-btn active">
                    <i class="fas fa-users"></i> Forum
                </a>
            </div>

            <!-- Forum Content -->
            <div class="forum-container">
                <div class="forum-header">
                    <h3>Discussion Forums</h3>
                    <a href="#new-topic" class="new-topic-btn">
                        <i class="fas fa-plus"></i> New Topic
                    </a>

                    <!-- New Forum Topic Modal -->
                    <div class="modal" id="newTopicModal">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3>Create New Topic</h3>
                                <button class="close-btn">&times;</button>
                            </div>
                            <form id="topicForm" action="" method="post">
                                <div class="form-group">
                                    <label for="topicTitle">Title:</label>
                                    <input name='title' type="text" id="topicTitle" required>
                                </div>
                                <div class="form-group">
                                    <label for="topicContent">Content:</label>
                                    <textarea name='content'id="topicContent" rows="6" required></textarea>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="cancel-btn">Cancel</button>
                                    <button type="submit" class="save-btn">Create Topic</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php
                    // Check if forums exist
                    $forums = fetchAllForums();
                    if (!empty($forums)) {
                        foreach ($forums as $forum) {
                            $forum_id = $forum['forum_id'];
                            $username = htmlspecialchars($forum['full_name']);
                            $title = htmlspecialchars($forum['title']);
                            $content = htmlspecialchars($forum['content']);
                            $createdAt = htmlspecialchars($forum['created_at']);
                            echo "
                            <div class='forum-item'>
                                <div class='forum-header'>
                                    <h3 class='forum-title'>
                                        <a href='forum-details.php?forum_id=$forum_id'>$title</a>
                                    </h3>
                                    
                                    <p class='forum-meta'>Posted by: <span class='forum-author'>$username</span> on <span class='forum-date'>$createdAt</span></p>
                                </div>
                                <div class='forum-body'>
                                    <p class='forum-content'>$content</p>
                                </div>
                            </div>
                            ";
                        }
                    } else {
                        echo "<p class='no-forums'>No forums found.</p>";
                    }
                    ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("newTopicModal");
            const openModalBtn = document.querySelector(".new-topic-btn");
            const closeModalBtn = document.querySelector(".close-btn");
            const cancelBtn = document.querySelector(".cancel-btn");

            const toggleModal = (show) => {
                modal.style.display = show ? "block" : "none";
            };

            openModalBtn.addEventListener("click", () => toggleModal(true));
            closeModalBtn.addEventListener("click", () => toggleModal(false));
            cancelBtn.addEventListener("click", () => toggleModal(false));
        });

    </script>
</body>
</html> 