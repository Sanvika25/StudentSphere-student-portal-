<?php
// Establish database connection
$servername = "localhost";
$username = "php";
$password = "1234";
$dbname = "student_portal";
$port = 8889;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming $user_id is obtained from session or input
session_start();
$user_id = $_SESSION['user_id']; // Example: you might set this during login

// Fetch student details using user_id (which is the same as student_id)
$sql_student = "SELECT * FROM students WHERE student_id = '$user_id'";
$result_student = $conn->query($sql_student);

if ($result_student->num_rows > 0) {
    $student_data = $result_student->fetch_assoc();
} else {
    die("No student data found");
}

// Fetch grades
$sql_grades = "SELECT * FROM grades WHERE student_id = '$user_id'";
$result_grades = $conn->query($sql_grades);

// Fetch attendance
$sql_attendance = "SELECT * FROM attendance WHERE student_id = '$user_id'";
$result_attendance = $conn->query($sql_attendance);

// Fetch attendance data for chart
$attendanceData = [];
while ($row = $result_attendance->fetch_assoc()) {
    $attendanceData[] = $row;
}

// Calculate attendance percentages
$attendance_summary = [];
foreach ($attendanceData as $row) {
    $course_id = $row['course_id'];
    if (!isset($attendance_summary[$course_id])) {
        $attendance_summary[$course_id] = ['present' => 0, 'absent' => 0];
    }
    if ($row['status'] == 'Present') {
        $attendance_summary[$course_id]['present']++;
    } else {
        $attendance_summary[$course_id]['absent']++;
    }
}
foreach ($attendance_summary as $course_id => $summary) {
    $total = $summary['present'] + $summary['absent'];
    $attendance_summary[$course_id]['present_percent'] = ($summary['present'] / $total) * 100;
    $attendance_summary[$course_id]['absent_percent'] = ($summary['absent'] / $total) * 100;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #007bff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        th:first-child, td:first-child {
            width: 30%;
        }

        th:nth-child(2), td:nth-child(2) {
            width: 20%;
        }

        th:nth-child(3), td:nth-child(3) {
            width: 15%;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .attendance-chart {
            margin-top: 20px;
        }

        .logout-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .logout-link:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($student_data['student_name']); ?>!</h1>

        <div class="profile-section">
            <h2>Profile</h2>
            <p>Student ID: <?php echo htmlspecialchars($student_data['student_id']); ?></p>
            <p>Email: <?php echo htmlspecialchars($student_data['student_email']); ?></p>
            <p>Date of Birth: <?php echo htmlspecialchars($student_data['student_dob']); ?></p>
            <p>Address: <?php echo htmlspecialchars($student_data['student_address']); ?></p>
            <p>Academic Year: <?php echo htmlspecialchars($student_data['academic_year']); ?></p>
            <p>Branch: <?php echo htmlspecialchars($student_data['branch']); ?></p>
        </div>

        <div class="grades-section">
            <h2>Grades</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Grade</th>
                        <th>Grade Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result_grades->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['course_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['grade']); ?></td>
                        <td><?php echo htmlspecialchars($row['grade_date']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="attendance-section">
            <h2>Attendance</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Attendance Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceData as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['course_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['attendance_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="attendance-chart">
            <h2>Attendance Chart</h2>
            <canvas id="attendanceChart" width="400" height="200"></canvas>
        </div>

        <a href="logout.php" class="logout-link">Logout</a>
    </div>

    <script>
        // JavaScript code for attendance chart
        var attendanceData = <?php echo json_encode($attendance_summary); ?>;
        var labels = Object.keys(attendanceData);
        var presentData = labels.map(function(course_id) {
            return attendanceData[course_id].present_percent;
        });
        var absentData = labels.map(function(course_id) {
            return attendanceData[course_id].absent_percent;
        });

        var ctx = document.getElementById('attendanceChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Present (%)',
                        data: presentData,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Absent (%)',
                        data: absentData,
                        backgroundColor: 'rgba(255, 99, 132, 0.6)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    </script>
</body>
</html>
