<!-- PHP ของ manage-members.php -->
<?php
session_start(); // เริ่มต้น session
include('config.php'); // เชื่อมต่อฐานข้อมูล

// รับ subject_id ที่ส่งมาจาก timetable (index.php)
if (isset($_GET['subject_id'])) {
    $subject_id = $_GET['subject_id'];
    $academic_semester = $_GET['academic_semester'];
    $section = $_GET['section'];

    // เชื่อมต่อฐานข้อมูล นำค่าภายใน config.php มาใส่ตัวแปร $conn
    $conn = new mysqli($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // เตรียมข้อมูล subject_id และ section ใน SQL
    $sql = "SELECT * FROM courses WHERE subject_id = ? AND section = ?";

    // ใช้ prepared statement เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $subject_id, $section);  // "ss" หมายถึง subject_id และ section เป็น string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    
        // Backup ข้อมูลจาก database ขึ้น $_SESSION
        $_SESSION['id'] = $row["id"];
        $_SESSION['subject_name'] = $row["subject_name"];
        $_SESSION['subject_id'] = $row["subject_id"];
        $_SESSION['classroom_id'] = $row["classroom_id"];
        $_SESSION['theory_hours'] = $row["theory_hours"];
        $_SESSION['practical_hours'] = $row["practical_hours"];
        $_SESSION['semester'] = $row["semester"];
        $_SESSION['academic_year'] = $row["academic_year"];
        $_SESSION['day_of_week'] = $row["day_of_week"];
        $_SESSION['start_time'] = $row["start_time"];
        $_SESSION['end_time'] = $row["end_time"];

        $_SESSION['section'] = $section;
        $_SESSION['academic_semester'] = $academic_semester;
    
        // นำข้อมูลจาก $_SESSION มาใส่ในตัวแปร
        $id = $_SESSION['id'];
        $subject_name = $_SESSION['subject_name'];
        $subject_id = $_SESSION['subject_id'];
        $classroom_id = $_SESSION['classroom_id'];
        $theory_hours = $_SESSION['theory_hours'];
        $practical_hours = $_SESSION['practical_hours'];
        $semester = $_SESSION['semester'];
        $academic_year = $_SESSION['academic_year'];
        $day_of_week = $_SESSION['day_of_week'];
        $start_time = $_SESSION['start_time'];
        $end_time = $_SESSION['end_time'];
        
        $section = $_SESSION['section'];
        $academic_semesterNav = $_SESSION['academic_semester'];
    
        // เตรียมคำสั่ง SQL สำหรับดึงข้อมูลจาก classrooms โดยใช้ classroom_id
        $sql_classroom = "SELECT room_number, floor, building FROM classrooms WHERE id = ?";
        $stmt_classroom = $conn->prepare($sql_classroom);
        $stmt_classroom->bind_param("i", $classroom_id); // "i" หมายถึง classroom_id เป็น integer
        $stmt_classroom->execute();
        $result_classroom = $stmt_classroom->get_result();

        if ($result_classroom->num_rows > 0) {
            $classroom = $result_classroom->fetch_assoc();
            
            // เก็บข้อมูลลงในตัวแปรเพื่อแสดงผล
            $room_number = $classroom['room_number'];
            $floor = $classroom['floor'];
            $building = $classroom['building'];
            
        } else {
            echo "Classroom not found.";
        }

        // ตั้งชื่อ table สำหรับการนำเข้านักศึกษา
        $table_name = "section_" . preg_replace('/\s+/', '_', $subject_name) . "_" . $section . "_" . $semester . "_" . $academic_year;
    
        // สร้างตารางหากยังไม่มี (ตารางนี้มีไว้เก็บข้อมูลนักศึกษาที่ถูก import มาใน section)
        $sql_create_table = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT(11) NOT NULL AUTO_INCREMENT,
            image LONGBLOB,
            image1 LONGBLOB,
            image2 LONGBLOB,
            first_name VARCHAR(20) NOT NULL,
            last_name VARCHAR(20) NOT NULL,
            student_number VARCHAR(20) NOT NULL,
            Faculty VARCHAR(255) NOT NULL,
            Field_of_study VARCHAR(255) NOT NULL,
            PRIMARY KEY (id),
            FOREIGN KEY (student_number) REFERENCES images(student_number)
        )";
    
        // สร้างตาราง
        if ($conn->query($sql_create_table) === TRUE) {
            // echo "Table $table_name created successfully. "; // แสดงข้อความเมื่อสร้างตาราง section สำเร็จ
        } else {
            echo "Error creating table: " . $conn->error;
        }
    
        // ตั้งชื่อ table สำหรับการเช็คชื่อ
        $table_weeks_name = "attendance_" . preg_replace('/\s+/', '_', $subject_name) . "_" . $section . "_" . $semester . "_" . $academic_year;
    
        // สร้างตารางสำหรับการเช็คชื่อ (ตารางนี้มีไว้เก็บข้อมูล 15 week เพื่อเตรียมข้อมูลสำหรับจากนำไปเช็คชื่อ)
        $sql_create_weeks_table = "CREATE TABLE IF NOT EXISTS $table_weeks_name (
            week_number INT AUTO_INCREMENT PRIMARY KEY,
            week_date DATE NOT NULL,
            on_time_time TIME NOT NULL DEFAULT '09:00:00',
            late_time TIME NOT NULL DEFAULT '09:15:00',
            absent_time TIME NOT NULL DEFAULT '12:00:00'
        )";

        // สร้างตาราง
        if ($conn->query($sql_create_weeks_table) === TRUE) {
            // echo "Table $table_weeks_name created successfully. "; // แสดงข้อความเมื่อสร้างตาราง Attendance สำเร็จ

            // ตรวจสอบว่ามี records อยู่แล้วกี่แถวในตาราง
            $sql_count_weeks = "SELECT COUNT(*) as count FROM $table_weeks_name";
            $result_count = $conn->query($sql_count_weeks);
            $row_count = $result_count->fetch_assoc();
            $current_count = (int) $row_count['count'];

            // ถ้ามี records น้อยกว่า 15 ให้แทรกข้อมูลเพิ่ม
            if ($current_count < 15) {
                for ($i = $current_count + 1; $i <= 15; $i++) {
                    $week_date = date('Y-m-d', strtotime("+$i week")); // จัดเก็บในฐานข้อมูลด้วยรูปแบบ Y-m-d
                    $sql_insert_week = "INSERT INTO $table_weeks_name (week_date) VALUES ('$week_date')";
                    if ($conn->query($sql_insert_week) === TRUE) {
                        /*echo "Week $i data inserted successfully.<br>";*/
                    } else {
                        echo "Error inserting week $i data: " . $conn->error . "<br>";
                    }
                }
            }
            
        } else {
            echo "Error creating weeks table: " . $conn->error;
        }
        
        // ดึงข้อมูลจากตาราง
        $sql_select = "SELECT * FROM $table_name";
        $result = $conn->query($sql_select);
    
    } else {
        echo "No course found.";
    }
        
    $conn->close();
} else {
    echo "No subject ID provided.";
}

//Create var Link
$url_members = './manage-members.php?table_name=' . urlencode($table_name) . '&subject_id=' . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
$url_attendance = '../attendance-check.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
$url_report = '../report-history/summary_report.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
$url_calendar = '../calendar/calendar.php?table_name=' . urlencode($table_name) . '&subject_id=' . urlencode($subject_id) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);

?>
