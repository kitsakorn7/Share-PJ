<?php
session_start();
require "config.php";

// ตรวจสอบว่ามีพารามิเตอร์ 'error' หรือไม่
if (isset($_GET['error']) && $_GET['error'] == 'no_students_selected') {
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                var noSelectionModal = new bootstrap.Modal(document.getElementById('noSelectionModal'));
                noSelectionModal.show();

                // ลบ 'error' ออกจาก URL หลังจากที่แสดง Modal
                const url = new URL(window.location.href);
                url.searchParams.delete('error');
                window.history.replaceState({}, '', url);
            });
          </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Student List Public</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="../style.css">

    <!-- Menu left Sidebar -->
    <link href="./css/styles.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <style>
        body {
            background-color: #ffffff; /* สีพื้นหลังของทั้งหน้าเว็บ */
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
        .row h5 {
            
        }
    </style>
</head>
<body>
    <div class="d-flex" id="wrapper">

        <!-- Page content wrapper-->
        <div id="page-content-wrapper">

            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid mt-2">
                    <h6>Time attendance system with face recognition</h6>
                </div>
            </nav>
            <!-- End Top navigation-->

            <!-- Page content-->
            <div class="container mt-5">
                <h1>Student List Public</h1>
                <hr>
                <br> 
                <!-- ฟอร์มค้นหา -->
                <form action="student_public.php" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for student information" name="search">
                        <button class="btn btn-outline-secondary" id="search_btn" type="submit">Search</button>
                        <a href="./student_public.php" class="btn btn-outline-danger" id="clear_btn">Clear search</a>  
                    </div>
                </form>
                <!-- ฟอร์มสำหรับเลือกนักศึกษาและ Export -->
<form action="export_students_public.php" method="POST">

<div class="container-btn">
    <!-- ปุ่มเลือกทั้งหมด -->
    <input type="hidden" name="select_all" value="false" id="select_all_value">
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" id="select_all" onchange="toggleSelectAll(this)">
        <label class="form-check-label" for="select_all">Select all</label>
    </div>

    <!-- Export Btn -->
    <div class="input-group mb-3">
        <button class="btn btn-success" id="export_btn" type="submit">Export Excel</button>
    </div>
</div>

<?php
    // จำนวนข้อมูลต่อหน้า
    $limit = 9;

    // คำนวณหน้าปัจจุบัน
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // ตรวจสอบว่ามีการส่งคำขอค้นหาหรือไม่
    if (isset($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $stmt = $conn->prepare("SELECT * FROM images WHERE first_name LIKE :search OR last_name LIKE :search OR student_number LIKE :search OR Faculty LIKE :search OR Field_of_study LIKE :search LIMIT $limit OFFSET $offset");
        $stmt->bindParam(':search', $search);
        $stmt->execute();
    } else {
        // ถ้าไม่มีคำขอค้นหา ดึงข้อมูลทั้งหมด
        $stmt = $conn->prepare("SELECT * FROM images LIMIT $limit OFFSET $offset");
        $stmt->execute();
    }

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ดึงจำนวนข้อมูลทั้งหมดสำหรับการแบ่งหน้า
    $total_stmt = $conn->query("SELECT COUNT(*) FROM images");
    $total_records = $total_stmt->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    if ($students) {
        echo "<div class='row'>";
        foreach ($students as $student) {
            echo "<div class='col-md-4'>";
            echo '<div class="card mb-4">';
            echo '<img class="card-img-top" src="data:image/jpeg;base64,' . base64_encode($student['image']) . '" alt="Uploaded image" />';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']) . '</h5>';
            echo '<p class="card-text"><strong>Student Number:</strong> ' . htmlspecialchars($student['student_number']) . '</p>';
            echo '<p class="card-text"><strong>Faculty:</strong> ' . htmlspecialchars($student['Faculty']) . '</p>';
            echo '<p class="card-text"><strong>Branch:</strong> ' . htmlspecialchars($student['Field_of_study']) . '</p>';
            // echo '<a href="edit.php?id=' . htmlspecialchars($student['id']) . '" class="btn btn-warning" id="edit_btn">Edit</a>';
            // echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="' . htmlspecialchars($student['id']) . '">Delete</button>';
            echo '<div class="form-check mt-3">';
            echo '<input class="form-check-input" type="checkbox" name="selected_students[]" value="' . htmlspecialchars($student['id']) . '">';
            echo '<label class="form-check-label">Select</label>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "<p>ไม่พบนักศึกษาที่ตรงกับการค้นหา</p>";
    }

    // สร้าง hidden inputs สำหรับนักเรียนทั้งหมด
    foreach ($students as $student) {
        echo '<input type="hidden" name="all_students[]" value="' . htmlspecialchars($student['id']) . '">';
    }

    // สร้างลิงก์การแบ่งหน้า
    echo '<nav>';
    echo '<ul class="pagination">';

    // ปุ่มย้อนกลับ
    if ($page > 1) {
        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page - 1) . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') . '">Previous</a></li>';
    }

    // ลูปแสดงตัวเลขหน้า
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<li class="page-item"><a class="page-link" href="?page=' . $i . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') . '">' . $i . '</a></li>';
    }

    // ปุ่มถัดไป
    if ($page < $total_pages) {
        echo '<li class="page-item"><a class="page-link" href="?page=' . ($page + 1) . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '') . '">Next</a></li>';
    }

    echo '</ul>';
    echo '</nav>';
?>

            <!-- Modal Popup -->
            <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadModalLabel">Update result</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="modalMessage"></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
                </form>
                <!-- Modal for delete confirmation -->
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
<!-- Modal for alert when no students are selected -->
<div class="modal fade" id="noSelectionModal" tabindex="-1" aria-labelledby="noSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noSelectionModalLabel">No student selected.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                No students were selected for Export.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal for alert when no students are selected -->
<div class="modal fade" id="noSelectionModal" tabindex="-1" aria-labelledby="noSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noSelectionModalLabel">No student selected.</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Please select the student you want to export.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSelectAll(selectAllCheckbox) {
    document.getElementById('select_all_value').value = selectAllCheckbox.checked ? 'true' : 'false';
    var checkboxes = document.querySelectorAll('input[name="selected_students[]"]');
    for (var checkbox of checkboxes) {
        checkbox.checked = selectAllCheckbox.checked; // ตั้งค่า checkbox ของนักเรียนตามสถานะของ Select all
    }
}
</script>        
            </div>
        </div>
    </div>
    
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                    localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
                });
            }
        });
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
    <script>
// ใช้ event delegation เพื่อจัดการการเปิด modal และการลบข้อมูล
var deleteModal = document.getElementById('deleteModal'); // Modal ที่จะแสดงเมื่อคลิกปุ่ม Delete
var noSelectionModal = new bootstrap.Modal(document.getElementById('noSelectionModal')); // Modal ที่จะแสดงเมื่อไม่มีการเลือกข้อมูล
var selectAllCheckbox = document.getElementById('select_all'); // Checkbox สำหรับ Select all

// เมื่อเปิด modal ขึ้นมา
deleteModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // ปุ่มที่กระตุ้นให้เปิด modal

    // ถ้าปุ่มลบ (Delete Selected) ถูกคลิก
    if (button.id === 'deleteSelected') {
        // ดึงรายการที่ถูกเลือกจาก checkbox
        var checkboxes = document.querySelectorAll('input[name="selected_students[]"]:checked');
        var studentIds = Array.from(checkboxes).map(cb => cb.value); // สร้าง array ของ student IDs

        // ถ้าไม่มีการเลือก checkbox ใดๆ
        if (studentIds.length === 0 && !selectAllCheckbox.checked) {
            event.preventDefault(); // ยกเลิกการเปิด modal
            noSelectionModal.show(); // แสดง modal แจ้งเตือนว่าไม่ได้เลือกข้อมูล
            return; // หยุดการทำงาน
        }

        // ตรวจสอบว่าผู้ใช้ได้เลือก "Select all" หรือไม่
        if (selectAllCheckbox.checked) {
            // ถ้าเลือก Select all ให้ลบทั้งหมด
            var deleteLink = deleteModal.querySelector('#confirmDelete'); // ลิงก์ใน modal
            deleteLink.href = 'delete.php?id=all'; // ตั้ง href สำหรับลบทั้งหมด
        } else {
            // ถ้าเลือกเฉพาะบางรายการ
            var deleteLink = deleteModal.querySelector('#confirmDelete');
            deleteLink.href = 'delete.php?id=' + studentIds.join(','); // ตั้ง href สำหรับลบเฉพาะที่เลือก
        }
    } else {
        // กรณีที่ปุ่มอื่นที่ไม่ใช่ "Delete Selected" ถูกคลิก (เช่น ปุ่มลบแถวเดียว)
        var studentId = button.getAttribute('data-id'); // ดึง ID นักเรียนจาก attribute data-id
        var deleteLink = deleteModal.querySelector('#confirmDelete');
        deleteLink.href = 'delete.php?id=' + studentId; // ตั้ง href สำหรับลบแถวนั้น
    }
});
</script>

</body>
</html>
