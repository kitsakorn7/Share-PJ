<?php
session_start(); // เริ่มต้น session

$table_name = $_SESSION['table_name'];
$table_weeks_name = $_SESSION['table_weeks_name'];
$subject_id = $_SESSION['subject_id'];

//Create var Link
$url_members = '../web_app/section/import-students/manage-members.php?table_name=' . urlencode($table_name) . '&subject_id=' . urlencode($subject_id);
$url_attendance = '../web_app/section/attendance-check.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name);
$url_report = '../web_app/section/report-history/summary_report.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
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
                margin: 50px auto;
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
            /* Right Sidebar */
            .right-group {
                max-width: 475px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: center;
                margin: 50px auto;
            }
            .total-profile {
                display: flex;
                justify-content: center;
                align-items: center;

                width: 90%;
                height: 70px;
                border: 1px solid black;
                border-radius: 20px;
                margin-top: 20px;

            }    
            strong {
                font-weight: 500;
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
                /*margin: 50px auto;*/
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;
            }
            /* Right Sidebar */
            .right-group {
                max-width: 700px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;

                display: flex;
                flex-direction: column;
                align-items: center;
                margin: 50px auto;
            }
            .total-profile {
                display: flex;
                justify-content: center;
                align-items: center;

                width: 90%;
                height: 70px;
                border: 1px solid black;
                border-radius: 20px;
                margin-top: 20px;

            }    
            strong {
                font-weight: 500;
            }    
        }

        /*Large devices (desktops, 992px and up)*/
        @media (min-width: 992px) { 
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
            .save-summary {
                /*border: 1px solid black;*/
                padding-top: 20px;
                margin: 0px 50px;
            }
            button[type="submit"] {
                background-color: #007bff;
                width: 100%;
                color: #ffffff;
                border: none;
                padding: 10px 20px;
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
                height: 100vh;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
                border-radius: 20px;
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
                            <a class="nav-link" href="<?php echo htmlspecialchars($url_members); ?>">Manage Members</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo htmlspecialchars($url_attendance); ?>">Attendance Check</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="<?php echo htmlspecialchars($url_report); ?>">Report daily</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <!-- End Menu Bar -->

            <div class="wrapper-page-content"> <!-- wrapper-page-content -->

                <div class="container mt-5"> <!-- Page content-->
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
                            $redirect_url_managemember = "http://192.168.1.39/myproject/Web_app/section/import-students/manage-members.php?table_name=$table_name&subject_id=$subject_id";
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
                        echo "<h2>Summary daily</h2>";
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

                        echo "<br>";
                        echo "<br>";

                        // Summary Students
                        echo "<div class='name-container'>";
                        echo "<div class='totalBar-container'>";

                        echo "</div>";
                        echo "</div>";
                        // End Summary Students

                        echo "</div>";

                    } else {
                        echo "<p>Server is  not open!.<br>Please contact admin.</p>";
                    }
                    ?>
                    <div class="save-summary mb-2">
                        <form action="./create-daily-report.php" method="post" onsubmit="return confirm('Confirm to Save results?');">
                            <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($table_name); ?>">
                            <input type="hidden" name="table_weeks_name" value="<?php echo htmlspecialchars($table_weeks_name); ?>">
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

                <div class="right-group mt-5"> <!-- Right Sidebar-->

                    <div class="total-profile">
                        Total Faces On the Time : <?php echo htmlspecialchars($total_facesOnTime); ?>            
                    </div>

                    <div class="total-profile">
                        Total Faces Late Time : <?php echo htmlspecialchars($total_facesLateTime); ?>
                    </div>

                    <div class="total-profile">
                        Total Faces AbsentTime : <?php echo htmlspecialchars($total_facesAbsentTime); ?>
                    </div>
                    <div class="total-profile">
                        Total Faces Attendance : <?php echo htmlspecialchars($total_faces); ?>
                    </div>
                </div>
            </div> <!-- End wrapper-Page-content-->
        </div> <!-- End Page content wrapper-->  
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>