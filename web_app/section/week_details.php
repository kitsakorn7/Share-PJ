<?php
session_start(); // เริ่มต้น session

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "projecta";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// นำข้อมูลจาก $_SESSION มาใส่ในตัวแปร
$subject_name = $_SESSION['subject_name'];
$subject_id = $_SESSION['subject_id'];
$classroom_id = $_SESSION['classroom_id'];
$theory_hours = $_SESSION['theory_hours'];
$practical_hours = $_SESSION['practical_hours'];
$semester = $_SESSION['semester'];
$academic_year = $_SESSION['academic_year'];
$day_of_week = $_SESSION['day_of_week'];
$start_time = $_SESSION['start_time'];
$end_time = $_SESSION['end_time'];
$section = $_SESSION['section'];

// Get week_number from query string
$week_number = isset($_GET['week_number']) ? intval($_GET['week_number']) : 0;

// Get subject_name from query string
$subject = isset($_GET['subject']) ? $_GET['subject'] : 'ไม่ทราบชื่อวิชา';

// Get id from query string (if available)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get table_name from query string (if available)
$table_name = isset($_GET['table_name']) ? strtolower($_GET['table_name']) : '';

// Get table_weeks_name from query string (if available)
$table_weeks_name = isset($_GET['table_weeks_name']) ? strtolower($_GET['table_weeks_name']) : '';

// Fetch data for the selected weeks โดยค้นหาข้อมูลจาก week_number
$sql = "SELECT week_number, week_date, on_time_time, late_time, absent_time FROM $table_weeks_name WHERE week_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $week_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $week_date = date('d-m-Y', strtotime($row['week_date'])); // จัดรูปแบบวันที่เป็น วัน-เดือน-ปี
    $on_time_time = $row["on_time_time"];
    $late_time = $row["late_time"];
    $absent_time = $row["absent_time"];
} else {
    die("Week not found.");
}

$stmt->close();
$conn->close();

$url_members = './import-students/manage-members.php?table_name=' . urlencode($table_name) . '&subject_id=' . urlencode($subject_id);
$url_attendance = './attendance-check.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name);
$url_report = './report-history/summary_report.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name);
$url_home = './index.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Check Faces</title>
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="../styles.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" /> 
    <link rel="stylesheet" href="navigation.css">
    <link href="./weekdetails_upload.css" rel="stylesheet" />
    <style>
        /* Small devices (Phone 0-576px) */
        @media (max-width: 576px) {
        .nav-item {
            font-size: 13px !important;
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
            /* Header details */
            .container {
                max-width: 415px;
                margin: 50px auto;
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: start;
            }
            /* Content */
            .container-header-weeks {
                max-width: 415px;
                display: flex;
                flex-direction: column;
                margin: 50px auto;
                gap: 25px;
            }
            /* Condition Attendance */
            .container-form {
                width: 100%;
                margin: 0px;
                padding: 30px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }
            .container-form p {
                font-size: 12px;
            }            
            /* Upload images */
            .container-members-fluid {
                width: 100%;
                padding: 30px;
                margin: 0px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;            
            }
            .container-members-fluid p {
                font-size: 13px;
            }
            /* Detection Btn */
            #url_Face_detection {
                width: 160px; /* ขนาดของ Import members Btn */
                padding: 10px 20px;
            }
            /* Reset Btn */
            #resetBtn {
                width: 80px; /* ขนาดของ Import members Btn */
                padding: 10px 20px;
                margin-right: 5px;
            }
            .footer {
                font-size: 12px;
            }
        }

        /*Medium devices (tablets, 576px and up)*/
        @media (min-width: 576px) { 
            /* Header details */
            .container {
                max-width: 700px;
                margin: 50px auto;
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: start;
            }
            /* Content */
            .container-header-weeks {
                max-width: 700px;
                display: flex;
                gap: 15px;
                margin: 50px auto;
            }
            /* Condition Attendance */
            .container-form {
                width: 475px;
                padding: 30px;
                margin: 0px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }
            .container-form p {
                font-size: 14px;
            }            
            /* Upload images */
            .container-members-fluid {
                width: 475px;
                padding: 30px;
                margin: 0px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;            
            }
            .container-members-fluid p {
                font-size: 14px;
            }
            /* Detection Btn */
            #url_Face_detection {
                width: 160px; /* ขนาดของ Import members Btn */
                padding: 10px 20px;
            }
            /* Reset Btn */
            #resetBtn {
                width: 80px; /* ขนาดของ Import members Btn */
                padding: 10px 20px;
                margin-right: 5px;
            }
        }
        
        /*Large devices (desktops, 992px and up)*/
        @media (min-width: 992px) { 
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
            
            /* Header details */
            .container {
                max-width: 1000px;
                margin: 50px auto;
                padding: 20px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: start;
            }
            /* Content */
            .container-header-weeks {
                display: flex;
                max-width: 1000px;
                gap: 15px;
                margin: 50px auto;
            }
            /* Condition Attendance */
            .container-form {
                width: 40%;
                padding: 30px;
                margin: 0px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }
            /* Upload images */
            .container-members-fluid {
                width: 60%;
                padding: 30px;
                margin: 0px;
                background-color: #ffffff;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
                text-align: center;            
            }
            /* Detection Btn */
            #url_Face_detection {
                width: 350px; /* ขนาดของ Import members Btn */
                padding: 10px 20px;
            }
            /* Reset Btn */
            #resetBtn {
                width: 100px; /* ขนาดของ Import members Btn */
                padding: 10px 20px;
                margin-right: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Include Setting navigation -->
        <?php include 'component/setting_nav.php';?>

        <!-- Include navigation -->
        <?php include './component/navigation.php';?>

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
            
            <!-- Page content-->
            <div class="container mt-5">

                <!-- Include header details -->
                <?php include('./component/header_details.php'); ?>   
            </div>

            <div class="container-header-weeks">

                <div class="container-form">
                    <!-- Top Details -->
                    <div class="mt-3">
                        <h4><b>Condition Attendance</b></h4><br>
                        <p>Subject: <?php echo htmlspecialchars($subject); ?></p>
                        <p>Week: <?php echo htmlspecialchars($week_number); ?></p>
                        <p>Date: <?php echo htmlspecialchars($week_date); ?></p><br>
                        <p>On-time: <?php echo htmlspecialchars($on_time_time); ?></p>
                        <p>Late-time: <?php echo htmlspecialchars($late_time); ?></p>
                        <p>Absent-time: <?php echo htmlspecialchars($absent_time); ?></p>
                    </div>
                </div>

                <!-- Upload Form -->
                <div class="container-members-fluid">
                    <button class="btn btn-primary" id="user-tie">
                        <i class="fa-solid fa-cloud-arrow-up"></i> <!-- Icon -->
                    </button>
                    <br>

                    <!-- ข้อความที่ต้องการเปลี่ยน -->
                    <p id="uploadMessage">Please! upload your picture for face detection.</p>

                    <!-- Display selected file names -->
                    <div id="selectedFiles" style="margin: 5px auto;"></div>

                    <div class="button-members-fluid">

                        <!-- Reset button (hidden initially) -->
                        <button class="btn btn-secondary" id="resetBtn" style="display:none;">Reset</button>

                        <!-- Face detection Btn -->
                        <button class="btn btn-primary" id="url_Face_detection" style="display:none;">Face detection</button>

                        <!-- Form -->
                        <form id="importForm" action="../../flask_app/upload-CheckFaces.php" method="post" enctype="multipart/form-data" style="display:none;">
                            <input type="file" name="images[]" id="fileInput" accept="image/*" multiple required>
                            <input type="hidden" name="table_name" value="<?php echo htmlspecialchars($table_name); ?>">
                            <input type="hidden" name="table_weeks_name" value="<?php echo htmlspecialchars($table_weeks_name); ?>">
                            <input type="hidden" name="week_date" value="<?php echo htmlspecialchars($week_date); ?>">
                            <input type="hidden" name="subject" value="<?php echo htmlspecialchars($subject); ?>">
                            <input type="hidden" name="week_number" value="<?php echo htmlspecialchars($week_number); ?>">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">                        
                            <input type="hidden" name="on_time_time" value="<?php echo htmlspecialchars($on_time_time); ?>">
                            <input type="hidden" name="late_time" value="<?php echo htmlspecialchars($late_time); ?>">
                            <input type="hidden" name="absent_time" value="<?php echo htmlspecialchars($absent_time); ?>">
                        </form>

                    </div>
                </div>
            <!-- End Upload Form -->
            </div>

            <script>
                const userTieBtn = document.getElementById('user-tie');
                const fileInput = document.getElementById('fileInput');
                const selectedFilesDiv = document.getElementById('selectedFiles');
                const resetBtn = document.getElementById('resetBtn');
                const uploadMessage = document.getElementById('uploadMessage');
                const faceDetectionBtn = document.getElementById('url_Face_detection');

                // เมื่อกดปุ่ม user-tie ให้คลิกเลือกไฟล์ใน input type="file"
                userTieBtn.addEventListener('click', function() {
                    fileInput.click();
                });

                // แสดงชื่อไฟล์ที่เลือก ซ่อนปุ่ม user-tie และเปลี่ยนข้อความ
                fileInput.addEventListener('change', function() {
                    selectedFilesDiv.innerHTML = ''; // ล้างข้อมูลเก่าก่อน

                    // แสดงชื่อไฟล์ที่เลือกทั้งหมด
                    for (let i = 0; i < fileInput.files.length; i++) {
                        const fileName = document.createElement('p');
                        fileName.textContent = fileInput.files[i].name;
                        selectedFilesDiv.appendChild(fileName);
                    }

                    // ซ่อนปุ่ม user-tie และแสดงปุ่ม reset และปุ่ม face detection
                    userTieBtn.style.display = 'none';
                    resetBtn.style.display = 'inline-block';
                    faceDetectionBtn.style.display = 'inline-block';

                    // เปลี่ยนข้อความเป็น "Selected images"
                    uploadMessage.textContent = 'Selected images';
                });

                // เมื่อกดปุ่ม reset ให้ล้างข้อมูลที่เลือก และกลับมาแสดงปุ่ม user-tie และข้อความเดิม
                resetBtn.addEventListener('click', function() {
                    fileInput.value = ''; // ล้างไฟล์ที่เลือก
                    selectedFilesDiv.innerHTML = ''; // ล้างชื่อไฟล์ที่แสดง
                    userTieBtn.style.display = 'inline-block'; // แสดงปุ่ม user-tie
                    resetBtn.style.display = 'none'; // ซ่อนปุ่ม reset
                    faceDetectionBtn.style.display = 'none'; // ซ่อนปุ่ม face detection

                    // เปลี่ยนข้อความกลับเป็น "Please! upload your picture for face detection."
                    uploadMessage.textContent = 'Please! upload your picture for face detection.';
                });

                // เมื่อกดปุ่ม url_Face_detection ให้ส่งฟอร์ม
                faceDetectionBtn.addEventListener('click', function() {
                    document.getElementById('importForm').submit();
                });
            </script>

            <!-- Include footer -->
            <?php include('./component/footer_details.php'); ?>

        <!-- End Page content-->
        </div> 
    <!-- End Page content wrapper-->
    </div>
<script src="./js/scripts.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var sidebarToggle = document.getElementById('sidebarToggle');
    var body = document.body;
    var wrapper = document.getElementById('wrapper');
    var overlay = document.getElementById('overlay');

    // Toggle sidebar visibility when button is clicked
    sidebarToggle.addEventListener('click', function () {
        wrapper.classList.toggle('toggled');
        overlay.style.display = wrapper.classList.contains('toggled') ? 'block' : 'none';
    });

    // Hide sidebar and overlay when overlay is clicked
    overlay.addEventListener('click', function () {
        body.classList.remove('sb-sidenav-toggled');
        wrapper.classList.remove('toggled');
        overlay.style.display = 'none';
    })
});

</script>
</body>
</html>