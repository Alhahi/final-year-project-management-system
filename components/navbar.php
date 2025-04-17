<?php
function getNavbar($active_page = '') {
    $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

    $homePage = '/fyp-system/index.php';
    $projectsPage = '/fyp-system/pages/projects/projects-page.php';
    $meetingsPage = '/fyp-system/pages/meetings/meetings-page.php';
    $progressPage = '/fyp-system/pages/progress/progress-page.php';
    $assessmentPage = '/fyp-system/pages/assessment/assessment-page.php';
    $supportPage = '/fyp-system/pages/support/support-page.php';
    $communicationsPage = '/fyp-system/pages/communication/communication-page.php';
    $profilePage = '/fyp-system/pages/profile/profile-page.php';
    $loginPage = '/fyp-system/pages/login/login-page.php';
    $registerPage = '/fyp-system/pages/register/register-page.php';
    $logo = '/fyp-system/assets/images/mmu-logo-white.png';
    
    $html = '
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo-section">
                <a href="/fyp-system/index.php"><img src="'.$logo.'" alt="MMU Logo"></a>
            </div>
            <div class="nav-section">
                <div class="nav-links">
                    <a href="'.$homePage.'" class="nav-link ' . ($active_page === 'home' ? 'active' : '') . '" id="homeLink">Home</a>
                    <a href="'.$projectsPage.'" class="nav-link ' . ($active_page === 'projects' ? 'active' : '') . '" id="projectsLink">Projects</a>
                    <a href="'.$meetingsPage.'" class="nav-link ' . ($active_page === 'meetings' ? 'active' : '') . '" id="meetingsLink">Meetings</a>
                    <a href="'.$progressPage.'" class="nav-link ' . ($active_page === 'progress' ? 'active' : '') . '" id="progressLink">Progress</a>
                    <a href="'.$assessmentPage.'" class="nav-link ' . ($active_page === 'assessment' ? 'active' : '') . '" id="assessmentLink">Assessment</a>
                    <a href="'.$supportPage.'" class="nav-link ' . ($active_page === 'support' ? 'active' : '') . '" id="supportLink">Support</a>
                    <a href="'.$communicationsPage.'" class="nav-link ' . ($active_page === 'communication' ? 'active' : '') . '" id="communicationLink">Communication</a>
                    <a href="'.$profilePage.'" class="nav-link ' . ($active_page === 'profile' ? 'active' : '') . '" id="profileLink">Profile</a>';
    
    if ($isLoggedIn) {
        $html .= '<a href="javascript:void(0);" onclick="handleLogout()" class="nav-link" id="logoutBtn">Logout</a>';
    } else {
        $html .= '
            <a href="'.$loginPage.'" class="nav-link ' . ($active_page === 'login' ? 'active' : '') . '" id="loginBtn">Login</a>
            <a href="'.$registerPage.'" class="nav-link ' . ($active_page === 'register' ? 'active' : '') . '" id="registerBtn">Register</a>';
    }
    
    $html .= '
                </div>
            </div>
        </div>
    </nav>';
    
    return $html;
}
?> 