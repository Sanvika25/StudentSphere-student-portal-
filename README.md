# StudentSphere-student-portal-
# Student-portal
This is a student portal web application where students can log in to view their profiles, grades, and attendance records. The portal is built using PHP and MySQL, and it is configured to work with MAMP.

## Features
- Student login
- Profile display
- View grades
- View attendance records
- Attendance chart visualization (bar chart comparing present and absent percentages)

## Technologies Used
- PHP
- MySQL
- HTML/CSS
- JavaScript
- Chart.js

## Requirements
- MAMP (Macintosh, Apache, MySQL, and PHP)
- Text editor

## Installation

### Set up the database:

1. Open MAMP and start the servers.(Make sure to set the port numbers right).
2. Access phpMyAdmin by navigating to [http://localhost:8888/phpMyAdmin](http://localhost:8888/phpMyAdmin).
3. Create a new MySQL database named `student_portal`.
4. Add tables like students,grades,attendance,users

### Configure the database connection
### Set up the web server:

1. Place the project files in your MAMP web server's root directory, typically `/Applications/MAMP/htdocs/`.
2. Start your MAMP servers.

### Access the portal:

1. Open your web browser and navigate to [http://localhost:8888/simple-student-portal/login.html](http://localhost:8888/simple-student-portal/login.html).

## Usage

### Login:

1. Navigate to the login page at [http://localhost:8888/simple-student-portal/login.html](http://localhost:8888/simple-student-portal/login.html).
2. Enter your User ID and Password.

### Dashboard:

1. Upon successful login, you will be redirected to `dashboard.php`.
2. View your profile details.
3. View your grades and attendance records.
4. View the attendance chart.

## Project Structure

- `login.html`: The login page.
- `login.php`: The login form handler.
- `dashboard.php`: The main dashboard for viewing student details, grades, and attendance.
