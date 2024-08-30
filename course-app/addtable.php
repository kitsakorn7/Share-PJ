<?php
session_start();

// รับข้อมูลผู้ใช้จาก URL ที่ได้จากการ login มาใส่ตัวแปร
$userEmail = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : (isset($_SESSION['userEmail']) ? $_SESSION['userEmail'] : '');
$userName = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : (isset($_SESSION['userName']) ? $_SESSION['userName'] : '');
$userImage = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : (isset($_SESSION['userImage']) ? $_SESSION['userImage'] : '');

// บันทึกข้อมูลลงใน Session
if (!empty($userEmail)) {
    $_SESSION['userEmail'] = $userEmail;
    $_SESSION['userName'] = $userName;
    $_SESSION['userImage'] = $userImage;
}

// ตรวจสอบว่าผู้ใช้มีการล็อกอินหรือไม่
$isLoggedIn = !empty($userEmail);

// Logout Bug!
$logoutUrl = $isLoggedIn ? 'http://localhost:5173/' : '#';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: http://localhost:5173/"); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Timetable</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />

    <!-- Menu left Sidebar --> 
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .table-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 60px;
        }

        h1 {
            margin: 0;
            font-size: 2rem;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .btn-large {
            font-size: 1.25rem; /* เพิ่มขนาดฟอนต์ */
            padding: 0.75rem 1.25rem; /* เพิ่มขนาด Padding */
        }
        /* กำหนดลักษณะของ sidebar-heading */
        .sidebar-heading {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #ddd;
        }

        .profile-img {
            border-radius: 50%;
            width: 60px;
            height: 60px;
            margin-right: 15px;
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-info strong {
            font-size: 1.2rem;
            color: #333;
        }

        .profile-info small {
            font-size: 0.9rem;
            color: #666;
        }

        /* Apply media queries for different screen sizes */
        @media screen and (max-width: 1024px) {
            .sidebar-heading {
                padding: 15px;
            }
            #sidebar-wrapper {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
            position: fixed;
            height: 100%;
            top: 0;
            left: -250px; /* Hide by default */
            transition: left 0.3s ease;
        }
        /* ตัวเเปร overlay เป็นส่วนของตอนเลื่อนข้อมูลจากทางด้านซ้ายที่มีฟังชั่นต่างๆ เช่นการกดตรงเงาเเล้วข้อมูลจะเลื่อนกลับ */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Initially hidden */
            z-index: 998; /* Higher than content */
        }

        #wrapper.toggled #sidebar-wrapper {
            left: 0; /* Show sidebar */
            z-index: 999; /* Higher than overlay */
        }

        #wrapper.toggled .overlay {
            display: block; /* Show overlay */
        }


            .profile-img {
                width: 50px;
                height: 50px;
                margin-right: 10px;
            }

            .profile-info strong {
                font-size: 1.1rem;
            }

            .profile-info small {
                font-size: 0.8rem;
            }

        }

        @media screen and (max-width: 768px) {
            .sidebar-heading {
                padding: 10px;
            }
            #sidebar-wrapper {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
            position: fixed;
            height: 100%;
            top: 0;
            left: -250px; /* Hide by default */
            transition: left 0.3s ease;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Initially hidden */
            z-index: 998; /* Higher than content */
        }

        #wrapper.toggled #sidebar-wrapper {
            left: 0; /* Show sidebar */
            z-index: 999; /* Higher than overlay */
        }

        #wrapper.toggled .overlay {
            display: block; /* Show overlay */
        }

            .profile-img {
                width: 40px;
                height: 40px;
                margin-right: 8px;
            }

            .profile-info strong {
                font-size: 1rem;
            }

            .profile-info small {
                font-size: 0.7rem;
            }

        }

        @media screen and (max-width: 640px) {
            .sidebar-heading {
                padding: 5px;
                flex-direction: column;
                align-items: flex-start;
            }
            #sidebar-wrapper {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
            position: fixed;
            height: 100%;
            top: 0;
            left: -250px; /* Hide by default */
            transition: left 0.3s ease;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none; /* Initially hidden */
            z-index: 998; /* Higher than content */
        }

        #wrapper.toggled #sidebar-wrapper {
            left: 0; /* Show sidebar */
            z-index: 999; /* Higher than overlay */
        }

        #wrapper.toggled .overlay {
            display: block; /* Show overlay */
        }


            .profile-img {
                width: 30px;
                height: 30px;
                margin-right: 5px;
            }

            .profile-info strong {
                font-size: 0.9rem;
            }

            .profile-info small {
                font-size: 0.6rem;
            }

        }


            /* ปรับเปลี่ยนสไตล์ของปุ่มออกจากระบบให้ใหญ่ขึ้น */
            #logout-button {
                display: flex;
                align-items: center;
                font-size: 1.20rem; /* เพิ่มขนาดตัวหนังสือ */
                padding: 0.75rem 1.5rem; /* เพิ่มขนาดของ Padding */
                background-color: #ffffff; /* สีพื้นหลังของปุ่มเป็นสีขาว */
                color: #000000; /* สีตัวหนังสือเป็นสีดำ */
                border: none; /* ไม่มีเส้นขอบ */
                border-radius: 8px; /* ขอบปุ่มโค้งมน */
                text-decoration: none; /* ลบเส้นขีดใต้ลิงก์ */
            }
            #logout-button i {
                margin-right: 0.5rem;
            }
            #logout-button:hover {
                background-color: #f8f9fa; /* สีพื้นหลังเมื่อวางเมาส์เหนือปุ่ม */
                color: #000000; /* สีตัวหนังสือเมื่อวางเมาส์เหนือปุ่ม */
            }
            .text-center h5 {
            margin-bottom: 20px; /* ช่องว่างด้านล่างของข้อความ */
        }
        .list-group-item {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            padding: 10px 15px;
            border: none;
            border-radius: 0;
            background: transparent; /* ลบพื้นหลัง */
            color: #000; /* สีข้อความเป็นสีดำ */
        }

        .list-group-item i {
            margin-right: 10px;
        }

        .list-group-item-action:hover {
            background: rgba(0, 0, 0, 0.1); /* เพิ่มสีพื้นหลังอ่อน ๆ เมื่อ hover */
        }

        /* ปรับสำหรับหน้าจอขนาดเล็ก */
        @media screen and (max-width: 1024px) {
            .sidebar-heading {
                padding: 15px;
            }

            #sidebar-wrapper {
                width: 300px;
            }

            .profile-info {
                font-size: 0.9rem;
            }

            .profile-img {
                width: 50px;
                height: 50px;
                margin-right: 10px;
            }

            .profile-info strong {
                font-size: 1.1rem;
            }

            .profile-info small {
                font-size: 0.8rem;
            }

            .list-group-item {
                font-size: 1.25rem; /* ลดขนาดฟอนต์ในหน้าจอขนาดกลาง */
            }

            .list-group-item i {
                font-size: 1.25rem; /* ลดขนาดไอคอนในหน้าจอขนาดกลาง */
            }
        }

        @media screen and (max-width: 768px) {
            .sidebar-heading {
                padding: 10px;
            }

            #sidebar-wrapper {
                position: fixed;
                left: -250px;
                transition: all 0.3s ease;
                z-index: 1050;
            }

            #wrapper.sidebar-active #sidebar-wrapper {
                left: 0;
            }

            .profile-info {
                font-size: 0.7rem;
            }

            .circle-icon {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .course-info {
                font-size: 0.7rem;
            }

            .profile-img {
                width: 40px;
                height: 40px;
                margin-right: 8px;
            }

            .profile-info strong {
                font-size: 1rem;
            }

            .profile-info small {
                font-size: 0.7rem;
            }

            .list-group-item {
                font-size: 1rem; /* ลดขนาดฟอนต์ในหน้าจอขนาดเล็ก */
            }

            .list-group-item i {
                font-size: 1.25rem; /* ลดขนาดไอคอนในหน้าจอขนาดกลาง */
            }

        }

        @media screen and (max-width: 640px) {
            .sidebar-heading {
                padding: 5px;
                flex-direction: column;
                align-items: flex-start;
            }

            #wrapper {
                display: block;
            }

            .hamburger {
                display: block;
                cursor: pointer;
            }

            .hamburger .line {
                width: 30px;
                height: 3px;
                background-color: #000;
                margin: 5px 0;
            }

            .profile-img {
                width: 30px;
                height: 30px;
                margin-right: 5px;
            }

            .profile-info strong {
                font-size: 0.9rem;
            }

            .profile-info small {
                font-size: 0.6rem;
            }

            .list-group-item {
                font-size: 1rem; /* ลดขนาดฟอนต์ในหน้าจอขนาดเล็ก */
            }

            .list-group-item i {
                font-size: 1.5rem; /* ลดขนาดไอคอนในหน้าจอขนาดเล็ก */
            }
        }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
<div class="overlay" id="overlay"></div> <!-- เพิ่ม overlay -->

    <!-- Left Sidebar -->
    <div class="border-end bg-white" id="sidebar-wrapper">
        <div class="sidebar-heading">
            <!-- Profile image -->
            <?php if ($isLoggedIn && !empty($userImage)): ?>
                <img src="<?php echo $userImage; ?>" alt="User Profile" class="profile-img">
            <?php endif; ?>
            <!-- Profile Name -->
            <div class="profile-info">
                <strong><?php echo $isLoggedIn ? $userName : 'Project'; ?></strong>
                <?php if ($isLoggedIn): ?>
                    <small><?php echo $userEmail; ?></small>
                <?php endif; ?>
            </div>
        </div>

        <br>
        
        <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action list-group-item-light p-4" href="addtable.php" style="font-size: 1.5rem;">
                <i class="fas fa-home fa-lg"></i> หน้าเเรก
            </a>
            <a class="list-group-item list-group-item-action list-group-item-light p-4" href="http://localhost/myproject/calendar.php" style="font-size: 1.5rem;">
                <i class="fas fa-calendar fa-lg"></i> ปฏิทิน
            </a>

            <br>
            <hr>

            <div class="list-group list-group-flush">
            <!-- Other links -->
            <a href="<?php echo $logoutUrl; ?>" id="logout-button" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
            </a>
            </div>
        </div>
    </div>

    <!-- Page content wrapper-->
    <div id="page-content-wrapper">

        <!-- Top navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <button class="btn" id="sidebarToggle" style="border: none; background-color: transparent; padding: 0;">
                        <i class="fas fa-bars" style="font-size: 36px; color: black;"></i>
                    </button>
                    <span style="font-size: 24px; margin-left: 20px; color: black;">Classroom</span>
                </div>
            <!-- Left Bug! -->
            <script src="./js/scripts.js" defer></script>
            </div>
        </nav>

        <!-- Page content -->
        <div class="container mt-5">
            <!-- Topic -->
            <div class="d-flex align-items-center mb-4">
                <h1 class="me-4">Join Classroom</h1>
            </div>
    <hr>
    <br>
    <br>
    <br>
            <div class="table-container">
                <div class="text-center mb-3">
                    <h5>Click "Join class" to get start!</h5>
                </div>
                <!-- Table content with only button -->
                <div class="container mt-5">
                    <div class="d-flex justify-content-center">
                        <form action="tabledetails.php" method="POST">
                            <input type="hidden" name="action" value="create_table">
                            <button type="submit" class="btn btn-primary btn-large">Join Class</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
<br>
<br>
<br>
        <script src="js/scriptss.js"></script>
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
    </div>
</div>
</body>
</html>