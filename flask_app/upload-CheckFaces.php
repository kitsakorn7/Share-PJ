<?php
session_start(); // เริ่มต้น session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "uploads/";
    $uploadOk = 1;
    $allowed_file_types = ['jpg', 'jpeg', 'png', 'gif'];
    $max_file_size = 5000000; // 5MB

    if (!isset($_FILES["images"]) || !is_array($_FILES["images"]["name"])) {
        echo "No files were uploaded.";
        exit();
    }

    $_SESSION['section'] = isset($_POST['section']) ? $_POST['section'] : '';
    $_SESSION['table_name'] = isset($_POST['table_name']) ? $_POST['table_name'] : '';
    $_SESSION['table_weeks_name'] = isset($_POST['table_weeks_name']) ? $_POST['table_weeks_name'] : '';
    $_SESSION['academic_semester'] = isset($_POST['academic_semester']) ? $_POST['academic_semester'] : '';
    
    $table_name = $_SESSION['table_name'];
    $academic_semesterNav = $_SESSION['academic_semester'];
    $section = $_SESSION['section'];
    
    $week_date = isset($_POST['week_date']) ? $_POST['week_date'] : '';
    $week_number = isset($_POST['week_number']) ? $_POST['week_number'] : '';
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $subject = isset($_POST['subject']) ? $_POST['subject'] : '';
    $on_time_time = isset($_POST['on_time_time']) ? $_POST['on_time_time'] : '';
    $late_time = isset($_POST['late_time']) ? $_POST['late_time'] : '';
    $absent_time = isset($_POST['absent_time']) ? $_POST['absent_time'] : '';

    
    $images = $_FILES["images"];
    $results = array();
    $unique_faces = array();
    $uploaded_images = array();

    for ($i = 0; $i < count($images["name"]); $i++) {
        $target_file = $target_dir . basename($images["name"][$i]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (!in_array($imageFileType, $allowed_file_types)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($images["size"][$i] > $max_file_size) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        $check = getimagesize($images["tmp_name"][$i]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($images["tmp_name"][$i], $target_file)) {
                $exif_data = exif_read_data($target_file);
                $image_time = isset($exif_data['DateTimeOriginal']) ? $exif_data['DateTimeOriginal'] : date("Y-m-d H:i:s");

                date_default_timezone_set('Asia/Bangkok');
                $upload_time = date("Y-m-d H:i:s");

                $url = 'http://localhost:8000/faces_recognition_Project';
                $cfile = new CURLFile($target_file, mime_content_type($target_file), basename($target_file));

                $data = array(
                    'file' => $cfile,
                    'table_name' => $table_name
                );

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($response, true);

                $image_data = base64_decode($result['image']);
                $output_file = $target_dir . 'result_' . basename($images["name"][$i]);
                file_put_contents($output_file, $image_data);

                foreach ($result['faces'] as $index => $name) {
                    if (!isset($unique_faces[$name]) || strtotime($image_time) < strtotime($unique_faces[$name]['image_time'])) {
                        $unique_faces[$name] = array(
                            'image' => $output_file,
                            'image_time' => $image_time,
                            'upload_time' => $upload_time,
                            'stdId' => $result['stdId'][$index]
                        );
                    }
                }

                // เก็บข้อมูลของรูปภาพที่อัปโหลด
                $uploaded_images[] = array(
                    'file' => $target_file,
                    'image' => $output_file,
                    'upload_time' => $upload_time
                );
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    $results = array();
    foreach ($unique_faces as $name => $face_data) {
        $results[] = array(
            'faces' => array($name),
            'stdId' => $face_data['stdId'],
            'image' => $face_data['image'],
            'image_time' => $face_data['image_time'],
            'upload_time' => $face_data['upload_time']
        );
    }

    $result_data = json_encode($results);
    $uploaded_images_data = json_encode($uploaded_images);

    $redirect_url = 'section-result.php?data=' . urlencode($result_data) . '&uploaded_images=' . urlencode($uploaded_images_data) . '&week_date=' . urlencode($week_date) . '&week_number=' . urlencode($week_number) . '&id=' . urlencode($id) . '&subject=' . urlencode($subject) . '&on_time_time=' . urlencode($on_time_time) . '&late_time=' . urlencode($late_time) . '&absent_time=' . urlencode($absent_time) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);

    header('Location: ' . $redirect_url);
    exit();
}
?>
