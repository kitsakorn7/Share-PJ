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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

       /* Container หลัก */
.table-container {
    margin-top: 20px;
    padding: 20px;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    width: 100%;
    overflow-x: auto; /* ทำให้ตารางสามารถเลื่อนได้ในหน้าจอเล็ก */
}
h1 {
            margin: 0;
            font-size: 2rem;
        }
        
/* หัวตาราง */
.table-title {
    font-size: 24px;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
    margin: 0 auto;
    max-width: 600px; /* จำกัดความกว้างของหัวตาราง */
}

/* ตาราง */
.table {
    width: 100%;
    border-collapse: collapse;
}

.table th, .table td {
    padding: 8px;
    text-align: left;
    white-space: nowrap; /* ป้องกันการตัดคำ */
}


/* ปรับแต่งสำหรับหน้าจอขนาดเล็กมาก */
@media screen and (max-width: 580px) {

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
    }

    .table-title {
        font-size: 18px;
    }

    .table th, .table td {
        font-size: 0.8em;
        padding: 5px;
    }

    .table-title {
        max-width: 100%;
    }

    .form-label {
    font-size: 0.9em;
    }
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
}


.btn-details {
    display: block;
    width: 100%; /* ปรับขนาดความกว้างของปุ่ม */
    margin: 10px 0; /* ระยะห่างระหว่างปุ่ม */
    padding: 15px; /* ระยะห่างภายในปุ่ม */
    font-size: 16px; /* ขนาดตัวอักษรทั่วไป */
    color: #fff; /* สีตัวอักษร */
    text-align: left; /* จัดข้อความในปุ่มให้ชิดซ้าย */
    text-decoration: none; /* ลบเส้นขีดใต้ข้อความ */
    border-radius: 5px; /* มุมปุ่มเป็นมน */
    border: none;
    box-sizing: border-box; /* ให้ขนาดของปุ่มรวมขอบและระยะห่างภายใน */
}



.subject-name {
    font-size: 50px; /* ขนาดตัวอักษรสำหรับชื่อวิชา */
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
    font-size: 1em;
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
}

.subject-namee {
    font-weight: bold;
    font-size: 1.2em;
}

/* เเก้เเล้วววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววววว*/
.course-details-2 {
    font-size: 0.5em;
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

    .list-group-item {
        font-size: 1rem; /* ลดขนาดฟอนต์ในหน้าจอขนาดเล็ก */
    }

    .list-group-item i {
        font-size: 1.5rem; /* ลดขนาดไอคอนในหน้าจอขนาดเล็ก */
    }

    .responsive-div {
        margin-left: 20px; /* ลดระยะห่างให้เล็กลงในหน้าจอมือถือ */
        font-size: 1em; /* ปรับขนาดตัวอักษรให้อยู่ในขนาดที่เหมาะสม */
    }

    .course-details-2 {
    font-size: 0.7em;
}

}

        .filter-container {
            display: flex;
            justify-content: flex-end; /* จัดตำแหน่งให้ช่อง dropdown อยู่ทางขวา */
            padding: 20px;
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

    </style>
</head>
<body>
       <!-- เมนูนนนนนนนนนนนนนนนนนนนนนนนนนนน-->
<div class="d-flex" id="wrapper">
<div class="overlay" id="overlay"></div> <!-- เพิ่ม overlay -->
    <!-- Sidebar-->
    <div class="border-end bg-white" id="sidebar-wrapper">
        <div class="sidebar-heading">
            <?php if ($isLoggedIn && !empty($userImage)): ?>
                <img src="<?php echo $userImage; ?>" alt="User Profile" class="profile-img">
            <?php endif;?>
            <div class="profile-info">
                <strong><?php echo $isLoggedIn ? $userName : 'Project'; ?></strong>
                <?php if ($isLoggedIn): ?>
                    <small><?php echo $userEmail; ?></small>
                <?php endif;?>
            </div>
        </div>
        <br>
        <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action list-group-item-light mb-2" href="../course-app/addtable.php" style="font-size: 1rem; ">
                <i class="fas fa-home fa-lg" style="font-size: 1.5rem; margin-left: 10px;" ></i> HOME
            </a>
            <a class="list-group-item list-group-item-action list-group-item-light mb-2" href="./indext.php" style="font-size: 1rem;">
                <i class="fas fa-calendar fa-lg" style="font-size: 1.5rem; margin-left: 10px;"></i> CALENDAR
            </a>
        </div>
        <br>
        <hr>
        <div class="responsive-div" style="margin-left: 30px;">ENROLLED</div>
            <div class="btn-container-2">
                <?php
// Courses list in Menu
foreach ($courses as $course):
    $color = getColorForCourse($course['subject_id']);
    $subjectName = htmlspecialchars($course['subject_name']);
    $day_of_week = htmlspecialchars($course['day_of_week']);
    // ตัดข้อมูลให้แสดงแค่ 3 ตัวอักษรแรก
    $shortName = mb_substr($day_of_week, 0, 3);
    ?>
                            <!-- Link -->
                            <a href="../web_app/section/import-students/manage-members.php?subject_id=<?php echo htmlspecialchars($course['subject_id']); ?>" class="btn btn-circle">
                            <div class="circle-icon" style="background-color: <?php echo $color; ?>; margin-left: 9px;">
			                        <span class="circle-text"><?php echo $shortName; ?></span>
			                    </div>

			                    <div class="course-info">
			                        <div class="info-row">
			                            <span class="subject-namee"><?php echo $subjectName; ?></span>
			                        </div>
			                        <div class="info-row">
			                            <span class="course-details-2">
			                                (<?php echo htmlspecialchars($course['start_time']) . " - " . htmlspecialchars($course['end_time']); ?>) <?php echo htmlspecialchars($course['day_of_week']); ?>
			                                กลุ่ม: <?php echo htmlspecialchars($course['section']); ?>
			                            </span>
			                        </div>
			                    </div>
			                </a>
			                <?php endforeach;?>
            </div>
            <hr>
            <div class="list-group list-group-flush">
            <!-- Other links -->
            <a href="<?php echo $logoutUrl; ?>" id="logout-button" class="list-group-item list-group-item-action list-group-item-light mb-2" style="font-size: 1rem;">
                <i class="fas fa-sign-out-alt" style="font-size: 1.5rem; margin-left: 5px;"></i>LOG OUT
            </a>
            </div>
    </div>
     <!-- ถึงนี่นนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนนน-->
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
    </div>
</nav>

<!-- Page content -->
         <div class="container mt-5">
            <!-- Topic -->
            <div class="d-flex align-items-center mb-4">
                <h1 class="me-4" >Calendar</h1>
            </div>
    <hr>
    <br>
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
