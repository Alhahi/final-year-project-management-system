<?php
require_once '../../auth/auth_check.php';
require_once '../../components/navbar.php';
require_once "./fetch-courses.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCI FYP System - Manage Courses</title>
    <link rel="stylesheet" href="../../index.css">
    <link rel="stylesheet" href="./admin-dashboard-page.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php echo getNavbar('admin'); ?>

    <div class="section">
        <h2>Course Management</h2>
        <div class="dashboard-container">
            <div class="action-panel">
                <div class="panel-header">
                    <h3>All Courses</h3>
                    <button class="new-course-btn" id="newCourseBtn">
                        <i class="fas fa-plus"></i> Add New Course
                    </button>
                </div>
                <div class="courses-list">
                    <?php
                        $result = getAllCourses();
                        while ($row = $result->fetch_assoc()) {
                            echo "<div class='course-item'>";
                            echo "<div class='course-header'>";
                            echo "<h4 class='course-name'><i class='fas fa-graduation-cap'></i> ".$row['course_name']."</h4>";
                            echo "<p class='course-code'><i class='fas fa-hashtag'></i> ".$row['course_code']."</p>";
                            echo "</div>";
                            echo "<div class='course-details'>";
                            echo "<span class='faculty-name'><i class='fas fa-university'></i> Faculty: ".$row['faculty_name']."</span>";
                            echo "<span class='course-date'><i class='fas fa-calendar-alt'></i> Added: ".date('M d, Y', strtotime($row['created_at']))."</span>";
                            echo "</div>";
                            echo "<div class='course-actions'>";
                            echo "<button class='edit-btn' data-courseid='".$row['course_id']."'><i class='fas fa-edit'></i> Edit</button>";
                            echo "<button class='delete-btn' data-courseid='".$row['course_id']."'><i class='fas fa-trash'></i> Delete</button>";
                            echo "</div>";
                            echo "</div>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal" id="addCourseModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New Course</h3>
                <span class="close-btn">&times;</span>
            </div>
            <form id="addCourseForm" method="POST" action="./add-course.php">
                <div class="form-group">
                    <label for="course_name">Course Name</label>
                    <input type="text" id="course_name" name="course_name" required>
                </div>
                <div class="form-group">
                    <label for="course_code">Course Code</label>
                    <input type="text" id="course_code" name="course_code" required 
                           pattern="[A-Z]{3}[0-9]{4}"
                           placeholder="ABC1234"
                           title="Please enter a valid course code (e.g., ABC1234)">
                </div>
                <div class="form-group">
                    <label for="faculty_id">Faculty</label>
                    <select id="faculty_id" name="faculty_id" required>
                        <option value="">Select Faculty</option>
                        <?php
                        $faculties = getAllFaculties();
                        while ($faculty = $faculties->fetch_assoc()) {
                            echo "<option value='".$faculty['faculty_id']."'>".$faculty['faculty_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save-btn">Add Course</button>
                    <button type="button" class="cancel-btn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal" id="editCourseModal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Course</h3>
                <span class="close-btn" id="editCloseBtn">&times;</span>
            </div>
            <form id="editCourseForm" method="POST" action="./update-course.php">
                <input type="hidden" id="edit_course_id" name="course_id">
                <div class="form-group">
                    <label for="edit_course_name">Course Name</label>
                    <input type="text" id="edit_course_name" name="course_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_course_code">Course Code</label>
                    <input type="text" id="edit_course_code" name="course_code" required
                           pattern="[A-Z]{3}[0-9]{4}"
                           title="Please enter a valid course code (e.g., ABC1234)">
                </div>
                <div class="form-group">
                    <label for="edit_faculty_id">Faculty</label>
                    <select id="edit_faculty_id" name="faculty_id" required>
                        <option value="">Select Faculty</option>
                        <?php
                        $faculties = getAllFaculties();
                        while ($faculty = $faculties->fetch_assoc()) {
                            echo "<option value='".$faculty['faculty_id']."'>".$faculty['faculty_name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="save-btn">Save Changes</button>
                    <button type="button" class="cancel-btn" id="editCancelBtn">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Faculty of Computing and Informatics, Multimedia University. All Rights Reserved.</p>
    </footer>

    <script>
    // Modal functionality for Add Course
    const modal = document.getElementById('addCourseModal');
    const addBtn = document.getElementById('newCourseBtn');
    const closeBtn = document.querySelector('.close-btn');
    const cancelBtn = document.querySelector('.cancel-btn');

    addBtn.onclick = function() {
        modal.style.display = "block";
    }

    closeBtn.onclick = function() {
        modal.style.display = "none";
    }

    cancelBtn.onclick = function() {
        modal.style.display = "none";
    }

    // Delete functionality
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this course?')) {
                const courseId = this.dataset.courseid;
                fetch('./delete-course.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `course_id=${courseId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        window.location.reload();
                    } else {
                        alert('Error deleting course');
                    }
                });
            }
        });
    });

    // Edit functionality
    const editModal = document.getElementById('editCourseModal');
    const editCloseBtn = document.getElementById('editCloseBtn');
    const editCancelBtn = document.getElementById('editCancelBtn');

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const courseId = this.dataset.courseid;
            fetch(`./get-course.php?course_id=${courseId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_course_id').value = courseId;
                    document.getElementById('edit_course_name').value = data.course_name;
                    document.getElementById('edit_course_code').value = data.course_code;
                    
                    const facultySelect = document.getElementById('edit_faculty_id');
                    for (let i = 0; i < facultySelect.options.length; i++) {
                        if (facultySelect.options[i].value === data.faculty_id) {
                            facultySelect.selectedIndex = i;
                            break;
                        }
                    }

                    editModal.style.display = 'block';
                });
        });
    });

    editCloseBtn.onclick = function() {
        editModal.style.display = "none";
    }

    editCancelBtn.onclick = function() {
        editModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        if (event.target == editModal) {
            editModal.style.display = "none";
        }
    }
    </script>
</body>
</html> 