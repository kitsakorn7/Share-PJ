<?php
session_start();
include('db.php');

// ดึงข้อมูลห้องเรียนและครู
$classrooms_sql = "SELECT * FROM classrooms";
$classrooms_result = $conn->query($classrooms_sql);

$teachers_sql = "SELECT id, first_name_eng, last_name_eng, teacher_id FROM teachers";
$teachers_result = $conn->query($teachers_sql);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM courses WHERE id=$id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No course found";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $course_name = $_POST['course_name'];
    $subject_id = $_POST['subject_id'];
    $subject_name = $_POST['subject_name'];
    $theory_hours = $_POST['theory_hours'];
    $practical_hours = $_POST['practical_hours'];
    $semester = $_POST['semester'];
    $academic_year = $_POST['academic_year'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $section = $_POST['section'];
    $classroom_id = $_POST['classroom_id'];
    $teacher_id = $_POST['teacher'];
    $teacher_id2 = $_POST['teacher2'];
    $teacher_id3 = $_POST['teacher3'];

    $sql = "UPDATE courses SET 
        course_name='$course_name', 
        subject_id='$subject_id', 
        subject_name='$subject_name', 
        theory_hours='$theory_hours', 
        practical_hours='$practical_hours', 
        semester='$semester', 
        academic_year='$academic_year', 
        day_of_week='$day_of_week', 
        start_time='$start_time', 
        end_time='$end_time', 
        section='$section',
        classroom_id='$classroom_id',
        teacher_id='$teacher_id',
        teacher2_id='$teacher_id2',
        teacher3_id='$teacher_id3'
        WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Data update successful.";
        header('Location: index.php');
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}


$conn->close();
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
    <h1 class="mb-4">Edit Course</h1>
    <form action="edit.php" method="POST" id="courseForm">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name:<span style="color: red;">*</span></label>
            <input type="text" id="course_name" name="course_name" class="form-control" value="<?php echo htmlspecialchars($row['course_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="subject_id" class="form-label">Subject ID:<span style="color: red;">*</span></label>
            <input type="text" id="subject_id" name="subject_id" class="form-control" value="<?php echo htmlspecialchars($row['subject_id']); ?>" maxlength="7" required oninput="validateSubjectId(this)" required>
            <div id="subjectIdError" style="color: red; display: none;">Please enter a complete 7 digit Subject ID.</div>
        </div>
        <div class="mb-3">
            <label for="subject_name" class="form-label">Subject Name:<span style="color: red;">*</span></label>
            <input type="text" id="subject_name" name="subject_name" class="form-control" value="<?php echo htmlspecialchars($row['subject_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="theory_hours" class="form-label">Theory Hours:<span style="color: red;">*</span></label>
            <input type="number" id="theory_hours" name="theory_hours" class="form-control" value="<?php echo htmlspecialchars($row['theory_hours']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="practical_hours" class="form-label">Practical Hours:<span style="color: red;">*</span></label>
            <input type="number" id="practical_hours" name="practical_hours" class="form-control" value="<?php echo htmlspecialchars($row['practical_hours']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="semester" class="form-label">Semester:<span style="color: red;">*</span></label>
            <input type="text" id="semester" name="semester" class="form-control" value="<?php echo htmlspecialchars($row['semester']); ?>" required oninput="validateNumberInput(this)">
        </div>
        <div class="mb-3">
            <label for="academic_year" class="form-label">Academic Year:<span style="color: red;">*</span></label>
            <input type="text" id="academic_year" name="academic_year" class="form-control" value="<?php echo htmlspecialchars($row['academic_year']); ?>" required oninput="validateNumberInput(this)">
        </div>
        <div class="mb-3">
            <label for="day_of_week" class="form-label">Day of Week:<span style="color: red;">*</span></label>
            <select id="day_of_week" name="day_of_week" class="form-select">
                <option value="Monday" <?php if($row['day_of_week'] == 'Monday') echo 'selected'; ?>>Monday</option>
                <option value="Tuesday" <?php if($row['day_of_week'] == 'Tuesday') echo 'selected'; ?>>Tuesday</option>
                <option value="Wednesday" <?php if($row['day_of_week'] == 'Wednesday') echo 'selected'; ?>>Wednesday</option>
                <option value="Thursday" <?php if($row['day_of_week'] == 'Thursday') echo 'selected'; ?>>Thursday</option>
                <option value="Friday" <?php if($row['day_of_week'] == 'Friday') echo 'selected'; ?>>Friday</option>
                <option value="Saturday" <?php if($row['day_of_week'] == 'Saturday') echo 'selected'; ?>>Saturday</option>
                <option value="Sunday" <?php if($row['day_of_week'] == 'Sunday') echo 'selected'; ?>>Sunday</option>
            </select>
        </div>  
        <div class="mb-3">
            <label for="start_time" class="form-label">Start Time:<span style="color: red;">*</span></label>
            <input type="time" id="start_time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($row['start_time']); ?>">
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">End Time:<span style="color: red;">*</span></label>
            <input type="time" id="end_time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($row['end_time']); ?>">
        </div>
        <div class="mb-3">
            <label for="section" class="form-label">Section:<span style="color: red;">*</span></label>
            <input type="text" id="section" name="section" class="form-control" value="<?php echo htmlspecialchars($row['section']); ?>" required oninput="validateNumberInput(this)">
        </div>

        <!-- Classroom Selection -->
        <div class="mb-3">
            <label for="classroom_id" class="form-label">Classroom:<span style="color: red;">*</span></label>
            <select id="classroom_id" name="classroom_id" class="form-select" required>
                <option value="">Select Classroom</option>
                <?php while($classroom = $classrooms_result->fetch_assoc()): ?>
                    <option value="<?php echo $classroom['id']; ?>" <?php if($row['classroom_id'] == $classroom['id']) echo 'selected'; ?>>
                        <?php echo $classroom['room_number'] . ", Floor: " . $classroom['floor'] . ", Building: " . $classroom['building']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

          <!-- Teacher Selection -->
          <div class="mb-3">
            <label for="teacher" class="form-label">Teacher:<span style="color: red;">*</span></label>
            <select id="teacher" name="teacher" class="form-select" required>
                <?php while($teacher = $teachers_result->fetch_assoc()): ?>
                    <option value="<?php echo $teacher['first_name_eng'] . ' ' . $teacher['last_name_eng']; ?>" <?php if($row['teacher_id'] == $teacher['first_name_eng'] . ' ' . $teacher['last_name_eng']) echo 'selected'; ?>>
                        <?php echo $teacher['first_name_eng'] . " " . $teacher['last_name_eng']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <?php 
        // Reset result set pointer to the beginning
        $teachers_result->data_seek(0); 
        ?>

<div class="mb-3">
    <label for="teacher2" class="form-label">Teacher:</label>
    <select id="teacher2" name="teacher2" class="form-select">
        <option value="">-- Select Teacher --</option>
        <?php while($teacher = $teachers_result->fetch_assoc()): ?>
            <option value="<?php echo $teacher['first_name_eng'] . ' ' . $teacher['last_name_eng']; ?>" <?php if($row['teacher2_id'] == $teacher['first_name_eng'] . ' ' . $teacher['last_name_eng']) echo 'selected'; ?>>
                <?php echo $teacher['first_name_eng'] . " " . $teacher['last_name_eng']; ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

<?php 
// Reset result set pointer to the beginning
$teachers_result->data_seek(0); 
?>

<div class="mb-3">
    <label for="teacher3" class="form-label">Teacher:</label>
    <select id="teacher3" name="teacher3" class="form-select">
        <option value="">-- Select Teacher --</option>
        <?php while($teacher = $teachers_result->fetch_assoc()): ?>
            <option value="<?php echo $teacher['first_name_eng'] . ' ' . $teacher['last_name_eng']; ?>" <?php if($row['teacher3_id'] == $teacher['first_name_eng'] . ' ' . $teacher['last_name_eng']) echo 'selected'; ?>>
                <?php echo $teacher['first_name_eng'] . " " . $teacher['last_name_eng']; ?>
            </option>
        <?php endwhile; ?>
    </select>
</div>

        <button type="submit" class="btn btn-success">Update Course</button>
    </form>
    <a href="index.php" class="btn btn-secondary mt-3">Back to Course List</a>
</div>
</div>
</div>
<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Core theme JS-->
        <script src="../startbootstrap-simple-sidebar-gh-pages\startbootstrap-simple-sidebar-gh-pages/js/scripts.js"></script>
        <script src="js/scripts.js"></script>
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

// Script to show modal based on session status
<?php if (isset($_SESSION["success"])) { ?>
            var modalMessage = "<?php echo $_SESSION['success']; ?>";
            document.getElementById('modalMessage').textContent = modalMessage;
            var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
            uploadModal.show();
            <?php unset($_SESSION["success"]); ?>
        <?php } elseif (isset($_SESSION["error"])) { ?>
            var modalMessage = "<?php echo $_SESSION['error']; ?>";
            document.getElementById('modalMessage').textContent = modalMessage;
            var uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
            uploadModal.show();
            <?php unset($_SESSION["error"]); ?>
        <?php } ?>
</script>
</body>
</html>
