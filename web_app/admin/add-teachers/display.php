<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Teacher List</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="./style.css">

    <!-- Menu left Sidebar -->
    <link href="./css/styles.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body { 
            background-color: white;         
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
        
        .search-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .edit-btn, .delete-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .delete-btn {
            background-color: #f44336;
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

            <!-- Page content-->
            <div class="container mt-5">
            <h1 style="text-align: left;">Teacher List</h1>
            <hr>
            <br>
            <div class="search-container">
            <form action="display.php" method="get">
            <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search for teachers information" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button class="btn btn-outline-secondary" id="search_btn" type="submit">Search</button>
                        <a href="./display.php" class="btn btn-outline-danger" id="clear_btn">Clear search</a>  
                    </div>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Teacher ID</th>
                    <th>First Name-Last Name</th>
                    <th>Faculty</th>
                    <th>Field of Study</th>
                    <th>E-mail</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "projecta";

                // สร้างการเชื่อมต่อ
                $conn = new mysqli($servername, $username, $password, $dbname);

                // ตรวจสอบการเชื่อมต่อ
                if ($conn->connect_error) {
                    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
                }

                // รับค่าการค้นหาจากฟอร์ม
                $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

                // ดึงข้อมูลจากฐานข้อมูล
                if ($search) {
                    $sql = "SELECT id, teacher_id, first_name, last_name, faculty, department, email
                            FROM teachers 
                            WHERE first_name LIKE '%$search%' 
                            OR last_name LIKE '%$search%' 
                            OR faculty LIKE '%$search%' 
                            OR department LIKE '%$search%' 
                            OR email LIKE '%$search%'
                            OR teacher_id LIKE '%$search%'"; 
                } else {
                    $sql = "SELECT id, teacher_id, first_name, last_name, faculty, department, email FROM teachers";
                }

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // แสดงผลข้อมูลแต่ละแถว
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["teacher_id"] . "</td>";
                        echo "<td>" . $row["first_name"] . " " . $row["last_name"] . "</td>";
                        echo "<td>" . $row["faculty"] . "</td>";
                        echo "<td>" . $row["department"] . "</td>";
                        echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                        echo "<td>
                                <a href='edit.php?id=" . $row["id"] . "' class='btn btn-warning'>Edit</a>
                                <button type='button' class='btn btn-danger delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='" . $row["id"] . "'>Delete</button>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>ไม่พบข้อมูล</td></tr>";
                }

                // ปิดการเชื่อมต่อ
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update result</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php
        // แสดงข้อความจาก update.php
        if (isset($_SESSION['modalMessage'])) {
            echo $_SESSION['modalMessage'];
            unset($_SESSION['modalMessage']); // ลบค่าเพื่อไม่ให้แสดงอีกครั้งเมื่อรีเฟรชหน้า
        }
        ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
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
        <!-- Core theme JS-->
        <script src="../startbootstrap-simple-sidebar-gh-pages\startbootstrap-simple-sidebar-gh-pages/js/scripts.js"></script>
        <script src="js/scriptss.js"></script>
        <script>
            // แสดง modal หาก showModal ถูกตั้งค่าเป็น true
  <?php if (isset($_GET['showModal']) && $_GET['showModal'] == 'true'): ?>
    var myModal = new bootstrap.Modal(document.getElementById('resultModal'));
    myModal.show();

    // ลบพารามิเตอร์ showModal ออกจาก URL หลังจากแสดง Modal เสร็จแล้ว
    const url = new URL(window.location.href);
    url.searchParams.delete('showModal');
    window.history.replaceState({}, document.title, url.toString());
  <?php endif; ?>

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
