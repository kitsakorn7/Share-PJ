<head>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>

<?php
session_start();

// เชื่อมต่อฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projecta";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลที่ต้องการอัปเดตจากฟอร์ม
$id = $_POST['id'];
$room_number = $_POST['room_number'];
$floor = $_POST['floor'];
$building = $_POST['building'];

// เตรียมคำสั่ง SQL เพื่ออัปเดตข้อมูล
$sql = "UPDATE classrooms SET room_number='$room_number', floor='$floor', building='$building' WHERE id=$id";

// ทำการอัปเดตข้อมูล
if ($conn->query($sql) === TRUE) {
    // อัปเดตสำเร็จ แสดง modal popup ด้วย JS
    echo "
    <script>
        window.onload = function() {
            var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
            updateModal.show();
        }
    </script>
    ";
} else {
    echo "Error updating record: " . $conn->error;
}

// ทำการอัปเดตข้อมูลในฐานข้อมูลและกำหนดข้อความแจ้งเตือน
if ($conn->query($sql) === TRUE) {
    $_SESSION['modalMessage'] = "Data update successful.";
} else {
    $_SESSION['modalMessage'] = "An error occurred. " . $conn->error;
}

// ปิดการเชื่อมต่อ
$conn->close();

// ส่งผู้ใช้ไปยัง display.php
header("Location: display_classrooms.php?showModal=true");
exit();
?>


