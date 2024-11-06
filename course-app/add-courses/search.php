<?php
session_start();
include('db.php');

// ดึงค่าจากฟอร์มค้นหา
$search = isset($_GET['search']) ? $_GET['search'] : '';

// คำสั่ง SQL สำหรับค้นหาข้อมูลทั้งหมด
$sql = "SELECT c.*, 
               cl.room_number, cl.floor, cl.building, 
               t1.first_name as first_name1, t1.last_name as last_name1, 
               t2.first_name as first_name2, t2.last_name as last_name2, 
               t3.first_name as first_name3, t3.last_name as last_name3 
        FROM courses c
        LEFT JOIN classrooms cl ON c.classroom_id = cl.id
        LEFT JOIN teachers t1 ON c.teacher_id = t1.id
        LEFT JOIN teachers t2 ON c.teacher2_id = t2.id
        LEFT JOIN teachers t3 ON c.teacher3_id = t3.id
        WHERE c.subject_id LIKE ? 
           OR c.course_name LIKE ? 
           OR c.subject_name LIKE ?  -- เพิ่มการค้นหาสำหรับ Subject Name
           OR c.theory_hours LIKE ? 
           OR c.practical_hours LIKE ? 
           OR c.semester LIKE ? 
           OR c.academic_year LIKE ? 
           OR c.day_of_week LIKE ? 
           OR c.section LIKE ? 
           OR cl.room_number LIKE ? 
           OR c.teacher_id LIKE ? 
           OR c.teacher2_id LIKE ? 
           OR c.teacher3_id LIKE ?"; 
           

$stmt = $conn->prepare($sql);
$searchParam = "%" . $search . "%";
// จำนวนตัวแปรต้องตรงกัน
$stmt->bind_param("sssssssssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam,$searchParam);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Course List</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="./style.css">

    <!-- Menu left Sidebar -->
    <link href="./css/styles.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background: white;
        }
        .container h1 {
            color: black;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: start;
        }
        /* เพิ่ม CSS สำหรับกรอบวิชา */
        .list-group-items {
            border: 1px solid #ccc; /* ขอบกรอบ */
            border-radius: 10px; /* มุมกรอบ */
            margin-bottom: 15px; /* เว้นระยะห่างระหว่างกรอบ */
            padding: 15px; /* เพิ่มพื้นที่ภายในกรอบ */
            font-size: 1rem; /* ปรับขนาดตัวอักษรที่นี่ */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* เพิ่มเงา */
            font-family: 'Arial', sans-serif;
        }
        .list-group-items h5 {
        font-size: 1rem; /* ขนาดสำหรับหัวเรื่อง */
        font-family: 'Arial', sans-serif;
        }
        #clear_btn {
            border-radius: 5px;
            margin-left: 5px;
        }
        #search_btn {
            border-radius: 0px 5px 5px 0px;
        }
    </style>
</head>
<body>
<div class="d-flex" id="wrapper">

<!-- Include sidebar -->
<?php include('./sidebar.php'); ?>   

<!-- Page content wrapper-->
<div id="page-content-wrapper">

    <!-- Top navigation-->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
        <div class="container-fluid">
            <button class="btn btn-primary" id="sidebarToggle">Menu</button>
        </div>
    </nav>
    <!-- End Top navigation-->
<div class="container mt-5">
    <h1>Course List</h1>
    <hr>
    <br>
    <!-- เพิ่มฟอร์มสำหรับค้นหา -->
    <form action="search.php" method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" class="form-control" placeholder="Search Course" name="search">
            <button class="btn btn-outline-secondary" id="search_btn" type="submit">Search</button>
            <a href="./index.php" class="btn btn-outline-danger" id="clear_btn">Clear search</a>
        </div>
    </form>
    <a href="add.php" class="btn btn-success mb-4">Add New Course</a>
    <ul class="list-group">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li class='list-group-items d-flex justify-content-between align-items-center'>
                <div>
                    <h5 class='mb-2'>Subject Name: " . htmlspecialchars($row["subject_name"]) . "</h5>
                    <h5 class='mb-2'>Subject ID: " . htmlspecialchars($row["subject_id"]) . "</h5>
                    <p class='mb-2'>Course Name: " . htmlspecialchars($row["course_name"]) . "</p>
                    <p class='mb-2'>Theory Hours: " . htmlspecialchars($row["theory_hours"]) . "</p>
                    <p class='mb-2'>Practical Hours: " . htmlspecialchars($row["practical_hours"]) . "</p>
                    <p class='mb-2'>Semester: " . htmlspecialchars($row["semester"]) . "</p>
                    <p class='mb-2'>Academic Year: " . htmlspecialchars($row["academic_year"]) . "</p>
                    <p class='mb-2'>Day of Week: " . htmlspecialchars($row["day_of_week"]) . " | Start Time:" . htmlspecialchars($row["start_time"]) . " | End Time:" . htmlspecialchars($row["end_time"]) . "</p>
                    <p class='mb-2'>Section: " . htmlspecialchars($row["section"]) . "</p>
                    <p class='mb-2'>Classroom: " . ($row["room_number"] ? htmlspecialchars($row["room_number"]) . ", Floor: " . htmlspecialchars($row["floor"]) . ", Building: " . htmlspecialchars($row["building"]) : 'N/A') . "</p>
                    <p class='mb-2'>Teacher : " . htmlspecialchars($row["teacher_id"]) . "</p>
                    <p class='mb-2'>Teacher 2: " . htmlspecialchars($row["teacher2_id"]) . "</p>
                    <p class='mb-2'>Teacher 3: " . htmlspecialchars($row["teacher3_id"]) . "</p>
                </div>
                <div>
                    <a href='edit.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                    <button type='button' class='btn btn-danger delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='" . $row["id"] . "'>Delete</button>
                </div>
                </li>";
            }
        } else {
            echo "<li class='list-group-item'>0 results</li>";
        }
        ?>
    </ul>
</div>
<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      Are you sure you want to delete this record?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
      </div>
    </div>
  </div>
</div>
<!-- Bootstrap core JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/scripts.js"></script>
        <script>
            
            var deleteModal = document.getElementById('deleteModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget; // ปุ่มที่กดเพื่อเปิด modal
      var id = button.getAttribute('data-id'); // รับ id จาก data-id

      // อัปเดตลิงก์ในปุ่มลบใน modal
      var confirmDelete = document.getElementById('confirmDelete');
      confirmDelete.setAttribute('href', 'delete.php?id=' + id);
    });
        </script>
</body>
</html>
