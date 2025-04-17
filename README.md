# PHP Website Setup Guide (Using XAMPP & MySQL)

## App Features

### User Management
- User registration and login functionality
- Role-based access control (Admin, Supervisor, Student)
- User profile management
- Password reset functionality

### FYP Management
- Project proposal submission and approval workflow
- Project allocation and assignment to supervisors
- Progress tracking and milestone management
- File upload/download for project documentation
- Feedback and evaluation system

### Communication
- Messaging system between students and supervisors
- Notification system for important events and deadlines
- Announcement board for system-wide communications

### Reporting
- Generate progress reports for projects
- Analytics dashboard for administrators
- Export functionality for project data

## Requirements

### Performance
- Page load time should not exceed 3 seconds
- System should handle at least 100 concurrent users
- Database queries should be optimized for speed

### Security
- Secure authentication and authorization mechanisms
- Data encryption for sensitive information
- Protection against common web vulnerabilities (XSS, CSRF, SQL Injection)
- Regular security audits and updates

### Usability
- Intuitive and responsive user interface
- Mobile-friendly design
- Consistent navigation and layout
- Helpful error messages and user guidance

### Reliability
- System availability of at least 99.5%
- Automated backup and recovery procedures
- Graceful error handling and logging

### Scalability
- Ability to scale with increasing user base
- Modular architecture for easy feature additions
- Efficient resource utilization

### Compatibility
- Support for major browsers (Chrome, Firefox, Safari, Edge)
- Responsive design for different screen sizes

## Prerequisites

Before setting up the PHP website, ensure you have the following installed:

- [XAMPP](https://www.apachefriends.org/index.html) (Includes Apache, PHP, and MySQL)
- A web browser (Chrome, Firefox, Edge, etc.)
- A text editor (VS Code, Sublime Text, Notepad++, etc.)

## Installation and Setup

### Step 1: Install XAMPP
1. Download XAMPP from [Apache Friends](https://www.apachefriends.org/download.html).
2. Run the installer and follow the on-screen instructions.
3. Start XAMPP Control Panel and ensure **Apache** and **MySQL** are running.

### Step 2: Configure the PHP Website
1. Unzip and copy `fyp-system` folder into the `htdocs` directory located in your XAMPP installation directory (e.g., `C:\xampp\htdocs\fyp-system`).
2. Open XAMPP Control Panel and start **Apache** and **MySQL**.

### Step 3: Set Up the MySQL Database
1. Open your web browser and go to `http://localhost/phpmyadmin`.
2. Import your database SQL file:
   - Click on the database you just created.
   - Go to the **Import** tab.
   - Click **Choose File** and select the `database.sql` file
   - Click **Go** to import the database.

### Step 4: Configure Database Connection
1. Open your project folder in a text editor.
2. Locate the database configuration file (commonly `config.php` or `.env`).
3. Update the database credentials as follows:
   ```php
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "mydatabase";
   ```

### Step 5: Run the Website
1. Open your browser and go to `http://localhost/fyp-system`.
2. Your PHP website should now be accessible.

## Troubleshooting

- **Apache or MySQL Not Starting?**
  - Check for port conflicts. Change Apache’s port in `httpd.conf` (`Listen 80` → `Listen 8080`).
  - Change MySQL’s port in `my.ini` (`port=3306` → `port=3307`).
  - Restart XAMPP after changes.

- **Database Connection Errors?**
  - Ensure MySQL is running.
  - Verify database credentials in `config.php`.

## Conclusion
Your PHP website is now set up and running locally using XAMPP. Happy coding!
