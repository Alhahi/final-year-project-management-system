<?php
require_once '../../db_connection.php';

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Projects - FCI FYP</title>
    <link rel="stylesheet" href="../../index.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./view-projects.css">
</head>
<body>
    <div class="back-button">
        <a href="projects-page.php" title="Back to Projects">
            <i class="fas fa-arrow-left"></i> Back to Projects
        </a>
    </div>

    <div class="section">
        <h2>Available Project Proposals</h2>

        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search project proposal">
        </div>
        
        <div class="projects-container">
            <?php
            $conn = OpenCon();
            
            // Query to get all available project proposals
            $sql = "SELECT pp.proposal_id, 
                    pp.title, 
                    pp.description, 
                    pp.status,
                    pp.submitted_at,
                    pp.feedback,
                    u.full_name as supervisor_name,
                    u.email as supervisor_email,
                    s.expertise
                    FROM project_proposals pp
                    LEFT JOIN users u ON pp.supervisor_id = u.user_id
                    LEFT JOIN supervisors s ON pp.supervisor_id = s.supervisor_id
                    WHERE pp.status = 'available'
                    ORDER BY pp.submitted_at DESC";
            
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($proposal = mysqli_fetch_assoc($result)) {
                    echo '<div class="project-card">';
                    echo '<div class="project-header">';
                    echo '<h3>' . htmlspecialchars($proposal['title']) . '</h3>';
                    echo '</div>';
                    
                    echo '<div class="project-body">';
                    echo '<p><strong>Description:</strong><br>' . htmlspecialchars($proposal['description']) . '</p>';
                    
                    echo '<div class="supervisor-info">';
                    echo '<p><strong>Supervisor:</strong><br>';
                    echo htmlspecialchars($proposal['supervisor_name']) . '<br>';
                    echo '<small>' . htmlspecialchars($proposal['supervisor_email']) . '</small></p>';
                    
                    if (!empty($proposal['expertise'])) {
                        echo '<p><strong>Expertise:</strong></p>';
                        echo '<div class="expertise-tags">';
                        $expertise_array = explode(',', $proposal['expertise']);
                        foreach ($expertise_array as $expertise) {
                            echo '<span class="expertise-tag">' . htmlspecialchars(trim($expertise)) . '</span>';
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                    
                    echo '<div class="project-footer">';
                    echo '<p><small>Posted: ' . date('M d, Y', strtotime($proposal['submitted_at'])) . '</small></p>';
                    
                    if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student') {
                        echo '<button class="apply-btn" data-proposal-id="' . $proposal['proposal_id'] . '">';
                        echo '<i class="fas fa-paper-plane"></i> Apply Now</button>';
                    }
                    echo '</div>';
                    
                    echo '</div>'; // end project-body
                    echo '</div>'; // end project-card
                }
            } else {
                echo '<div class="no-projects">';
                echo '<i class="fas fa-info-circle"></i>';
                echo '<p>No available project proposals at the moment.</p>';
                echo '<p>Please check back later.</p>';
                echo '</div>';
            }
            
            CloseCon($conn);
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>

    <script>
    // Handle apply button clicks
    document.querySelectorAll('.apply-btn').forEach(button => {
        button.addEventListener('click', function() {
            const proposalId = this.dataset.proposalId;
            if (confirm('Are you sure you want to apply for this project?')) {
                // Add your application logic here
                // You might want to redirect to an application form or handle it via AJAX
                window.location.href = `apply-project.php?proposal_id=${proposalId}`;
            }
        });
    });

    // Add search function (Search available project proposals)
    const searchInput = document.getElementById('searchInput');
    const projectsContainer = document.querySelector('.projects-container');

    // Variable to store timeout ID for debouncing (query rate limiting)
    let searchTimeout;

    // Add event listener for input changes in search box
    searchInput.addEventListener('input', function() {
        // Clear any existing timeout to implement debouncing
        clearTimeout(searchTimeout);
        
        // Set a timeout to delay the search execution
        // This prevents sending too many requests while user is still typing
        searchTimeout = setTimeout(() => {
            // Get the trimmed search term
            const searchTerm = this.value.trim();
            
            // Make AJAX request to search-proposal.php with the search term
            fetch(`search-proposal.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.text())
                .then(data => {
                    // Update the projects container with search results
                    projectsContainer.innerHTML = data;
                    
                    // Reattach event listeners to new apply buttons
                    // This is necessary because the original buttons were replaced
                    document.querySelectorAll('.apply-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const proposalId = this.dataset.proposalId;
                            if (confirm('Are you sure you want to apply for this project?')) {
                                window.location.href = `apply-project.php?proposal_id=${proposalId}`;
                            }
                        });
                    });
                })
                .catch(error => console.error('Error:', error));
        }, 300); // Add a 300ms delay to prevent too many requests
    });
    </script>
</body>
</html>