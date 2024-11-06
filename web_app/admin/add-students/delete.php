<?php
require "config.php";

// ตรวจสอบว่ามีการส่งค่ามาหรือไม่
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ไม่มีรหัสนักศึกษา";
    header("Location: std_list.php");
    exit();
}

// รับค่าหมายเลขนักเรียนที่ส่งมาจาก URL
$idList = $_GET['id'];

// ตรวจสอบว่า id เป็น 'all' หรือไม่
if ($idList === 'all') {
    try {
        // ลบข้อมูลทั้งหมดจากฐานข้อมูล
        $stmt = $conn->prepare("DELETE FROM images"); // เปลี่ยน 'images' เป็นชื่อที่ถูกต้องของตารางคุณ
        $stmt->execute();

        // ตรวจสอบการลบข้อมูล
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "ลบข้อมูลนักเรียนทั้งหมดเรียบร้อยแล้ว";
        } else {
            $_SESSION['error'] = "ไม่สามารถลบข้อมูลนักเรียนได้ หรือไม่พบข้อมูล";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage();
    }
} else {
    // แยก ID ออกเป็นอาเรย์
    $idArray = explode(',', $idList);
    // สร้างคำสั่ง SQL สำหรับลบข้อมูลหลายแถว
    $placeholders = rtrim(str_repeat('?,', count($idArray)), ','); // สร้าง placeholder เช่น ?,?,?

    try {
        // ลบข้อมูลจากฐานข้อมูล
        $stmt = $conn->prepare("DELETE FROM images WHERE id IN ($placeholders)");
        $stmt->execute($idArray); // ส่งอาเรย์ ID เป็นพารามิเตอร์

        // ตรวจสอบการลบข้อมูล
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "ลบข้อมูลนักเรียนเรียบร้อยแล้ว";
        } else {
            $_SESSION['error'] = "ไม่สามารถลบข้อมูลนักเรียนได้ หรือไม่พบข้อมูล";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage();
    }
}

// รีไดเร็กต์ไปยังหน้า std_list.php
header("Location: std_list.php");
exit();
?>
