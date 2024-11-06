<?php
// การเชื่อมต่อกับฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projecta";

// ตรวจสอบว่ามีข้อมูลที่ส่งมาและไม่ว่างเปล่าหรือไม่
if (!isset($_POST['room_number']) || !isset($_POST['floor']) || !isset($_POST['building']) || empty($_POST['room_number']) || empty($_POST['floor']) || empty($_POST['building'])) {
    // ถ้าข้อมูลไม่ครบหรือว่างเปล่า ให้ Redirect กลับไปยังหน้า index.html พร้อมกับการส่งพารามิเตอร์ error
    header("Location: index.html?error=Please fill out all fields");
    exit(); // ออกจากสคริปต์เพื่อป้องกันการทำงานต่อ
}

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$room_number = $_POST['room_number'];
$floor = $_POST['floor'];
$building = $_POST['building'];

// เตรียมคำสั่ง SQL เพื่อเพิ่มข้อมูลลงในฐานข้อมูล
$sql = "INSERT INTO classrooms (room_number, floor, building) VALUES (?, ?, ?)";

// เต prepared statement
$stmt = $conn->prepare($sql);

// ตรวจสอบว่า prepared statement สามารถถูกสร้างได้หรือไม่
if ($stmt) {
    // Bind parameters
    $stmt->bind_param("sss", $room_number, $floor, $building);

    // Execute the statement
    if ($stmt->execute()) {
        // สร้างการ Redirect ไปยังหน้า index.html พร้อมกับการส่งพารามิเตอร์ success
        header("Location: index.php?success=true");
        exit(); // ออกจากสคริปต์เพื่อป้องกันการทำงานต่อ
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error: Unable to prepare statement";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
