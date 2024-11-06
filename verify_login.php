<?php
session_start();

// เชื่อมต่อฐานข้อมูล (ปรับให้ตรงกับการตั้งค่าของคุณ)
try {
    $conn = new PDO("mysql:host=localhost;dbname=projecta", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// รับข้อมูลผู้ใช้จาก URL
$userEmail = isset($_GET['user']) ? htmlspecialchars(trim($_GET['user'])) : '';
$userName = isset($_GET['name']) ? htmlspecialchars(trim($_GET['name'])) : '';
$userImage = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : (isset($_SESSION['userImage']) ? $_SESSION['userImage'] : '');

// บันทึกข้อมูลลงใน Session หากมีข้อมูลใหม่
if (!empty($userEmail)) {
    $_SESSION['userEmail'] = $userEmail;
    $_SESSION['userName'] = $userName;
    $_SESSION['userImage'] = $userImage;
}

// ตัวแปรเพื่อตรวจสอบประเภทผู้ใช้
$isAdmin = false;
$isTeacher = false;

try {
    // ตรวจสอบในตาราง admins
    $stmt = $conn->prepare("SELECT email FROM admins WHERE email = :email");
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // ถ้าพบอีเมลในตาราง admins
        $isAdmin = true;
    } else {
        // ตรวจสอบในตาราง teachers (ถ้าคุณมีตารางนี้สำหรับ teachers)
        $stmt = $conn->prepare("SELECT email FROM teachers WHERE email = :email");
        $stmt->bindParam(':email', $userEmail);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // ถ้าพบอีเมลในตาราง teachers
            $isTeacher = true;
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// ใช้ข้อมูลนี้ในการแสดงผลหรือทำการแยกประเภทหน้าที่ของผู้ใช้
if ($isAdmin) { 
    // แสดงหน้าสำหรับ Admin
    header('Location: http://localhost/myproject/learn-reactjs-2024/web_app/admin/admin_home.php');
    exit();
} elseif ($isTeacher) {
    // แสดงหน้าสำหรับ Teacher
    header('Location: http://localhost/myproject/learn-reactjs-2024/course-app/tabledetails.php');
    exit();
} else {
    // ถ้าไม่ใช่ teacher หรือ admin
    echo 'Access denied. Your email is not allowed.';
    exit();
}
?>
