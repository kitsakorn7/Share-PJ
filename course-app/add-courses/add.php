<?php
session_start(); // เริ่มต้น session ที่ด้านบนของไฟล์
include 'db.php';

// ดึงข้อมูล classroom จากตารางในฐานข้อมูล
$sql_classrooms = "SELECT id, room_number, floor, building FROM classrooms";
$result_classrooms = $conn->query($sql_classrooms);

// ดึงข้อมูล teacher จากตารางในฐานข้อมูล
$sql_teachers = "SELECT id, first_name_eng, last_name_eng, teacher_id FROM teachers";
$result_teachers = $conn->query($sql_teachers);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์ม
    $course_name = $_POST['course_name'];
    $subject_id = $_POST['subject_id'];
    $subject_name = $_POST['subject_name'];
    $theory_hours = $_POST['theory_hours'];
    $practical_hours = $_POST['practical_hours'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    $day_of_week = $_POST['day_of_week'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time = $_POST['end_time'] ?? null;
    $section = $_POST['section'] ?? null;
    $classroom_id = $_POST['classroom'] ?? null;
    $teacher_id = $_POST['teacher'] ?? null;
    $teacher_id2 = $_POST['teacher2'] ?? null;
    $teacher_id3 = $_POST['teacher3'] ?? null;

    // ตรวจสอบว่ามีการกรอกข้อมูลที่ต้องการหรือไม่
    if (empty($course_name) || empty($subject_id) || empty($subject_name) || empty($theory_hours) || empty($practical_hours) || empty($semester) || empty($academic_year)) {
        echo "กรุณากรอกข้อมูลให้ครบทุกช่อง";
        exit;
    }

    // เตรียม SQL statement และ binding parameters
    $sql = "INSERT INTO courses (
        course_name, subject_id, subject_name, theory_hours, practical_hours,
        semester, academic_year, day_of_week, start_time, end_time,
        section, classroom_id, teacher_id, teacher2_id, teacher3_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die('เตรียมคำสั่ง SQL ไม่สำเร็จ: ' . $conn->error);
    }

    // Binding parameters
    $stmt->bind_param(
        "sssiiisssssssss",
        $course_name, $subject_id, $subject_name, $theory_hours,
        $practical_hours, $semester, $academic_year, $day_of_week,
        $start_time, $end_time, $section, $classroom_id, $teacher_id,
        $teacher_id2, $teacher_id3
    );

    if ($stmt->execute()) {
        $_SESSION["success"] = "ข้อมูลถูกเพิ่มสำเร็จแล้ว!";
        header("Location: add.php"); // เปลี่ยนเส้นทางกลับไปที่หน้า add.php
        exit; // หยุดการทำงาน
    } else {
        echo "เกิดข้อผิดพลาดในการเพิ่มข้อมูล: " . $stmt->error;
    }

    // ปิด statement และ connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Coruses</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="./style.css">

    <!-- Menu left Sidebar -->
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: start;
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">

        <!-- Include sidebar -->
        <?php include './sidebar.php';?>

        <!-- Page content wrapper-->
        <div id="page-content-wrapper">

            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">Menu</button>
                </div>
            </nav>
            <!-- End Top navigation-->

            <!-- Page content-->
            <div class="container mt-5">
                <h1 class="mt-3">Manage-Courses</h1>
                <hr>
                <br>
                <form action="add.php" method="POST" id="courseForm">

                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name:<span style="color: red;">*</span></label>
                        <input type="text" id="course_name" name="course_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Subject ID:<span style="color: red;">*</span></label>
                        <input type="text" id="subject_id" name="subject_id" class="form-control" maxlength="7" required oninput="validateSubjectId(this)">
                    <div id="subjectIdError" style="color: red; display: none;">Please enter a complete 7 digit Subject ID.</div>
                    </div>


                    <div class="mb-3">
                        <label for="subject_name" class="form-label">Subject Name:<span style="color: red;">*</span></label>
                        <input type="text" id="subject_name" name="subject_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="theory_hours" class="form-label">Theory Hours:<span style="color: red;">*</span></label>
                        <input type="number" id="theory_hours" name="theory_hours" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="practical_hours" class="form-label">Practical Hours:<span style="color: red;">*</span></label>
                        <input type="number" id="practical_hours" name="practical_hours" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester:<span style="color: red;">*</span></label>
                        <input type="text" id="semester" name="semester" class="form-control" required oninput="validateNumberInput(this)">
                    </div>

                    <div class="mb-3">
                        <label for="academic_year" class="form-label">Academic Year:<span style="color: red;">*</span></label>
                        <input type="text" id="academic_year" name="academic_year" class="form-control" required oninput="validateNumberInput(this)">
                    </div>

                    <div class="mb-3">
                        <label for="day_of_week" class="form-label">Day of Week:<span style="color: red;">*</span></label>
                        <select id="day_of_week" name="day_of_week" class="form-select">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time:<span style="color: red;">*</span></label>
                        <input type="time" id="start_time" name="start_time" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time:<span style="color: red;">*</span></label>
                        <input type="time" id="end_time" name="end_time" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="section" class="form-label">Section:<span style="color: red;">*</span></label>
                        <input type="text" id="section" name="section" class="form-control" required oninput="validateNumberInput(this)">
                    </div>

                    <div class="mb-3">
                        <label for="classroom" class="form-label">Classroom:<span style="color: red;">*</span></label>
                        <select id="classroom" name="classroom" class="form-select">
                            <?php while ($row = $result_classrooms->fetch_assoc()): ?>
                                <option value="<?=$row['id']?>"><?=$row['room_number']?>, Floor: <?=$row['floor']?>, Building: <?=$row['building']?></option>
                            <?php endwhile;?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="teacher" class="form-label">Teacher:<span style="color: red;">*</span></label>
                        <select id="teacher" name="teacher" class="form-select">
                            <?php while ($row = $result_teachers->fetch_assoc()): ?>
                                <option value="<?=$row['first_name_eng'] . ' ' . $row['last_name_eng']?>"><?=$row['first_name_eng']?> <?=$row['last_name_eng']?> (ID: <?=$row['teacher_id']?>)</option>
                            <?php endwhile;?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="teacher2" class="form-label">Teacher 2:</label>
    <select id="teacher2" name="teacher2" class="form-select">
        <option value="">-- Select teacher --</option> <!-- ค่าว่างเป็นตัวเลือกแรก -->
        <?php
$result_teachers->data_seek(0); // รีเซ็ต pointer ของผลลัพธ์ query
while ($row = $result_teachers->fetch_assoc()): ?>
            <option value="<?=$row['first_name_eng'] . ' ' . $row['last_name_eng']?>"><?=$row['first_name_eng']?> <?=$row['last_name_eng']?> (ID: <?=$row['teacher_id']?>)</option>
        <?php endwhile;?>
    </select>
</div>

<div class="mb-3">
    <label for="teacher3" class="form-label">Teacher 3:</label>
    <select id="teacher3" name="teacher3" class="form-select">
        <option value="">-- Select teacher --</option> <!-- ค่าว่างเป็นตัวเลือกแรก -->
        <?php
$result_teachers->data_seek(0); // รีเซ็ต pointer ของผลลัพธ์ query
while ($row = $result_teachers->fetch_assoc()): ?>
            <option value="<?=$row['first_name_eng'] . ' ' . $row['last_name_eng']?>"><?=$row['first_name_eng']?> <?=$row['last_name_eng']?> (ID: <?=$row['teacher_id']?>)</option>
        <?php endwhile;?>
    </select>
</div>


                    <button type="submit" class="btn btn-success">Add Course</button>

                </form>
                <br>
            </div>
        </div>
    </div>
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Result</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                The data has been successfully added.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
<script>
    // เมื่อโหลดหน้า ตรวจสอบว่ามีการตั้งค่า success ใน session หรือไม่
    <?php if (isset($_SESSION["success"])) {?>
        var myModal = new bootstrap.Modal(document.getElementById("successModal"), {});
        myModal.show();
        <?php unset($_SESSION["success"]); // ลบข้อความ success หลังจากแสดง ?>
    <?php }?>
</script>
<script>
function validateSubjectId(input) {
    // กรองเฉพาะตัวเลข
    input.value = input.value.replace(/\D/g, '');

    // ตรวจสอบว่าจำนวนหลักครบ 7 หรือไม่
    if (input.value.length !== 7) {
        document.getElementById('subjectIdError').style.display = 'block';
    } else {
        document.getElementById('subjectIdError').style.display = 'none';
    }
}

function validateNumberInput(input) {
    // กรองเฉพาะตัวเลข
    input.value = input.value.replace(/\D/g, '');
}
</script>

</body>
</html>