<?php
session_start(); // เริ่มต้น session
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// นำข้อมูลจาก $_SESSION มาใส่ในตัวแปร
$id = $_SESSION['id'];
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

$table_name = isset($_GET['table_name']) ? strtolower($_GET['table_name']) : '';
$table_weeks_name = isset($_GET['table_weeks_name']) ? strtolower($_GET['table_weeks_name']) : '';
$academic_semesterNav = isset($_GET['academic_semester']) ? strtolower($_GET['academic_semester']) : '';
$section = isset($_GET['section']) ? strtolower($_GET['section']) : '';

$url_calendar = './calendar/calendar.php?table_name=' . urlencode($table_name) . '&subject_id=' . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
$url_members = '../import-students/manage-members.php?table_name=' . urlencode($table_name) . '&subject_id=' . urlencode($subject_id) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
$url_attendance = '../attendance-check.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
$url_report = '../report-history/summary_report.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
?>

<!-- Render Website -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Student</title>
    <!-- เชื่อมโยงกับ Bootstrap CSS -->
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../css/styles.css" rel="stylesheet" />  <!-- เเก้ path ลำดับให้ถูกต้อง -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
     <!--เอา <link rel="stylesheet" href="../../styles.css"> ออก คือ CSS มันเปลี่ยนเเค่บราวเซอร์โหมดไม่ระบุตัวตนบราวเซอร์ปกติไม่เปลี่ยน--> 
    <link rel="stylesheet" href="../../section/navigation.css">
    <link rel="stylesheet" href="./calendar_responsive.css">
    <link rel="stylesheet" href="styles.css">
    <style> /* มาใส่ในหน้านี้เเทน เนื่องจาก พอมาใส่หน้านี้มันกลับ เปลี่ยนทั้ง บราวเซอร์ปกติ เเละ บราวเซอร์โหมดไม่ระบุตัวตนเลย งงเหมือนกัน */

        #wrapper {
            display: flex;
            width: 100%;
        /* เอา height to full viewport height ออก */
        }

        .nav-item {
            margin-left: 15px; /* ระยะห่างระหว่างแต่ละปุ่ม */
        }
        .page-content-wrapper {
            flex: 1;
        }

        /* การตั้งค่าคอนเทนเนอร์ Form */
        .container-form {
            font-size: 12px;
            max-width: 600px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        
        /* ฟอร์มอัปโหลด */
        form {
            display: flex;
            flex-direction: column;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }
        button[type="submit"] {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        #sidebarToggle {
            background: transparent; /* ทำให้พื้นหลังของปุ่มเป็นใส */
            border: none; /* เอาขอบออกจากปุ่ม */
            padding: 0; /* เอาระยะห่างภายในปุ่มออก */
            cursor: pointer; /* เปลี่ยนเคอร์เซอร์เมื่อวางบนปุ่ม */
        }

        #sidebarToggle i {
            font-size: 24px; /* กำหนดขนาดของไอคอน (ปรับขนาดตามต้องการ) */
            color: #000; /* กำหนดสีของไอคอน (เปลี่ยนตามต้องการ) */
        }

        /* เพิ่ม hover effect ถ้าต้องการ */
        #sidebarToggle:hover {
            background: rgba(0, 0, 0, 0.1); /* เพิ่มพื้นหลังสีอ่อนเมื่อเลื่อนเมาส์มาบนปุ่ม */
        }

        .footer {
            width: 100%;
            text-align: center;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        
    </style>
</head>
<body>
        <div class="d-flex" id="wrapper">
        <!-- Include Setting navigation -->
        <?php include '../component/setting_nav.php';?>

        <!-- Include navigation -->
        <?php include '../component/navigation.php';?>

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
                <?php include '../component/header_details.php';?>
            </div>

            <div class="container-weeks">
                <h1 class="mb-4" >Calendar</h1>
                <hr>
                <div class="calendar mt-5">

                    <header class="calendar-header">
                        <button id="prevMonth" class="nav-button">&#10094;</button>
                        <h1 id="monthYear"></h1>
                        <button id="nextMonth" class="nav-button">&#10095;</button>
                    </header>

                    <div class="calendar-grid">
                        <!-- Days will be added dynamically here -->
                    </div>
                    <script src="script.js"></script>
                </div>
            </div>
            <!-- End Page content-->

            <!-- Include footer -->
            <?php include '../component/footer_details.php';?>

        </div>
        <!-- End Page content wrapper-->

    </div>
    <!-- เชื่อมโยงกับ Bootstrap JS และ jQuery -->
    <script src="../js/scripts.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://apis.google.com/js/api.js"></script>
        <script>
            function handleLogout() {
                gapi.load('auth2', function() {
                    var auth2 = gapi.auth2.getAuthInstance();
                    if (auth2) {
                        auth2.signOut().then(function () {
                            window.location.href = 'http://localhost:5173/'; // เปลี่ยนเป็น URL ที่ต้องการหลังจากออกจากระบบ
                        });
                    }
                });
            }

            // Load the Google API client library and initialize it with your client ID
            function initClient() {
                gapi.load('client:auth2', function() {
                    gapi.client.init({
                        clientId: 'YOUR_CLIENT_ID',
                        scope: 'profile email'
                    }).then(function () {
                        // Add event listener to the logout button
                        document.getElementById('logout-button').addEventListener('click', handleLogout);
                    });
                });
            }

            // Call initClient on load
            window.onload = initClient;

            document.addEventListener('DOMContentLoaded', function () {
                var sidebarToggle = document.getElementById('sidebarToggle');
                var body = document.body;
                var wrapper = document.getElementById('wrapper');
                var overlay = document.getElementById('overlay');

                sidebarToggle.addEventListener('click', function () {
                    // Toggle the sidebar and overlay visibility
                    wrapper.classList.toggle('toggled');
                    overlay.style.display = wrapper.classList.contains('toggled') ? 'block' : 'none';
                });

                overlay.addEventListener('click', function () {
                    // Hide the sidebar and overlay when the overlay is clicked
                    body.classList.remove('sb-sidenav-toggled');
                    wrapper.classList.remove('toggled');
                    overlay.style.display = 'none';
                });
            });
        </script>
</body>
</html>
