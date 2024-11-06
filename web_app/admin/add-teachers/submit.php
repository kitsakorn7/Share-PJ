<?php
session_start();
require "config.php"; // เปลี่ยนเส้นทางไปยังไฟล์การเชื่อมต่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$teacher_id = $_POST['teacher_id'];
$faculty = $_POST['faculty'];
$department = $_POST['department'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$first_name_eng = $_POST['first_name_eng'];
$last_name_eng = $_POST['last_name_eng'];
$email = $_POST['email'];

// เตรียมคำสั่ง SQL
$sql = "INSERT INTO teachers (teacher_id, faculty, department, first_name, last_name, first_name_eng, last_name_eng, email)
        VALUES ('$teacher_id', '$faculty', '$department', '$first_name', '$last_name', '$first_name_eng', '$last_name_eng', '$email')";

if ($conn->query($sql) === TRUE) {
    // ถ้าบันทึกสำเร็จ
    $_SESSION['result_message'] = 'Data updated successfully.';
    $_SESSION['result_type'] = 'success';
} else {
    // ถ้าบันทึกไม่สำเร็จ
    $_SESSION['result_message'] = 'An error occurred. ' . $conn->error;
    $_SESSION['result_type'] = 'error';
}

// ปิดการเชื่อมต่อ
$conn->close();

// เปลี่ยนเส้นทางกลับไปที่ index.php
header("Location: index.php");
exit();
?>
