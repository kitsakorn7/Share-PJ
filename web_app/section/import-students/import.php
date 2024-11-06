<?php
session_start(); // เริ่มต้น session
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projecta";

// สร้างการเชื่อมต่อกับ MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีไฟล์อัปโหลดเข้ามาหรือไม่
if (isset($_FILES['excel_file']['tmp_name'][0])) {
    // รับค่าจากฟอร์มและแปลงเป็นพิมพ์เล็ก
    $table_name = isset($_POST['table_name']) ? strtolower($_POST['table_name']) : '';
    $subject_id = $_SESSION['subject_id'];
    $section = $_SESSION['section'];
    $academic_semesterNav = $_SESSION['academic_semester'];

    // ตรวจสอบว่าค่าของ $table_name ไม่ใช่ค่าว่าง
    if (!empty($table_name)) {
        // ขั้นตอนที่ 1: เรียก API เพื่อตรวจสอบสถานะ
        $data = array('table_name' => $table_name);
        $url = 'http://localhost:5000/recognize'; // URL ของ Python API

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
                'timeout' => 5, // กำหนด timeout เป็น 5 วินาที
            ),
        );

        $context = stream_context_create($options);

        try {
            // ลองเรียก API และตรวจสอบว่ามีการตอบสนองหรือไม่
            $result = @file_get_contents($url, false, $context); // ใช้ @ เพื่อปิดการแสดงข้อผิดพลาด
            if ($result === FALSE) {
                // บันทึกข้อความแจ้งเตือนลงใน session หาก API ไม่ตอบสนอง
                $_SESSION['alert_message'] = "Warning: API server is not responding. No data has been imported.";
                header("Location: manage-members.php?table_name=" . urlencode($table_name) . "&subject_id=" . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section));
                exit(); // ยุติการทำงานที่นี่หาก API ไม่ตอบสนอง
            }
        } catch (Exception $e) {
            // จัดการข้อผิดพลาดทั่วไป และบันทึกข้อความแจ้งเตือน
            $_SESSION['alert_message'] = "Error: Could not connect to API. Exception: " . $e->getMessage();
            header("Location: manage-members.php?table_name=" . urlencode($table_name) . "&subject_id=" . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section));
            exit(); // ยุติการทำงานที่นี่หากเกิดข้อผิดพลาด
        }

        // ขั้นตอนที่ 2: ถ้า API ตอบสนอง ทำการอ่านและบันทึกข้อมูลจากไฟล์ Excel ลงฐานข้อมูล
        foreach ($_FILES['excel_file']['tmp_name'] as $key => $tmp_name) {
            // อ่านไฟล์ Excel แต่ละไฟล์
            $spreadsheet = IOFactory::load($tmp_name);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            foreach ($sheetData as $row) {
                $student_number = $row['A'];
                $first_name = $row['B'];
                $last_name = $row['C'];
                $faculty = $row['D'];
                $field_of_study = $row['E'];

                if (empty($student_number)) {
                    echo "Error: student_number cannot be empty.";
                    continue; // ข้ามแถวนี้ไป
                }

                // ตรวจสอบว่านักศึกษามีอยู่ในฐานข้อมูลแล้วหรือไม่
                $check_stmt = $conn->prepare("SELECT COUNT(*) FROM $table_name WHERE student_number = ?");
                $check_stmt->bind_param("s", $student_number);
                $check_stmt->execute();
                $check_stmt->bind_result($count);
                $check_stmt->fetch();
                $check_stmt->close();

                if ($count > 0) {
                    // อัปเดตข้อมูลนักศึกษา
                    $stmt = $conn->prepare("UPDATE $table_name SET first_name = ?, last_name = ?, Faculty = ?, Field_of_study = ? WHERE student_number = ?");
                    $stmt->bind_param("sssss", $first_name, $last_name, $faculty, $field_of_study, $student_number);
                } else {
                    // เพิ่มข้อมูลนักศึกษาใหม่
                    $stmt = $conn->prepare("INSERT INTO $table_name (student_number, first_name, last_name, Faculty, Field_of_study) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $student_number, $first_name, $last_name, $faculty, $field_of_study);
                }

                $stmt->execute();
                $stmt->close();
            }
        }

        // อัปเดตข้อมูลรูปภาพ
        $updateStmt = $conn->prepare("
            UPDATE $table_name s
            JOIN images i ON s.student_number = i.student_number
            SET s.image = i.image, s.image1 = i.image1, s.image2 = i.image2
            WHERE s.student_number = i.student_number
        ");
        $updateStmt->execute();
        $updateStmt->close();

        // ขั้นตอนที่ 3: เรียก API อีกครั้งเพื่อประมวลผลเพิ่มเติม
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            echo "Error calling API.";
        }

        $_SESSION['alert_message'] = "Data and facial recognition processed successfully.";
        
        // รีไดเร็กต์ไปยังหน้า manage-members
        header("Location: manage-members.php?table_name=" . urlencode($table_name) . "&subject_id=" . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section));
        exit();
    } else {
        echo "Error: Table name is required!";
    }
}
?>
