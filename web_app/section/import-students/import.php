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

    // ตรวจสอบว่าค่าของ $table_name ไม่ใช่ค่าว่าง
    if (!empty($table_name)) {
        // วนลูปผ่านไฟล์แต่ละไฟล์ที่ถูกอัปโหลด
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

                // ตรวจสอบว่า $student_number มีค่าและไม่เป็นค่าว่าง
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
                    // ถ้ามีข้อมูลนักศึกษานี้ในฐานข้อมูลแล้ว ให้อัปเดตข้อมูลใหม่
                    $stmt = $conn->prepare("UPDATE $table_name SET first_name = ?, last_name = ?, Faculty = ?, Field_of_study = ? WHERE student_number = ?");
                    $stmt->bind_param("sssss", $first_name, $last_name, $faculty, $field_of_study, $student_number);
                } else {
                    // ถ้าไม่มีข้อมูลนักศึกษานี้ในฐานข้อมูล ให้เพิ่มข้อมูลใหม่
                    $stmt = $conn->prepare("INSERT INTO $table_name (student_number, first_name, last_name, Faculty, Field_of_study) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $student_number, $first_name, $last_name, $faculty, $field_of_study);
                }

                // รันคำสั่ง SQL
                $stmt->execute();
                $stmt->close();
            }
        }

        // อัปเดตข้อมูลรูปภาพโดยการ Join กับตาราง images
        $updateStmt = $conn->prepare("
            UPDATE $table_name s
            JOIN images i ON s.student_number = i.student_number
            SET s.image = i.image,
                s.image1 = i.image1,
                s.image2 = i.image2
            WHERE s.student_number = i.student_number
        ");
        $updateStmt->execute();
        $updateStmt->close();

        $conn->close();

        // เรียก Python API เพื่อประมวลผลและบันทึกใบหน้า
        $data = array('table_name' => $table_name);
        $url = 'http://localhost:5000/recognize'; // URL ของ Python API

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            // จัดการกรณีที่เกิดข้อผิดพลาด
            echo "Error calling API.";
        }

        // รีไดเร็กต์ไปยังหน้า manage-members
        header("Location: manage-members.php?table_name=" . urlencode($table_name) . "&subject_id=" . urlencode($subject_id));
        exit(); // ให้แน่ใจว่าการทำงานของสคริปต์หยุดหลังจากการรีไดเร็กต์
    } else {
        echo "Error: Table name is required!";
    }
}
?>
