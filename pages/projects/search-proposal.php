<?php
require_once '../../db_connection.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['search'])) {
    $conn = OpenCon();
    // Sanitize the search input to prevent SQL injection
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    
    // SQL query to search through multiple fields
    // Uses LIKE with wildcards (%) to match partial text
    // Searches in: project title, description, supervisor name, and expertise
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
            AND (
                pp.title LIKE '%$search%' 
                OR pp.description LIKE '%$search%'
                OR u.full_name LIKE '%$search%'
                OR s.expertise LIKE '%$search%'
            )
            ORDER BY pp.submitted_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    // Initialize output string for HTML content
    $output = '';
    
    // If projects are found matching the search criteria
    if (mysqli_num_rows($result) > 0) {
        while ($proposal = mysqli_fetch_assoc($result)) {
            $output .= '<div class="project-card">';
            $output .= '<div class="project-header">';
            $output .= '<h3>' . htmlspecialchars($proposal['title']) . '</h3>';
            $output .= '</div>';
            
            $output .= '<div class="project-body">';
            $output .= '<p><strong>Description:</strong><br>' . htmlspecialchars($proposal['description']) . '</p>';
            
            $output .= '<div class="supervisor-info">';
            $output .= '<p><strong>Supervisor:</strong><br>';
            $output .= htmlspecialchars($proposal['supervisor_name']) . '<br>';
            $output .= '<small>' . htmlspecialchars($proposal['supervisor_email']) . '</small></p>';
            
            if (!empty($proposal['expertise'])) {
                $output .= '<p><strong>Expertise:</strong></p>';
                $output .= '<div class="expertise-tags">';
                $expertise_array = explode(',', $proposal['expertise']);
                foreach ($expertise_array as $expertise) {
                    $output .= '<span class="expertise-tag">' . htmlspecialchars(trim($expertise)) . '</span>';
                }
                $output .= '</div>';
            }
            $output .= '</div>';
            
            $output .= '<div class="project-footer">';
            $output .= '<p><small>Posted: ' . date('M d, Y', strtotime($proposal['submitted_at'])) . '</small></p>';
            
            if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student') {
                $output .= '<button class="apply-btn" data-proposal-id="' . $proposal['proposal_id'] . '">';
                $output .= '<i class="fas fa-paper-plane"></i> Apply Now</button>';
            }
            $output .= '</div>';
            
            $output .= '</div>'; // end project-body
            $output .= '</div>'; // end project-card
        }
    } else {
        // Display message when no projects match the search
        $output .= '<div class="no-projects">';
        $output .= '<i class="fas fa-info-circle"></i>';
        $output .= '<p>No projects found matching your search.</p>';
        $output .= '</div>';
    }
    
    CloseCon($conn);
    // Return the generated HTML to be inserted into the page
    echo $output;
}
?>