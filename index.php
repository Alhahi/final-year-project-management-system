<?php
    session_start();
    require_once './auth/auth_check.php';
    require_once './components/navbar.php';
    require_once './db_connection.php';

    $conn = OpenCon();

    // Fetch announcements from database
    $sql = "SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5"; // Limit to 5 most recent
    $result = mysqli_query($conn, $sql);

    CloseCon($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System</title>
    <link rel="stylesheet" href="index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="./assets/js/auth.js" defer></script>
</head>
<body>
<?php echo getNavbar('home'); ?>

<div class="section" id="homeSection">
    <!-- Carousel Section -->
    <div class="carousel-container">
        <div class="carousel">
            <div class="carousel-item">
                <img src="./assets/images/ads-1.jpg" style="width:100%" alt="Ad 1">
                <div class="carousel-caption">Welcome Student! This is your one-stop portal for your FYP journey.</div>
            </div>
            <div class="carousel-item">
                <img src="./assets/images/ads-2.jpg" style="width:100%" alt="Ad 2">
                <div class="carousel-caption">Register for FYP 2024</div>
            </div>
            <div class="carousel-item">
                <img src="./assets/images/ads-3.jpg" style="width:100%" alt="Ad 3">
                <div class="carousel-caption">Join the FYP Community and Stay Ahead</div>
            </div>
        </div>
    </div>
        <div style="text-align: center;">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>
    <script src="assets\js\carousel-auto.js"></script>
    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3><i class="fas fa-bullhorn"></i>Announcements</h3>
            <div class="announcements-container">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <div class="announcement">
                            <h4><?php echo htmlspecialchars($row['title']); ?></h4>
                            <p><?php echo htmlspecialchars($row['content']); ?></p>
                            <small>Posted: <?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="announcement">
                        <p>No announcements available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="dashboard-card quick-links">
            <h3><i class="fas fa-link"></i> Quick Links</h3>
            <ul>
                <li><a href="pages/guidelines/project-guidelines.php"><i class="fas fa-file-alt"></i> Project Guidelines</a></li>
                <!-- <li><a href="pages/dates/important-dates.php"><i class="fas fa-calendar-alt"></i> Important Dates</a></li> -->
                <li><a href="pages/templates/documentation-templates.php"><i class="fas fa-book"></i> Documentation Templates</a></li>
                <li><a href="pages/faq/faq.php"><i class="fas fa-question-circle"></i> FYP FAQ</a></li>
                <li><a href="pages/supervisor-dashboard/supervisor-dashboard-page.php" class="dashboard-link" id="supervisorLink">
                    <i class="fas fa-chalkboard-teacher"></i> Supervisor Dashboard</a></li>
                <li><a href="pages/admin-dashboard/admin-dashboard-page.php" class="dashboard-link" id="adminLink">
                    <i class="fas fa-user-shield"></i> Admin Dashboard</a></li>
            </ul>
        </div>
        <div class="dashboard-card important-dates">
            <h3><i class="fas fa-calendar-alt"></i> Upcoming Events</h3>
            <div class="dates-list">
                <?php
                $conn = OpenCon();
                $sql = "SELECT d.*, u.full_name as creator_name 
                        FROM important_dates d 
                        LEFT JOIN users u ON d.created_by = u.user_id 
                        WHERE d.date >= CURDATE() 
                        ORDER BY d.date ASC LIMIT 5";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="event">';
                        echo '<div class="event-date">';
                        echo '<span>' . date('M', strtotime($row['date'])) . '</span>';
                        echo '<span>' . date('d', strtotime($row['date'])) . '</span>';
                        echo '</div>';
                        echo '<div class="event-details">';
                        echo '<h4>' . htmlspecialchars($row['title']) . '</h4>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        echo '<small>Posted by: ' . htmlspecialchars($row['creator_name'] ?? 'Unknown') . '</small>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="event"><p>No upcoming events.</p></div>';
                }
                CloseCon($conn);
                ?>
            </div>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
</footer>

</body>
</html>