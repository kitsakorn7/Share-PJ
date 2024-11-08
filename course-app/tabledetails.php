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

// สีของปุ่ม Section มีดังนี้ โดยสีพวกนี้จะทำการสุ่มตามที่เราเพิ่มในโค้ดนี้
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
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Timetable</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/knowledge.png" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="css/styles.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../web_app/section/navigation.css">
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
            padding: 20px;
        }

       /* Container หลัก */
        .table-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start; /* ปรับให้ตารางเริ่มแสดงจากทางด้านซ้าย ถ้าเป็น center มันอยู่กลางนะเเต่มันเเสดงข้อมูลในตารางไม่ครบ ข้อมูล ID */
            margin-top: 20px;
            padding: 20px;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            overflow-x: auto; /* ทำให้ตารางสามารถเลื่อนได้ในหน้าจอเล็ก */
        }

        /* หัวตาราง */
        .table-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
            align-items: center; 
            text-align: center;
            padding-bottom: 10px;
            width: 100%; /* จำกัดความกว้างของหัวตาราง */
        }

        /* ตาราง */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            font-size: 14px;
            padding: 8px;
            text-align: center;
            white-space: nowrap; /* ป้องกันการตัดคำ */
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
        .option {
            font-size: 1rem;
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

        .No-course-icon {
            font-size: 98px;
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

            .table th, .table td {
                font-size: 0.8em;
                padding: 10px;
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

            .No-course {
                font-size: 14px;
            }
        }

    </style>
</head>
<body>
<div class="d-flex" id="wrapper">
<div class="overlay" id="overlay"></div> <!-- เพิ่ม overlay -->

    <!-- Sidebar -->
    <div class="border-end bg-white" id="sidebar-wrapper">
        <div class="sidebar-heading">
            <?php if ($isLoggedIn && !empty($userImage)): ?>
                <img src="<?php echo $userImage; ?>" alt="User Profile" class="profile-img">
            <?php endif;?>
            <div class="profile-info">
                <strong><?php echo $isLoggedIn ? $userName : 'Testing'; ?></strong>
                <?php if ($isLoggedIn): ?>
                    <small><?php echo $userEmail; ?></small>
                <?php endif;?>
            </div>
        </div>
        <br>
        <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action list-group-item-light mb-2" href="./tabledetails.php" style="font-size: 1rem; ">
                <i class="fas fa-home fa-lg" style="font-size: 1.5rem; margin-left: 10px;" ></i> HOME
            </a>
            <!-- <a class="list-group-item list-group-item-action list-group-item-light mb-2" href="../calendar/indext.php" style="font-size: 1rem;">
                <i class="fas fa-calendar fa-lg" style="font-size: 1.5rem; margin-left: 10px;"></i> CALENDAR
            </a> -->
        </div>
        <hr>
        <div class="responsive-div" style="margin-left: 23px;">ENROLLED</div>
            <div class="btn-container-2" style="display: none;">
                <?php
                // Courses list in Menu
                foreach ($courses as $course):
                    $color = getColorForCourse($course['subject_id']);
                    $subjectName = htmlspecialchars($course['subject_name']);
                    $day_of_week = htmlspecialchars($course['day_of_week']);
                    // ตัดข้อมูลให้แสดงแค่ 3 ตัวอักษรแรก
                    $shortName = mb_substr($day_of_week, 0, 3);
                    ?>
                            <!-- Link to Section of left menu -->
			                <a href="../web_app/section/import-students/manage-members.php?subject_id=<?php echo urlencode($course['subject_id']); ?>&academic_semester=<?php echo urlencode($selectedAcademicSemester); ?>&section=<?php echo urlencode($course['section']); ?>" class="btn btn-circle">
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
			                                Sec: <?php echo htmlspecialchars($course['section']); ?>
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
    <div class="d-flex align-items-center mb-4">
        <h1>TIMETABLE</h1>
    </div>
    <hr>
    
    <!-- Select Semester -->
    <div class="filter-container">
        <?php if (!empty($academicSemesters)): ?> <!-- ตรวจสอบว่ามี academic_semester ในระบบหรือไม่ -->
        <form action="tabledetails.php" method="GET" class="mb-3">
            <div class="form-group">
                <select id="academic_semester" name="academic_semester" class="form-select" onchange="this.form.submit()">
                    <option value="">Select Semester</option>
                    <?php foreach ($academicSemesters as $row): ?>
                        <option value="<?=htmlspecialchars($row['academic_year']) . '-' . htmlspecialchars($row['semester'])?>"
                            <?=isset($_GET['academic_semester']) && $_GET['academic_semester'] == htmlspecialchars($row['academic_year']) . '-' . htmlspecialchars($row['semester']) ? 'selected' : ''?>>
                            <!-- แสดงผล option -->
                            <?=htmlspecialchars($row['semester'])?> - <?=htmlspecialchars($row['academic_year'])?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        <?php else: ?>
            <!-- กรณีที่ไม่มี academic_semester ในระบบ -->
            <option>No semesters available</option>
        <?php endif; ?>
    </div>

    <!-- Div ที่จะแสดงเมื่อไม่มี academic_semester หรือไม่มีตารางสอนในเทอมดังกล่าว -->
    <div class="No-course" id="No-course" style="display: none; width: 100%; text-align: center;">
        <div class="alert alert-warning " style="width: 100%; margin: 0 auto;">
            Congratulations!, this academic year, <b><?php echo htmlspecialchars($userName); ?></b> has no teaching.
        </div>
    </div>

    <!-- ซ่อนหรือแสดงเนื้อหาด้วย JavaScript -->
    <div class="table-container" id="content" style="display: none;">
        <div class="table-title">Teaching schedule</div>
        <?php if (!empty($courses)): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Section</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Classroom</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($courses as $course): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($course['subject_id']); ?></td>
                        <td><?php echo htmlspecialchars($course['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($course['section']); ?></td>
                        <td><?php echo htmlspecialchars($course['day_of_week']); ?></td>
                        <td><?php echo htmlspecialchars($course['start_time']) . ' - ' . htmlspecialchars($course['end_time']); ?></td>
                        <td>
                            <?php echo htmlspecialchars($course['room_number']) . ' (' . htmlspecialchars($course['building']) . ', ' . htmlspecialchars($course['floor']) . ')'; ?>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
        <?php endif;?>
    </div>
    <br>
    <br>

    <!-- ซ่อนหรือแสดงปุ่มด้วย JavaScript -->
    <div class="btn-container" id="btnContent" style="display: none;">
        <h2>Classroom</h2>
        <hr>
        <?php foreach ($courses as $course): ?>
            <?php $color = getColorForCourse($course['subject_id']); ?>

            <?php ?>
            
            <!-- Link to sections ส่งค่าตัวแปร subject_id และ academic_semester -->
            <a href="../web_app/section/import-students/manage-members.php?subject_id=<?php echo urlencode($course['subject_id']); ?>&academic_semester=<?php echo urlencode($selectedAcademicSemester); ?>&section=<?php echo urlencode($course['section']); ?>" class="btn btn-details" style="background-color: <?php echo $color; ?>">
                <span class="subject-name"><?php echo htmlspecialchars($course['subject_name']); ?></span><br>
                <span class="course-details">
                    (<?php echo htmlspecialchars($course['start_time']) . " - " . htmlspecialchars($course['end_time']); ?>) <?php echo htmlspecialchars($course['day_of_week']); ?><br>
                    Section: <?php echo htmlspecialchars($course['section']); ?>
                </span>
            </a>
        <?php endforeach;?>
        
    </div>
</div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script>
    // JavaScript สำหรับการแสดงผล div เมื่อไม่มี academic_semester
    window.onload = function() {
        var hasAcademicSemester = <?php echo !empty($academicSemesters) ? 'true' : 'false'; ?>;
        var noCourseDiv = document.getElementById('No-course');

        // ตรวจสอบว่ามี academic_semester หรือไม่ ถ้าไม่มีให้แสดง div
        if (!hasAcademicSemester) {
            noCourseDiv.style.display = 'block'; // แสดง div เมื่อไม่มี academic_semester
        }
    };
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectElement = document.getElementById('academic_semester');
        const contentElement = document.getElementById('content');
        const btnContainerElement = document.getElementById('btnContent');
        const btnContainer2Element = document.querySelector('.btn-container-2');  // เพิ่มอ้างอิงถึง btn-container-2
        
        // แสดงหรือซ่อนเนื้อหาเมื่อเลือก academic_semester
        if (selectElement.value !== "") {
            contentElement.style.display = 'block';
            btnContainerElement.style.display = 'block';
            btnContainer2Element.style.display = 'block';  // แสดง btn-container-2

        } else {
            contentElement.style.display = 'none';
            btnContainerElement.style.display = 'none';
            btnContainer2Element.style.display = 'none';  // ซ่อน btn-container-2
        }

        selectElement.addEventListener('change', function() {
            if (this.value === "") {
                contentElement.style.display = 'none';
                btnContainerElement.style.display = 'none';
                btnContainer2Element.style.display = 'none';  // ซ่อน btn-container-2
            } else {
                contentElement.style.display = 'block';
                btnContainerElement.style.display = 'block';
                btnContainer2Element.style.display = 'block';  // แสดง btn-container-2
            }
        });
    });

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
