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
    <link rel="icon" type="image/x-icon" href="assets/knowledge.png" />

    <!-- Menu left Sidebar -->
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1200px;
            padding: 20px;
        }

        .table-container {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 60px;
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
            width: 45px;
            height: 45px;
            margin-right: 15px;
            object-fit: cover;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
        }

        .profile-info strong {
            font-size: 0.9rem;
            color: #333;
        }

        .profile-info small {
            font-size: 0.6rem;
            color: #666;
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
                width: 30px; /* กำหนดความกว้างของไอคอนให้เท่ากันทุกลิงก์ */
                text-align: center; /* จัดไอคอนให้อยู่กึ่งกลาง */
            }
            #logout-button:hover {
                background-color: #f0f0f0; /* เปลี่ยนสีพื้นหลังเมื่อเมาส์เลื่อนมาที่ปุ่ม */
                transform: scale(1.05); /* ขยายปุ่มเล็กน้อย */
                box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.3); /* เพิ่มเงาให้ปุ่ม โดยยืดระยะทางขวามากขึ้น */
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
            background: transparent; /* Transparent background */
            color: #000; /* Black text */
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }

        .list-group-item i {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 15px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .list-group-item-action:hover {
            background-color: #f0f0f0; /* เปลี่ยนสีพื้นหลังเมื่อเมาส์เลื่อนมาที่ปุ่ม */
            transform: scale(1.20); /* ขยายปุ่มเล็กน้อย */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* เพิ่มเงาให้ปุ่ม */
            border-radius: 6%;
        }

        @media screen and (max-width: 576px) {

            .sidebar-heading {
                display: flex;
                gap: 5px;
                align-items: center;
                padding: 5px;
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

        @media screen and (max-width: 576px) {
            #sidebar-wrapper {
            width: 300px;
            background-color: #f8f9fa;
            border-right: 1px solid #ddd;
            position: fixed;
            height: 100%;
            top: 0;
            left: -250px; /* Hide by default */
            transition: all 0.3s ease;
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
                width: 50px;
                height: 50px;
                margin-right: 5px;
                margin-bottom: 10px;
            }

            .profile-info strong {
                font-size: 0.9rem;
            }

            .profile-info small {
                font-size: 0.6rem;
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
            <?php endif;?>
            <!-- Profile Name -->
            <div class="profile-info">
                <strong><?php echo $isLoggedIn ? $userName : 'Project'; ?></strong>
                <?php if ($isLoggedIn): ?>
                    <small><?php echo $userEmail; ?></small>
                <?php endif;?>
            </div>
        </div>

        <br>

        <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action list-group-item-light mb-2" href="addtable.php" style="font-size: 1rem;">
                <i class="fas fa-home fa-lg" style="font-size: 1.5rem; margin-left: 10px;"></i> HOME
            </a>
            <a class="list-group-item list-group-item-action list-group-item-light mb-2" href="http://localhost/myproject/learn-reactjs-2024/calendar/index.php" style="font-size: 1rem;">
                <i class="fas fa-calendar fa-lg" style="font-size: 1.5rem; margin-left: 10px;"></i> CALENDAR
            </a>

            <br>
                </div>
            <hr>

            <div class="list-group list-group-flush">
            <!-- Other links -->
            <a href="<?php echo $logoutUrl; ?>" id="logout-button" class="list-group-item list-group-item-action list-group-item-light mb-2" style="font-size: 1rem;">
                <i class="fas fa-sign-out-alt" style="font-size: 1.5rem; margin-left: 5px;"></i> LOG OUT
            </a>
            </div>
    </div>

    <!-- Page content wrapper-->
    <div id="page-content-wrapper">
        <!-- Top navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <button class="btn" id="sidebarToggle" style="border: none; background-color: transparent; padding: 0;">
                        <i class="fas fa-bars" style="font-size: 28px; color: black;"></i>
                    </button>
                    <span style="font-size: 1.3rem; margin-left: 20px; color: black;">Classroom</span>
                </div>
            <!-- Left Bug! -->
            </div>
        </nav>

        <!-- Page content -->
        <div class="container mt-5">
            <!-- Topic -->
            <div class="d-flex align-items-center mb-4">
                <h1>JOIN CLASSROOM</h1>
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
                <div class="container">
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
        <script src="js/scripts.js"></script>
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
