-- Create the database
CREATE DATABASE IF NOT EXISTS fyp_system;
USE fyp_system;

-- Users table (base table for all user types)
CREATE   TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    smtp_pass VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('student', 'supervisor', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    phone_number VARCHAR(20) NOT NULL,
    country_code VARCHAR(10) NOT NULL
);

-- Students table
CREATE   TABLE students (
    student_id INT PRIMARY KEY,
    matric_number VARCHAR(20) UNIQUE NOT NULL,
    course VARCHAR(100) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Supervisors table
CREATE   TABLE supervisors (
    supervisor_id INT PRIMARY KEY,
    expertise TEXT,
    FOREIGN KEY (supervisor_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Admin table
CREATE   TABLE admins (
    admin_id INT PRIMARY KEY,
    FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Project proposals table
CREATE   TABLE project_proposals (
    proposal_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    supervisor_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected','available') DEFAULT 'pending',
    feedback TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE SET NULL,
    FOREIGN KEY (supervisor_id) REFERENCES supervisors(supervisor_id) ON DELETE SET NULL
);

-- Projects table (approved proposals become projects)
CREATE   TABLE projects (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    proposal_id INT UNIQUE,
    student_id INT,
    supervisor_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    start_date DATE,
    end_date DATE,
    status ENUM('ongoing', 'completed', 'failed') DEFAULT 'ongoing',
    FOREIGN KEY (proposal_id) REFERENCES project_proposals(proposal_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE SET NULL,
    FOREIGN KEY (supervisor_id) REFERENCES supervisors(supervisor_id) ON DELETE SET NULL
);

-- Assessments table
CREATE   TABLE assessments (
    assessment_id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    total_marks DECIMAL(5,2),
    project_planning DECIMAL(5,2),
    technical_implementation DECIMAL(5,2),
    documentation DECIMAL(5,2),
    presentation DECIMAL(5,2),
    feedback TEXT,
    FOREIGN KEY (project_id) REFERENCES projects(project_id) ON DELETE CASCADE
);

CREATE   TABLE emails (
    email_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    receiver_id INT,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE   TABLE messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT,
    receiver_id INT,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- Important dates table
CREATE   TABLE important_dates (
    date_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
);

-- FAQ table
CREATE   TABLE faqs (
    faq_id INT PRIMARY KEY AUTO_INCREMENT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Project guidelines table
CREATE   TABLE guidelines (
    guideline_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE   Table announcements (
    announcement_id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE   TABLE feedbacks(
    feedback_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    feedback TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE   TABLE forums(
    forum_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE forum_comments(
    comment_id INT PRIMARY KEY AUTO_INCREMENT,
    forum_id INT,
    user_id INT,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (forum_id) REFERENCES forums(forum_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE meetings(
    meeting_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    venue VARCHAR(255) NOT NULL,
    status ENUM('upcoming', 'completed') DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE   TABLE meeting_logs(
    meeting_log_id INT PRIMARY KEY AUTO_INCREMENT,
    meeting_id INT,
    user_id INT,
    work_done TEXT NOT NULL,
    future_work TEXT NOT NULL,
    other TEXT NOT NULL,
    comments TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (meeting_id) REFERENCES meetings(meeting_id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE   TABLE presentations_slots(
    presentation_slot_id INT PRIMARY KEY AUTO_INCREMENT,
    slot_date DATE NOT NULL,
    slot_time TIME NOT NULL,
    user_id INT,
    supervisor_id INT,
    status ENUM('available', 'booked') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (supervisor_id) REFERENCES users(user_id) ON DELETE SET NULL
);

CREATE   TABLE goals(
    goal_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    goal TEXT NOT NULL,
    status ENUM('not_started','ongoing', 'completed') DEFAULT 'not_started',
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE supervisor_proposals (
    proposal_id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT,
    supervisor_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    feedback TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE SET NULL,
    FOREIGN KEY (supervisor_id) REFERENCES supervisors(supervisor_id) ON DELETE SET NULL
);

-- Create indexes for better performance
CREATE INDEX idx_user_email ON users(email);
CREATE INDEX idx_student_matric ON students(matric_number);
CREATE INDEX idx_proposal_status ON project_proposals(status);
CREATE INDEX idx_project_status ON projects(status);

-- Insert supervisor user

INSERT INTO users (email, password, full_name, role, phone_number, country_code)
VALUES ('supervisor@mmu.edu.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dr. Sarah Johnson', 'supervisor', '1234567890', '+60');

SET @supervisor_id = LAST_INSERT_ID();

INSERT INTO supervisors (supervisor_id, expertise)
VALUES (@supervisor_id, 'Artificial Intelligence, Machine Learning, Data Science');

-- Insert admin user

INSERT INTO users (email, password, full_name, role, phone_number, country_code)
VALUES ('admin@mmu.edu.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', '0987654321', '+60');

SET @admin_id = LAST_INSERT_ID();

INSERT INTO admins (admin_id)
VALUES (@admin_id);

-- Insert student user

INSERT INTO users (email, password, full_name, role, phone_number, country_code)
VALUES ('student@mmu.edu.my', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'student', '123456789', '+60');

SET @student_id = LAST_INSERT_ID();

INSERT INTO students (student_id, matric_number, course)
VALUES (@student_id, '1234567890', 'Software Engineering');

-- Insert student user (Email purpose)

INSERT INTO users (email, password, smtp_pass, full_name, role, phone_number, country_code)
VALUES ('limtzeyou@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sdbi ojrm aotd iwdz','Lim Tze You', 'student', '168396802', '+60');

SET @student_id = LAST_INSERT_ID();

INSERT INTO students (student_id, matric_number, course)
VALUES (@student_id, '1221301879', 'Software Engineering');

-- Insert supervisor user (Email purpose)

INSERT INTO users (email, password, full_name, role, phone_number, country_code)
VALUES ('tzeyoulim00@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Supervisor Lim', 'supervisor', '1234567890', '+60');

SET @supervisor_id = LAST_INSERT_ID();

INSERT INTO supervisors (supervisor_id, expertise)
VALUES (@supervisor_id, 'Artificial Intelligence, Machine Learning, Data Science');

