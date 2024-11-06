<?php
include 'config.php';
session_start();

// รับข้อมูลผู้ใช้จาก URL หรือ Session
$userEmail = isset($_GET['user']) ? htmlspecialchars($_GET['user']) : (isset($_SESSION['userEmail']) ? $_SESSION['userEmail'] : '');
$userName = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : (isset($_SESSION['userName']) ? $_SESSION['userName'] : '');
$userImage = isset($_GET['image']) ? htmlspecialchars($_GET['image']) : (isset($_SESSION['userImage']) ? $_SESSION['userImage'] : '');

// บันทึกข้อมูลลงใน Session หากมีข้อมูลใหม่
if (!empty($userEmail)) {
    $_SESSION['userEmail'] = $userEmail;
    $_SESSION['userName'] = $userName;
    $_SESSION['userImage'] = $userImage;
}

// ตรวจสอบว่าผู้ใช้มีการล็อกอินหรือไม่
$isLoggedIn = !empty($userEmail);

// สร้าง URL สำหรับการออกจากระบบ
$logoutUrl = $isLoggedIn ? 'http://localhost:5173/?logout=true' : '#';

// การออกจากระบบ
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: http://localhost:5173/"); // Redirect to login page
    exit();
}

// รับข้อมูลชื่อผู้ใช้จากเซสชัน
$userName = isset($_SESSION['userName']) ? htmlspecialchars($_SESSION['userName']) : '';

// ตรวจสอบว่าผู้ใช้มีการล็อกอินหรือไม่
if (empty($userName)) {
    die("Please log in first.");
}

// รับค่า academic_year และ semester จากฟอร์ม
$selectedAcademicSemester = isset($_GET['academic_semester']) ? htmlspecialchars($_GET['academic_semester']) : '';

// แยกค่า academic_year และ semester
$selectedAcademicYear = '';
$selectedSemester = '';
if (!empty($selectedAcademicSemester)) {
    list($selectedAcademicYear, $selectedSemester) = explode('-', $selectedAcademicSemester);
}

// เตรียม SQL Query สำหรับค้นหาข้อมูล
$sql = "
    SELECT c.*,
           cl.room_number, cl.floor, cl.building
    FROM courses c
    LEFT JOIN classrooms cl ON c.classroom_id = cl.id
    WHERE (c.teacher_id = :userName
           OR c.teacher2_id = :userName
           OR c.teacher3_id = :userName)
";

// เพิ่มเงื่อนไขการกรองตาม academic_year และ semester
if (!empty($selectedAcademicYear) || !empty($selectedSemester)) {
    $sql .= " AND (";
    if (!empty($selectedAcademicYear)) {
        $sql .= " c.academic_year = :academicYear";
        if (!empty($selectedSemester)) {
            $sql .= " AND";
        }
    }
    if (!empty($selectedSemester)) {
        $sql .= " c.semester = :semester";
    }
    $sql .= ")";
}

try {
    // ค้นหาข้อมูลในตาราง courses ตาม teacher_id ที่ตรงกับชื่อผู้ใช้
    $stmt = $conn->prepare($sql);

    // Binding parameters
    $stmt->bindParam(':userName', $userName);
    if (!empty($selectedAcademicYear)) {
        $stmt->bindParam(':academicYear', $selectedAcademicYear);
    }
    if (!empty($selectedSemester)) {
        $stmt->bindParam(':semester', $selectedSemester);
    }

    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

try {
    // Query สำหรับดึงข้อมูล academic_year และ semester โดยกรองตาม teacher_id ของผู้ใช้
    $stmt = $conn->prepare("
        SELECT DISTINCT academic_year, semester
        FROM courses
        WHERE teacher_id = :userName
        OR teacher2_id = :userName
        OR teacher3_id = :userName
    ");
    $stmt->bindParam(':userName', $userName);
    $stmt->execute();
    $academicSemesters = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// สีของปุ่ม section มีดังนี้ โดยสีพวกนี้จะทำการสุ่มตามที่เราเพิ่มในโค้ดนี้
$colors = ['#4a235a', '#943126', '#196f3d', '#21618c', '#283747', '#af601a'];

// Function to get color for a course ID
function getColorForCourse($courseId)
{
    global $colors;
    if (!isset($_SESSION['course_colors'])) {
        $_SESSION['course_colors'] = [];
    }
    if (!isset($_SESSION['course_colors'][$courseId])) {
        // Generate a random color index
        $_SESSION['course_colors'][$courseId] = rand(2, count($colors) - 1);
    }
    return $colors[$_SESSION['course_colors'][$courseId]];
}

// สุ่มสีสำหรับแต่ละวิชา
foreach ($courses as $index => $course) {
    $colorIndex = $index % count($colors);
    $course['button_color'] = $colors[$colorIndex];
    $courses[$index] = $course; // อัพเดตข้อมูล
}

// Assuming you have $courses array populated
function dayOfWeekToNumber($day)
{
    $days = ['Sunday' => 0, 'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6];
    return $days[$day];
}

usort($courses, function ($a, $b) {
    $dayA = dayOfWeekToNumber($a['day_of_week']);
    $dayB = dayOfWeekToNumber($b['day_of_week']);

    if ($dayA == $dayB) {
        return strtotime($a['start_time']) - strtotime($b['start_time']);
    }
    return $dayA - $dayB;
});

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Calendar with Current Date</title>
<link rel="stylesheet" href="styles.css">
<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="assets/knowledge.png" />

<!-- Menu left Sidebar -->
<link href="css/styles.css" rel="stylesheet" />
<link href="./navigation.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>

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

    body {
    font-family: Arial, sans-serif;
    background-color: #f4f7f6;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 1000;
    padding: 20px;
}

/* ตาราง */
.table {
    width: 100%;
    border-collapse: collapse;
}

.footer {
    width: 100%;
    text-align: center;
    padding: 30px;
    background-color: #ffffff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
}       

.form-group {
    margin-bottom: 0;
    display: flex;
    justify-content: center;
    align-items: center;
}

.form-label{
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 0;
}

.form-select {
    width: 100%;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}
tr:hover {
    background-color: #ddd; /* Changes background on hover */
}
.no-courses {
    font-size: 1.2em;
    color: #555;
    text-align: center;
    margin-top: 20px;
}
.logout-link {
    display: inline-block;
    margin: 10px 0;
    padding: 10px 20px;
    background-color: #007BFF;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}
.logout-link:hover {
    background-color: #0056b3;
}
 .btn-large {
    font-size: 1.25rem; /* เพิ่มขนาดฟอนต์ */
    padding: 0.75rem 1.25rem; /* เพิ่มขนาด Padding */
}
.btn-info {
    display: block;
    width: 100%;
    max-width: 1500px; /* ขนาดความกว้างสูงสุดของปุ่ม */
    padding: 20px;
    font-size: 18px;
    margin-bottom: 10px; /* ระยะห่างระหว่างปุ่ม */
    text-align: center;
}

.btn-container {
    display: flex;
    flex-direction: column; /* เรียงปุ่มในแนวตั้ง */
    align-items: center;
    padding: 5px;
}

.btn-details {
    display: block;
    width: 100%; /* ปรับขนาดความกว้างของปุ่ม */
    margin: 10px 0; /* ระยะห่างระหว่างปุ่ม */
    padding: 15px; /* ระยะห่างภายในปุ่ม */

    font-size: 0.8rem; /* ขนาดตัวอักษรทั่วไป */
    font-weight: 100;
    color: #fff; /* สีตัวอักษร */
    text-align: left; /* จัดข้อความในปุ่มให้ชิดซ้าย */
    text-decoration: none; /* ลบเส้นขีดใต้ข้อความ */
    border-radius: 5px; /* มุมปุ่มเป็นมน */
    border: none;
    box-sizing: border-box; /* ให้ขนาดของปุ่มรวมขอบและระยะห่างภายใน */
}

.subject-name {
    font-size: 28px; /* ขนาดตัวอักษรสำหรับชื่อวิชา */
    font-weight: bold; /* ทำให้ชื่อวิชาหนาขึ้น */
}

.btn-container-2 {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.btn-circle {
    display: flex;
    align-items: center;
    padding: 10px;
    text-decoration: none;
    color: black;
    transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
    border-radius: 8px; /* เพิ่มมุมมนให้ปุ่ม */
}

.btn-circle:hover {
    background-color: #f0f0f0; /* เปลี่ยนสีพื้นหลังเมื่อเมาส์เลื่อนมาที่ปุ่ม */
    transform: scale(1.20); /* ขยายปุ่มเล็กน้อย */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* เพิ่มเงาให้ปุ่ม */
}

.circle-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 15px;
    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    color: white; /* สีข้อความในวงกลม */
    font-weight: bold;
    font-size: 0.8rem;
    text-align: center;
    transition: background-color 0.3s, transform 0.3s;
}

.circle-icon:hover {
    transform: scale(1.1); /* ขยายวงกลมเล็กน้อยเมื่อเมาส์เลื่อนมาที่มัน */
}

.circle-text {
    display: block;
    overflow: hidden;
    text-overflow: ellipsis;
}

.course-info {
    display: flex;
    flex-direction: column;
}

.info-row {
    display: flex;
    flex-direction: row;
    justify-content: start;
    align-items: start;
    text-align: start;
}

.subject-namee {
    font-weight: 550;
    font-size: 14px;
}

/* เเก้เเล้วววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววว*/
.course-details-2 {
    font-size: 0.65em;
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

.responsive-div {
    margin-left: 20px;
    font-size: 1.2em; /* ขนาดตัวอักษร */
}

.filter-container {
    display: flex;
    justify-content: flex-end; /* จัดตำแหน่งให้ช่อง dropdown อยู่ทางขวา */
    align-items: center; /* จัดตำแหน่งให้อยู่ในแนวเดียวกัน */
    margin-bottom: 20px;
}
.form-group {
    display: flex;
    align-items: center; /* จัดตำแหน่งให้อยู่ในแนวเดียวกัน */
    margin-left: 20px; /* ระยะห่างระหว่างข้อความป้ายกับช่อง dropdown */
}
.form-label {
    margin-right: 10px; /* ระยะห่างระหว่างข้อความป้ายกับช่อง dropdown */
    color: red;
}
.form-select {
    max-width: 200px; /* ขนาดช่อง dropdown */
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
    transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
}
#logout-button i {
    width: 30px; /* กำหนดความกว้างของไอคอนให้เท่ากันทุกลิงก์ */
    text-align: center; /* จัดไอคอนให้อยู่กึ่งกลาง */
}
#logout-button:hover {
    background-color: #f0f0f0; /* เปลี่ยนสีพื้นหลังเมื่อเมาส์เลื่อนมาที่ปุ่ม */
    transform: scale(1.20); /* ขยายปุ่มเล็กน้อย */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* เพิ่มเงาให้ปุ่ม */
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
    margin-right: 10px;
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
        padding: 10px;
    }
    #sidebar-wrapper {
    width: 300px;
    background-color: #f8f9fa;
    border-right: 1px solid #ddd;
    position: fixed;
    height: 100%;
    top: 0;
    left: -250px; /* Hide by default */
    transition: all 0.3s ease;
    overflow-y: auto; /* เพิ่มคุณสมบัติ overflow */
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
    transition: left 0.3s ease;
}

#wrapper.toggled .overlay {
    display: block; /* Show overlay */
}

#wrapper.sb-sidenav-toggled #sidebar-wrapper {
        left: 0;
        z-index: 999; /* Higher than overlay */
        transition: left 0.3s ease;
    }

    #wrapper.sb-sidenav-toggled .overlay {
        display: block;
        transition: left 0.3s ease;
    }


    .table-container {
        padding: 10px;
        max-width: 100%;
    }

    .table-title {
        font-size: 18px;
    }

    .subject-name {
        font-size: 24px; /* ขนาดตัวอักษรสำหรับชื่อวิชา */
        font-weight: bold; /* ทำให้ชื่อวิชาหนาขึ้น */
    }

    .course-details {
        font-size: 12px;
    }

    .table-title {
        max-width: 100%;
    }

    .form-label {
    font-size: 0.9em;
    }

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
        width: 50px;
        height: 50px;
        margin-right: 5px;
        margin-bottom: 10px;
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
        font-size: 1.5rem; /* ลดขนาดไอคอนในหน้าจอขนาดเล็ก */
    }

    .responsive-div {
        margin-left: 20px; /* ลดระยะห่างให้เล็กลงในหน้าจอมือถือ */
        font-size: 1rem; /* ปรับขนาดตัวอักษรให้อยู่ในขนาดที่เหมาะสม */
        margin-bottom: 10px;
    }

    .course-details-2 {
    font-size: 0.7em;
}

}

</style>
</head>
<body>
    <div class="d-flex" id="wrapper">
     <!-- Include navigation -->
    <?php include './navigation.php';?>

    <!-- Page content -->
    <div class="container mt-5">
    <!-- Topic -->
        <div class="d-flex align-items-center mb-4">
            <h1 class="me-4" >Calendar</h1>
        </div>
    <hr>
    <br>   
    <div class="calendar">

        <header class="calendar-header">
            <button id="prevMonth" class="nav-button">&#10094;</button>
            <h1 id="monthYear"></h1>
            <button id="nextMonth" class="nav-button">&#10095;</button>
        </header>

        <div class="calendar-grid">
            <!-- Days will be added dynamically here -->
        </div>

    </div>
    <script src="script.js"></script>
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
