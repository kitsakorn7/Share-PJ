<?php
session_start();
include('config.php');

if (isset($_POST['ids']) && isset($_SESSION['subject_id'])) {
    $ids = $_POST['ids'];
    $table_name = $_POST['table_name']; // รับจาก POST

    $academic_semesterNav = $_SESSION['academic_semester'];
    $subject_id = $_SESSION['subject_id'];
    $section = $_SESSION['section'];

    // เชื่อมต่อฐานข้อมูล
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // สร้างคำสั่ง SQL สำหรับการลบหลายรายการ
    $ids_placeholder = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM $table_name WHERE id IN ($ids_placeholder)";
    $stmt = $conn->prepare($sql);

    // ผูกค่า $ids กับคำสั่ง SQL
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);

    if ($stmt->execute()) {
        // อัปเดตลำดับ ID ใหม่
        $sql_update_id = "
            SET @new_id = 0;
            UPDATE $table_name SET id = (@new_id := @new_id + 1) ORDER BY id ASC;
            ALTER TABLE $table_name AUTO_INCREMENT = 1;
        ";

        if ($conn->multi_query($sql_update_id)) {
            do {
                // รอให้ multi_query เสร็จ
            } while ($conn->next_result());
        } else {
            echo "Error updating IDs: " . $conn->error;
        }

        // เรียก Python API เพื่ออัปเดตไฟล์ .json
        $data = array('table_name' => $table_name);
        $url = 'http://localhost:5000/recognize_clear'; // URL ของ Python API

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
            echo "Error calling API to update JSON file.";
        } else {
            // ส่งค่ากลับไปยัง manage-members Page หลังจากเรียกใช้ API เสร็จ
            header("Location: manage-members.php?table_name=" . urlencode($table_name) . "&subject_id=" . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section) . "&message=Records deleted and IDs updated successfully");
            exit;
        }
    } else {
        echo "Error deleting records: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
