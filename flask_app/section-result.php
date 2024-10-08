<?php
session_start(); // เริ่มต้น session

$table_name = $_SESSION['table_name'];
$table_weeks_name = $_SESSION['table_weeks_name'];
$academic_semesterNav = $_SESSION['academic_semester'];
$semester = $_SESSION['semester'];
$section = $_SESSION['section'];
$academic_year = $_SESSION['academic_year'];

$subject_id = $_SESSION['subject_id'];

//Create var Link
$url_members = '../web_app/section/import-students/manage-members.php?table_name=' . urlencode($table_name) . '&subject_id=' . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section) ;
$url_attendance = '../web_app/section/attendance-check.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
$url_report = '../web_app/section/report-history/summary_report.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Face Recognition Result</title>
    <style>
        .navbar {
            color: black;
        }
        .nav-item {
            margin-left: 15px; /* ระยะห่างระหว่างแต่ละปุ่ม */
        }
        .navbar-custom .nav-link {
                color: rgb(46, 46, 46);
                padding-bottom: 5px;
                position: relative;
            }

        .navbar-custom .nav-link::after {
            content: "";
            display: block;
            width: 0;
            height: 2px;
            height: 4px; /* ปรับความหนาของเส้น */
            background-color: #7124ff; /* สีของเส้นใต้ */
            position: absolute;
            bottom: 0;
            left: 0;
            transition: width 0.3s ease;
        }
        .navbar-custom .nav-link:hover::after,
        .navbar-custom .nav-link.active::after {
            width: 100%;
        }

        /* Small devices (Phone 0-576px) */
        @media (max-width: 576px) {
            .nav-item {
                font-size: 13px !important;
            }
            .container {
                max-width: 475px;
                margin: 0px auto;
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;
            }
            h1 {
                margin-bottom: 20px;
                color: #007bff;
            }
            .carousel-item img {
                width: 100%;
                height: 325px; /* กำหนดความสูงของภาพ */
                object-fit: contain; /* ขยายภาพให้พอดีกับกรอบ โดยรักษาสัดส่วนของภาพไว้ทั้งหมด ซึ่งอาจทำให้มีพื้นที่ว่าง */
            }
            .carousel-inner {
                max-width: 375px; /* กำหนดความกว้างของภาพ */
                margin: auto; /* จัดกลาง */
            }
            .carousel-indicators li {
                background-color: black; /* กำหนดสีของจุดที่แสดงตำแหน่ง */
            }
            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                background-color: #000; /* กำหนดสีของปุ่มลูกศร */
                border-radius: 50px;
            }

            .save-summary {
                /* border: 1px solid black; */
                padding-top: 20px;
                /* margin: 0px 20px; */
            }

            /* Right Sidebar */
            .right-group {
                max-width: 475px;
                margin: 50px auto;
                background-color: #f8f9fa; /* เพิ่มพื้นหลังที่ดูเบา */
                border-radius: 10px; /* มุมโค้งมน */
                padding: 20px; /* เพิ่มระยะห่างใน */
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1) !important; /* เงาที่ดูนุ่มนวล */
            }

            .total-profile {
                font-size: 14px; /* ขนาดฟอนต์ที่เหมาะสม */
                color: #333; /* สีข้อความที่ดูง่าย */
                padding: 10px 25px;
                border-bottom: 1px solid #ddd; /* เส้นแบ่งระหว่างแต่ละแถว */
            }

            .total-profile:last-child {
                border-bottom: none; /* เอาเส้นแบ่งออกในแถวสุดท้าย */
            }

            h5 {
                color: #007bff; /* สีที่ทำให้หัวข้อดูเด่น */
            }

            .fas {
                font-size: 20px; /* ขนาดไอคอน */
            }

            strong {
                font-weight: 500;
            }    

            /* ปรับขนาดและจัดตำแหน่งของ canvas */
            #attendanceChart {
                max-width: 100%; /* ให้ canvas ปรับขนาดตามความกว้างของ container */
                height: auto !important; /* ปรับความสูงตามสัดส่วน */
                margin: 0 auto; /* จัดกลางให้ canvas */
                padding: 20px;
                /* border: 2px solid #ddd;  เพิ่มกรอบรอบ canvas */
                border-radius: 10px; /* ทำให้มุมกรอบมน */
                /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);  เพิ่มเงาให้ canvas */
            }

            /* ปรับแต่งตำแหน่ง legend */
            .legend {
                text-align: center; /* จัดตำแหน่ง legend ให้อยู่กลาง */
                margin-top: 20px; /* เพิ่มระยะห่างระหว่าง legend กับ canvas */
            }

            /* ปรับแต่งข้อความใน canvas */
            .canvas-text {
                position: absolute; /* ใช้ตำแหน่ง absolute */
                top: 47%; /* จัดกลางแนวตั้ง */
                left: 50%; /* จัดกลางแนวนอน */
                transform: translate(-50%, -50%); /* ทำให้ข้อความอยู่กลาง */
                font-size: 20px; /* ขนาดฟอนต์ */
                color: black; /* สีข้อความ */
                font-weight: bold; /* น้ำหนักฟอนต์ */
                z-index: 10; /* ทำให้ข้อความอยู่ด้านหน้า */
            }
        }

        /*Medium devices (tablets, 576px and up)*/
        @media (min-width: 576px) { 
            h1 {
                margin-bottom: 20px;
                color: #007bff;
            }
            .carousel-item img {
                width: 100%;
                height: 325px; /* กำหนดความสูงของภาพ */
                object-fit: cover; /* ปรับขนาดภาพเพื่อให้พอดีกับพื้นที่ที่กำหนด */
            }
            .carousel-inner {
                width: 500px; /* กำหนดความกว้างของภาพ */
                margin: auto; /* จัดกลาง */
            }
            .carousel-indicators li {
                background-color: black; /* กำหนดสีของจุดที่แสดงตำแหน่ง */
            }
            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                background-color: #000; /* กำหนดสีของปุ่มลูกศร */
                border-radius: 50px;
            }
            .container {
                max-width: 700px;
                margin: 50px auto;
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;
            }

            /* Right Sidebar */
            .right-group {
                max-width: 700px;
                margin: 50px auto;
                background-color: #f8f9fa; /* เพิ่มพื้นหลังที่ดูเบา */
                border-radius: 10px; /* มุมโค้งมน */
                padding: 20px; /* เพิ่มระยะห่างใน */
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) !important;
            }

            .total-profile {
                font-size: 14px; /* ขนาดฟอนต์ที่เหมาะสม */
                color: #333; /* สีข้อความที่ดูง่าย */
                padding: 10px 25px;
                border-bottom: 1px solid #ddd; /* เส้นแบ่งระหว่างแต่ละแถว */
            }

            .total-profile:last-child {
                border-bottom: none; /* เอาเส้นแบ่งออกในแถวสุดท้าย */
            }

            h5 {
                color: #007bff; /* สีที่ทำให้หัวข้อดูเด่น */
            }

            .fas {
                font-size: 20px; /* ขนาดไอคอน */
            }

            strong {
                font-weight: 500;
            }  
            
            /* ปรับขนาดและจัดตำแหน่งของ canvas */
            #attendanceChart {
                max-width: 100%; /* ให้ canvas ปรับขนาดตามความกว้างของ container */
                height: auto !important; /* ปรับความสูงตามสัดส่วน */
                margin: 0 auto; /* จัดกลางให้ canvas */
                padding: 20px;
                /* border: 2px solid #ddd;  เพิ่มกรอบรอบ canvas */
                border-radius: 10px; /* ทำให้มุมกรอบมน */
                /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);  เพิ่มเงาให้ canvas */
            }

            /* ปรับแต่งตำแหน่ง legend */
            .legend {
                text-align: center; /* จัดตำแหน่ง legend ให้อยู่กลาง */
                margin-top: 20px; /* เพิ่มระยะห่างระหว่าง legend กับ canvas */
            }

            /* ปรับแต่งข้อความใน canvas */
            .canvas-text {
                position: absolute; /* ใช้ตำแหน่ง absolute */
                top: 47%; /* จัดกลางแนวตั้ง */
                left: 50%; /* จัดกลางแนวนอน */
                transform: translate(-50%, -50%); /* ทำให้ข้อความอยู่กลาง */
                font-size: 20px; /* ขนาดฟอนต์ */
                color: black; /* สีข้อความ */
                font-weight: bold; /* น้ำหนักฟอนต์ */
                z-index: 10; /* ทำให้ข้อความอยู่ด้านหน้า */
            }
        }

        /*Large devices (desktops, 992px and up)*/
        @media (min-width: 1320px) { 
            .carousel-item img {
                width: 100%;
                height: 425px; /* กำหนดความสูงของภาพ */
                object-fit: cover; /* ปรับขนาดภาพเพื่อให้พอดีกับพื้นที่ที่กำหนด */
            }
            .carousel-inner {
                width: 700px; /* กำหนดความกว้างของภาพ */
                margin: auto; /* จัดกลาง */
            }
            .carousel-indicators li {
                background-color: #000; /* กำหนดสีของจุดที่แสดงตำแหน่ง */
            }
            .carousel-control-prev-icon,
            .carousel-control-next-icon {
                background-color: #000; /* กำหนดสีของปุ่มลูกศร */
                border-radius: 50px;
            }
            #wrapper {
                display: flex;
                flex-wrap: nowrap;
            }
            .page-content-wrapper {
                flex: 1;
                overflow-y: auto; /* Add vertical scrolling if content overflows */
                padding: 20px;
            }
            .wrapper-page-content {
                display: flex;        
            }    
            .container {
                max-width: 1000px;
                /*margin: 50px auto;*/
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;
            }
            h1 {
                margin-bottom: 20px;
                color: #007bff;
            }
            .nav-item {
                margin-left: 15px; /* ระยะห่างระหว่างแต่ละปุ่ม */
            }
            .totalBar-container {
                display: flex;
                justify-content: space-between ;
                /*border: 1px solid black;*/
            }
            .total-container {
                border: 1px solid black;
                border-radius: 15px;
                padding: 10px;

                width: 300px;
                text-align: center;
            }
            button[type="submit"] {
                background-color: #007bff;
                width: 625px;
                color: #ffffff;
                border: none;
                padding: 10px 20px;
                margin: 10px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 16px;
                transition: background-color 0.3s ease;
            }

            button[type="submit"]:hover {
                background-color: #0056b3;
            }

            /* Right Sidebar */
            .right-group {
                width: 250px;
                height: 600px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1) !important; 
                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: center;
                margin-left: auto;
            }
            .total-profile {
                display: flex;
                justify-content: center;
                align-items: center;

                width: 90%;
                height: 70px;
                border: 1px solid black;
                border-radius: 50px;
                margin-top: 20px;

            }    
            strong {
                font-weight: 500;
            }  
        }
    </style>
    <script>
        // Function to show the popup
        function showPopup(message) {
            alert(message);
        }

        // Extract the message from PHP if set
        window.onload = function() {
            <?php if (isset($message)) { ?>
                showPopup("<?php echo $message; ?>");
            <?php } ?>
        };
    </script>
    </head>
<body>
    <div class="d-flex" id="wrapper">

        <!-- Page content wrapper-->
        <div class="page-content-wrapper">

            <!-- Menu Bar class "navbar-custom" -->
            <nav class="navbar navbar-expand navbar-custom border-bottom">
            <div class="navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mt-2 mt-lg-0">
                <li class="nav-item active">
                    <a class="nav-link" href="#" data-href="<?php echo htmlspecialchars($url_members); ?>" onclick="showExitModal(event, this)">Manage Members</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#" data-href="<?php echo htmlspecialchars($url_attendance); ?>" onclick="showExitModal(event, this)">Attendance Check</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="#" data-href="<?php echo htmlspecialchars($url_report); ?>" onclick="showExitModal(event, this)">Report daily</a>
                </li>
                </ul>
            </div>
            </nav>
            <!-- End Menu Bar -->

            <!-- Bootstrap Modal Menu Bar -->
            <div class="modal fade" id="exitModal" tabindex="-1" aria-labelledby="exitModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exitModalLabel">Unsaved Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You have unsaved changes. Are you sure you want to leave this page?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="confirmExitButton">Unsaved, Leave</button>
                    <button type="button" class="btn btn-primary confirmSaveButton">Save, Leave</button>
                </div>
                </div>
            </div>
            </div>

            <div class="wrapper-page-content"> <!-- wrapper-page-content -->

                <!-- Page content-->
                <div class="container"> 
                    <h1 class="mt-4">Faces detection Result</h1>
                    <?php
                    $table_name = $_SESSION['table_name'];
                    $table_weeks_name = $_SESSION['table_weeks_name'];
                    $subject_id = $_SESSION['subject_id'];

                if (isset($_GET['data'])) {
                        $results = json_decode(urldecode($_GET['data']), true); // ตรวจสอบข้อมูลที่ถูกส่งมาจาก Upload-CheckFaces
                        $uploaded_images = isset($_GET['uploaded_images']) ? json_decode($_GET['uploaded_images'], true) : [];                    // ตรวจสอบการถอดรหัส JSON
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            echo "<p>Error decoding JSON data.</p>";
                            exit();
                        }
                    
                        // รับค่า week_date, on_time_time, late_time, absent_time จาก query string
                        $week_date = isset($_GET['week_date']) ? $_GET['week_date'] : '';
                        $week_number = isset($_GET['week_number']) ? intval($_GET['week_number']) : 0;
                        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                        $subject = isset($_GET['subject']) ? $_GET['subject'] : '';
                        $on_time_time = isset($_GET['on_time_time']) ? $_GET['on_time_time'] : '';
                        $late_time = isset($_GET['late_time']) ? $_GET['late_time'] : '';
                        $absent_time = isset($_GET['absent_time']) ? $_GET['absent_time'] : '';
                        
                        $total_faces = 0;

                        // อาเรย์เก็บรายชื่อนักศึกษาในแต่ละประเภท
                        $faces_on_time = array();
                        $faces_late_time = array();
                        $faces_absent_time = array();

                        $unique_images = array(); // เก็บภาพที่ไม่ซ้ำ
                        $faces_found = array(); // เก็บข้อมูลใบหน้าที่ตรวจจับได้

                        // Trap -> ตรวจสอบว่ารายชื่อนักศึกษาที่นำเข้ามาตรวจสอบ มีรายชื่อใน section หรือไม่?
                        if (!is_array($results) || empty($results)) {
                            // หากรูปบุคคลที่นำเข้ามา ไม่ตรง จะแสดงข้อความ ดังนี้
                            echo "<p>Can't detect students because Image of the face in the uploaded photo. Does not match the name listed in your class.<br>Please check your students to the section.</p>";
                            $redirect_url_managemember = "http://192.168.1.39/myproject/Web_app/section/import-students/manage-members.php?table_name=$table_name&subject_id=$subject_id&academic_semester=$academic_semesterNav";
                            echo "<a href='$redirect_url_managemember'>Click! to manage members</a>";
                            exit();
                        }
                        echo "<div class='result-container'>";

                        // แสดงจำนวนรูปภาพทั้งหมดใน $uploaded_images
                        echo "<strong>Number of Uploaded Images:</strong> " . count($uploaded_images) . "<br>";  

                        // ตรวจสอบว่ามีค่า Upload Time อยู่ในข้อมูลหรือไม่
                        if (isset($results[0]['upload_time'])) {
                            echo "<strong>Upload Time:</strong> " . htmlspecialchars($results[0]['upload_time']) . "<br>";
                        } else {
                            echo "<strong>Upload Time:</strong> Not available<br>";
                        }

                        echo "<br>";

                        // สร้าง Carousel
                        echo "<div id='carouselExampleIndicators' class='carousel slide'>";

                        // สร้าง indicators ของ Carousel
                        $indicators = array();
                        foreach ($uploaded_images as $index => $image_data) {
                            $image_hash = md5_file($image_data['file']); // ใช้ hash ของไฟล์เพื่อป้องกันภาพซ้ำ
                            $indicators[] = $image_data;
                        }

                        echo "<div class='carousel-inner'>";

                        // แสดงรูปภาพทั้งหมดใน Carousel
                        foreach ($indicators as $index => $data) {
                            $active = $index === 0 ? 'active' : '';
                            echo "<div class='carousel-item $active'>";
                            echo "<img src='" . htmlspecialchars($data['image']) . "' class='d-block w-100' alt='Uploaded Image'>";
                            echo "</div>";
                        }

                        echo "</div>";
                        echo "<a class='carousel-control-prev' href='#carouselExampleIndicators' role='button' data-slide='prev'>";
                        echo "<span class='carousel-control-prev-icon' aria-hidden='true'></span>";
                        //echo "<span class='sr-only'>Previous</span>";
                        echo "</a>";
                        echo "<a class='carousel-control-next' href='#carouselExampleIndicators' role='button' data-slide='next'>";
                        echo "<span class='carousel-control-next-icon' aria-hidden='true'></span>";
                        //echo "<span class='sr-only'>Next</span>";
                        echo "</a>";
                        echo "</div>";

                        echo "<br>";
                        // แสดงรายชื่อบุคคลที่ตรวจจับได้ทั้งหมด
                        echo "<h2 class='text-primary'>Summary daily</h2>";
                        echo "<br>";

                        // On Time
                        $total_facesOnTime = 0;
                        echo "<div class='name-container'>";
                        echo "<h5>Present Group:</h5>";
                        echo "<hr>";

                        foreach ($results as $data) {
                            if (!empty($data['faces']) && !empty($data['stdId'])) {
                                foreach ($data['faces'] as $index => $name) {
                                    if (!empty($name)) {
                                        $stdId = htmlspecialchars($data['stdId']);
                                        $check_in_time = htmlspecialchars($data['image_time']);
                                        
                                        // ใช้ strtotime() แปลงเป็น timestamp สำหรับเปรียบเทียบ
                                        if (strtotime($check_in_time) <= strtotime($on_time_time)) {
                                            $name_stdid = "Name: " . $name . " (" . $stdId . ") - " . $check_in_time;
                                            echo "<p><strong>Student Id:</strong> $stdId <br><strong>Name:</strong> $name <br><strong>Attendance time:</strong> $check_in_time</p><hr>";                                        
                                            $faces_on_time[] = $name_stdid;
                                            $total_facesOnTime++;
                                            $total_faces++;
                                        }
                                    }
                                }
                            }
                        }

                        if ($total_facesOnTime == 0) {
                            echo "<p>No faces found in On Time category.</p>";
                        }

                        echo "</div>";

                        // Late Time
                        $total_facesLateTime = 0;
                        echo "<div class='name-container'>";
                        echo "<br>";
                        echo "<h5>Late Group:</h5>";
                        echo "<hr>";

                        foreach ($results as $data) {
                            if (!empty($data['faces']) && !empty($data['stdId'])) {
                                foreach ($data['faces'] as $index => $name) {
                                    if (!empty($name)) {
                                        $stdId = htmlspecialchars($data['stdId']);
                                        $check_in_time = htmlspecialchars($data['image_time']);
                                        
                                        // ใช้ strtotime() แปลงเป็น timestamp สำหรับเปรียบเทียบ
                                        if (strtotime($check_in_time) > strtotime($on_time_time) && strtotime($check_in_time) <= strtotime($late_time)) {
                                            $name_stdid = "Name: " . $name . " (" . $stdId . ") - " . $check_in_time;
                                            echo "<p><strong>Student Id:</strong> $stdId <br><strong>Name:</strong> $name <br><strong>Attendance time:</strong> $check_in_time</p><hr>";                                        
                                            $faces_late_time[] = $name_stdid;
                                            $total_facesLateTime++;
                                            $total_faces++;
                                        }
                                    }
                                }
                            }
                        }

                        if ($total_facesLateTime == 0) {
                            echo "<p>No faces found in Late Time category.</p>";
                        }

                        echo "</div>";

                        // Absent Time
                        $total_facesAbsentTime = 0;
                        echo "<div class='name-container'>";
                        echo "<br>";
                        echo "<h5>Absent Group:</h5>";
                        echo "<hr>";

                        foreach ($results as $data) {
                            if (!empty($data['faces']) && !empty($data['stdId'])) {
                                foreach ($data['faces'] as $index => $name) {
                                    if (!empty($name)) {
                                        $stdId = htmlspecialchars($data['stdId']);
                                        $check_in_time = htmlspecialchars($data['image_time']);
                                        
                                        // ใช้ strtotime() แปลงเป็น timestamp สำหรับเปรียบเทียบ
                                        if (strtotime($check_in_time) > strtotime($late_time) && strtotime($check_in_time) && strtotime($absent_time)) {
                                            $name_stdid = "Name: " . $name . " (" . $stdId . ") - " . $check_in_time;
                                            echo "<p><strong>Student Id:</strong> $stdId <br><strong>Name:</strong> $name <br><strong>Attendance time:</strong> $check_in_time</p><hr>";
                                            $faces_absent_time[] = $name_stdid;
                                            $total_facesAbsentTime++;
                                            $total_faces++;
                                        }
                                    }
                                }
                            }
                        }

                        if ($total_facesAbsentTime == 0) {
                            echo "<p>No faces found in Absent Time category.</p>";
                        }
                        
                        echo "</div>";

                        echo "</div>";

                    } else {
                        echo "<p>Server is  not open!.<br>Please contact admin.</p>";
                    }
                    ?>
                    <br>
                    <div class="save-summary mb-2">
                    <form action="./create-daily-report.php" method="post">
                    <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($table_name); ?>">
                            <input type="hidden" name="table_weeks_name" value="<?php echo htmlspecialchars($table_weeks_name); ?>">
                            <input type="hidden" name="academic_semester" value="<?php echo htmlspecialchars($academic_semesterNav); ?>">
                            <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
                            <input type="hidden" name="academic_year" value="<?php echo htmlspecialchars($academic_year); ?>">
                            <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">

                            <input type="hidden" name="week_date" value="<?php echo htmlspecialchars($week_date); ?>">
                            <input type="hidden" name="week_number" value="<?php echo htmlspecialchars($week_number); ?>">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">                        
                            <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
                            <input type="hidden" name="upload_time" value="<?php echo htmlspecialchars($results[0]['upload_time']); ?>">
                            <input type="hidden" name="total_facesOnTime" value="<?php echo htmlspecialchars($total_facesOnTime); ?>">
                            <input type="hidden" name="faces_on_time" value="<?php echo htmlspecialchars(implode(',', $faces_on_time)); ?>">
                            <input type="hidden" name="total_facesLateTime" value="<?php echo htmlspecialchars($total_facesLateTime); ?>">
                            <input type="hidden" name="faces_late_time" value="<?php echo htmlspecialchars(implode(',', $faces_late_time)); ?>">
                            <input type="hidden" name="total_facesAbsentTime" value="<?php echo htmlspecialchars($total_facesAbsentTime); ?>">
                            <input type="hidden" name="faces_absent_time" value="<?php echo htmlspecialchars(implode(',', $faces_absent_time)); ?>">
                            <button type="submit">Save Attendance report!</button>                    
                        </form>
                    </div>
                </div> <!-- End Page content-->

                <!-- Bootstrap Modal -->
                <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Confirm to Save Results</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to save the results?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary confirmSaveButton">Save, Leave</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="right-group mt-5 p-3 rounded shadow-lg" style="background-color: #f8f9fa;"> 

                    <h5 class="mt-4 mb-3 text-primary text-center">More Detail</h5>

                    <div class="total-profile d-flex align-items-center my-2">
                        <i class="fas fa-check-circle text-success"></i>
                        <span>Faces On Time: </span>
                        <strong class="ms-auto"><?php echo htmlspecialchars($total_facesOnTime); ?></strong>          
                    </div>

                    <div class="total-profile d-flex align-items-center my-2">
                        <i class="fas fa-clock text-warning"></i>
                        <span>Faces Late Time: </span>
                        <strong class="ms-auto"><?php echo htmlspecialchars($total_facesLateTime); ?></strong>
                    </div>

                    <div class="total-profile d-flex align-items-center my-2">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                        <span>Faces Absent Time: </span>
                        <strong class="ms-auto"><?php echo htmlspecialchars($total_facesAbsentTime); ?></strong>
                    </div>

                    <div class="total-profile d-flex align-items-center my-2">
                        <i class="fas fa-user-check text-info"></i>
                        <span>Total Attendance: </span>
                        <strong class="ms-auto"><?php echo htmlspecialchars($total_faces); ?></strong>
                    </div>

                    <!-- แสดงกราฟใน Sidebar -->
                    <!-- <canvas class="mt-4" id="attendanceChart" width="400" height="400"></canvas> -->

                    <div class="canvas-container" style="position: relative;">
                        <canvas class="mt-4" id="attendanceChart" width="400" height="400"></canvas>
                    </div>

                 </div>
                <!-- End Right Sidebar -->

            </div> <!-- End wrapper-Page-content-->

            <!-- Include footer -->
            <?php include('../web_app/section/component/footer_details.php'); ?>

        </div> <!-- End Page content wrapper-->  
    </div>

    <!-- เพิ่ม Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- เพิ่ม Chart.js Plugin Doughnutlabel -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-doughnutlabel@1.0.0"></script>

    <!-- เพิ่ม Chart.js Plugin DataLabels -->
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Bootstrap Bundle with Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form');
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
            const confirmSaveButtons = document.querySelectorAll('.confirmSaveButton');

            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent the default form submission
                confirmModal.show(); // Show the modal
            });

            // เพิ่ม event listener ให้กับทุกปุ่มที่มี class "confirmSaveButton"
            confirmSaveButtons.forEach(button => {
                    button.addEventListener('click', function () {
                    form.submit();
                });
            });
        });

        function showExitModal(event, element) {
            event.preventDefault(); // Prevent the default link behavior
            const url = element.getAttribute('data-href'); // Get the URL from data attribute
            const confirmExitButton = document.getElementById('confirmExitButton');
            
            const exitModal = new bootstrap.Modal(document.getElementById('exitModal'));
            exitModal.show(); // Show the modal

            confirmExitButton.addEventListener('click', function () {
            window.location.href = url; // Redirect to the URL when "Yes, Leave" is clicked
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // ดึงค่าจาก PHP มาใช้ใน JavaScript
            var totalOnTime = <?php echo json_encode($total_facesOnTime); ?>;
            var totalLate = <?php echo json_encode($total_facesLateTime); ?>;
            var totalAbsent = <?php echo json_encode($total_facesAbsentTime); ?>;

            var ctx = document.getElementById('attendanceChart').getContext('2d');

            // ฟังก์ชันกำหนดการตั้งค่าของ label ฟอนต์
            function getLabelFont() {
                if (window.matchMedia("(min-width: 992px)").matches) {
                    return {
                        size: 20,
                        family: 'Arial, sans-serif',
                        weight: 'bold'
                    };
                } else {
                    return {
                        size: 16,
                        family: 'sans-serif',
                        weight: 'normal'
                    };
                }
            }

            // ฟังก์ชันกำหนดการตั้งค่าของ tooltip ฟอนต์
            function getTooltipFont() {
                if (window.matchMedia("(min-width: 992px)").matches) {
                    return {
                        size: 18,
                        family: 'Arial, sans-serif',
                        weight: 'bold'
                    };
                } else {
                    return {
                        size: 14,
                        family: 'sans-serif',
                        weight: 'normal'
                    };
                }
            }

            var attendanceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['On Time', 'Late', 'Absent'],
                    datasets: [{
                        label: 'Attendance Summary',
                        //data: [120, 45, 10],
                        data: [totalOnTime, totalLate, totalAbsent],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 206, 86, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [
                            'rgba(255, 255, 255, 1)',
                            'rgba(255, 255, 255, 1)',
                            'rgba(255, 255, 255, 1)'
                        ],
                        borderWidth: 5
                    }]
                },
                options: {
                    responsive: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuad',
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: getLabelFont() // ใช้ฟอนต์ที่กำหนดตามขนาดหน้าจอ
                            }
                        },
                        tooltip: {
                            callbacks: {
                                labelTextColor: function() {
                                    return 'white'; // กำหนดสีของ label เมื่อ hover
                                }
                            },
                            titleFont: getTooltipFont(), // ใช้ฟอนต์ที่กำหนดตามขนาดหน้าจอสำหรับ tooltip
                            bodyFont: getTooltipFont(), // ใช้ฟอนต์ที่กำหนดตามขนาดหน้าจอสำหรับ tooltip
                        },
                        datalabels: {
                            color: 'white',
                            font: {
                                size: 13,
                                family: 'sans-serif',
                                style: 'normal',
                                weight: 'bold'
                            },
                            formatter: (value, ctx) => {
                                let sum = 0;
                                let dataArr = ctx.chart.data.datasets[0].data;
                                dataArr.map(data => {
                                    sum += data;
                                });
                                let percentage = (value * 100 / sum).toFixed(2) + "%";
                                return percentage;
                            },
                        }
                    },
                },
                plugins: [ChartDataLabels]
            });

            // เพิ่ม event listener เพื่ออัปเดตฟอนต์ label และ tooltip เมื่อขนาดหน้าจอเปลี่ยน
            window.addEventListener('resize', function() {
                // อัปเดตฟอนต์ของ label
                attendanceChart.options.plugins.legend.labels.font = getLabelFont();
                attendanceChart.options.plugins.tooltip.titleFont = getTooltipFont();
                attendanceChart.options.plugins.tooltip.bodyFont = getTooltipFont();
                attendanceChart.update(); // อัปเดตกราฟ
            });
        });
    </script>
</body>
</html>