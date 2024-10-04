<?php 
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
    $academic_semesterNav = $_SESSION['academic_semester'];
    
    // เตรียมคำสั่ง SQL สำหรับดึงข้อมูลจาก classrooms โดยใช้ classroom_id
    $sql_classroom = "SELECT room_number, floor, building FROM classrooms WHERE id = ?";
    $stmt_classroom = $conn->prepare($sql_classroom);
    $stmt_classroom->bind_param("i", $classroom_id); // "i" หมายถึง classroom_id เป็น integer
    $stmt_classroom->execute();
    $result_classroom = $stmt_classroom->get_result();

    if ($result_classroom->num_rows > 0) {
        $classroom = $result_classroom->fetch_assoc();
        
        // เก็บข้อมูลลงในตัวแปรเพื่อแสดงผล
        $room_number = $classroom['room_number'];
        $floor = $classroom['floor'];
        $building = $classroom['building'];
        
    } else {
        echo "Classroom not found.";
    }
?>

<!-- Header Details -->
<div class="mt-3">
    <h1 class="mb-4"><?php echo htmlspecialchars($subject_id); ?> : <?php echo htmlspecialchars($subject_name); ?></h1>
    <p>Semester : <?php echo htmlspecialchars($semester); ?></p>
    <p>Academic Year : <?php echo htmlspecialchars($academic_year); ?></p>
    <p>Section : <?php echo htmlspecialchars($section); ?></p>
    <p>Location : <?php echo htmlspecialchars($room_number . " Floor : " . $floor . " Building : " .  $building); ?></p>
    <br>
    <br>
    <p><?php echo htmlspecialchars(urldecode($day_of_week)); ?> (<?php echo htmlspecialchars(urldecode($start_time)); ?> - <?php echo htmlspecialchars(urldecode($end_time)); ?>)</p>
</div>
<!-- End Header Details -->
