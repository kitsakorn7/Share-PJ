<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Classrooms</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="./style.css">

    <!-- Menu left Sidebar -->
    <link href="./css/styles.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <style>
        #search_btn {
            border-radius: 0px 5px 5px 0px;
        }
        #clear_btn {
            border-radius: 5px;
            margin-left: 5px;
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
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4caf50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .edit-link, .delete-link {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .edit-link {
            background-color: #4caf50;
            color: white;
        }
        .edit-link:hover {
            background-color: #45a049;
        }
        .delete-link {
            background-color: #f44336;
            color: white;
        }
        .delete-link:hover {
            background-color: #d32f2f;
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
        <h1>Classroom List</h1>
        <hr>
        <br>
        <form action="search_classrooms.php" method="GET" style="text-align: center;">
     <!-- ฟอร์มค้นหา -->
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search Classrooms" name="search">
                        <button class="btn btn-outline-secondary" id="search_btn" type="submit">Search</button>
                        <a href="./display_classrooms.php" class="btn btn-outline-danger" id="clear_btn">Clear search</a>  
                    </div>
</form>
<br>
        <?php
        // การเชื่อมต่อกับฐานข้อมูล MySQL
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "projecta";

        // สร้างการเชื่อมต่อ
        $conn = new mysqli($servername, $username, $password, $dbname);

        // ตรวจสอบการเชื่อมต่อ
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลจากฐานข้อมูล
        $sql = "SELECT * FROM classrooms";
        $result = $conn->query($sql);

        // ตรวจสอบว่ามีข้อมูลห้องเรียนหรือไม่
        if ($result->num_rows > 0) {
            // แสดงข้อมูลในรูปแบบตาราง
            echo "<table>
            <tr>
            <th>Room Number</th>
            <th>Floor</th>
            <th>Building</th>
            <th>Actions</th>
            </tr>";

            // วนลูปแสดงข้อมูลทีละแถว
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['room_number'] . "</td>";
                echo "<td>" . $row['floor'] . "</td>";
                echo "<td>" . $row['building'] . "</td>";
                echo "<td><a class='btn btn-warning' href='edit_classroom.php?id=" . $row['id'] . "'>Edit</a> | <button type='button' class='btn btn-danger delete-btn' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='" . $row["id"] . "'>Delete</button></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "0 results";
        }

        // ปิดการเชื่อมต่อกับฐานข้อมูล
        $conn->close();
        ?>
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
    <script src="js/scripts.js"></script>  
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
      confirmDelete.setAttribute('href', 'delete_classroom.php?id=' + id);
    });
  </script>
</body>
</html>
