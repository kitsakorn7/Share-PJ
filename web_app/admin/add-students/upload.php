<?php
session_start();
require "config.php";

function compressImage($file) {
    $imageContent = file_get_contents($file);
    $sourceImage = imagecreatefromstring($imageContent);
    ob_start();
    imagejpeg($sourceImage, null, 80);
    $compressedImageContent = ob_get_clean();
    imagedestroy($sourceImage);
    return $compressedImageContent;
}

if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $image = $_FILES["image"]["tmp_name"];
    $image1 = $_FILES["image1"]["tmp_name"];
    $image2 = $_FILES["image2"]["tmp_name"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $student_number = $_POST["student_number"];
    $Faculty = $_POST["Faculty"];
    $Field_of_study = $_POST["Field_of_study"];

    $imgContent = compressImage($image);
    $imgContent1 = compressImage($image1);
    $imgContent2 = compressImage($image2);

 try {
    $stmt = $conn->prepare("INSERT INTO images (image, image1, image2, first_name, last_name, student_number, Faculty, Field_of_study) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$imgContent, $imgContent1, $imgContent2, $first_name, $last_name, $student_number, $Faculty, $Field_of_study]);

    // ถ้าสำเร็จ ให้แสดงข้อความใน session
    $_SESSION["success"] = "Image uploaded successfully.";
} catch (PDOException $e) {
    // ตรวจสอบว่าเป็นข้อผิดพลาด "Duplicate entry" หรือไม่
    if ($e->getCode() == 23000) {
        $_SESSION["error"] = "Error: Student ID ซ้ำ กรุณากรอกข้อมูลใหม่.";
    } else {
        // ข้อผิดพลาดอื่นๆ
        $_SESSION["error"] = "Failed to upload image: " . $e->getMessage();
    }
}
} else {
$_SESSION["error"] = "Please select an image file to upload.";
}

header("Location: index.php");
?>