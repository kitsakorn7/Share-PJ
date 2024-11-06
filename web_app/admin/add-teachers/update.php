<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projecta";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$id = $_POST['id'];
$teacher_id = $_POST['teacher_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$first_name_eng = $_POST['first_name_eng'];
$last_name_eng = $_POST['last_name_eng'];
$email = $_POST['email'];
$faculty = $_POST['faculty'];
$department = $_POST['department'];

// เตรียมคำสั่ง SQL สำหรับการอัปเดตข้อมูล
$sql = "UPDATE teachers SET teacher_id='$teacher_id', first_name='$first_name', last_name='$last_name', 
        first_name_eng='$first_name_eng', last_name_eng='$last_name_eng', email='$email', 
        faculty='$faculty', department='$department' WHERE id='$id'";

// ทำการอัปเดตข้อมูลในฐานข้อมูลและกำหนดข้อความแจ้งเตือน
if ($conn->query($sql) === TRUE) {
    $_SESSION['modalMessage'] = "Data update successful.";
} else {
    $_SESSION['modalMessage'] = "An error occurred. " . $conn->error;
}

// ปิดการเชื่อมต่อ
$conn->close();

// ส่งผู้ใช้ไปยัง display.php
header("Location: display.php?showModal=true");
exit();
?>
