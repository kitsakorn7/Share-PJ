<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Student</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="../style.css">

    <!-- Menu left Sidebar -->
    <link href="./css/styles.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }

    </style>

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
    <div class="container">
        <h1>Edit Classroom</h1>
        <?php
        // ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
        if(isset($_GET['id'])) {
            $id = $_GET['id'];

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

            // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลห้องเรียนที่ต้องการแก้ไข
            $sql = "SELECT * FROM classrooms WHERE id = $id";
            $result = $conn->query($sql);

            // ตรวจสอบว่ามีข้อมูลห้องเรียนหรือไม่
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
        ?>
        <form id="classroomForm" action="update_classroom.php" method="post">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <label for="room_number">Room Number:<span style="color: red;">*</span></label>
            <input type="text" id="room_number" name="room_number" value="<?php echo $row['room_number']; ?>" oninput="validateNumberInput(this)">
            <label for="floor">Floor:<span style="color: red;">*</span></label>
            <input type="text" id="floor" name="floor" value="<?php echo $row['floor']; ?>">
            <label for="building">Building:<span style="color: red;">*</span></label>
            <input type="text" id="building" name="building" value="<?php echo $row['building']; ?>">
            <input type="submit" value="Update">
        </form>
        <?php
            } else {
                echo "Room not found.";
            }

            // ปิดการเชื่อมต่อกับฐานข้อมูล
            $conn->close();
        } else {
            echo "Room ID not provided.";
        }
        ?>
    </div>
    </div>
</div>
    <script>
        
        document.getElementById("classroomForm").addEventListener("submit", function(event) {
    // ตรวจสอบความถูกต้องของข้อมูลและความครบถ้วน
    if (!this.checkValidity() || !checkDataCompleteness()) {
        // หยุดการกระทำของฟอร์ม
        event.preventDefault();
        event.stopPropagation();
        // แสดงข้อความแจ้งเตือน
        alert('กรุณากรอกข้อมูลให้ครบ');
    }
    this.classList.add("was-validated");
}, false);

// ฟังก์ชันเพิ่มเติมเพื่อตรวจสอบความครบถ้วนของข้อมูล
function checkDataCompleteness() {
    var roomNumber = document.getElementById("room_number").value;
    var floor = document.getElementById("floor").value;
    var building = document.getElementById("building").value;

    // ตรวจสอบว่าข้อมูลมีค่าว่างหรือไม่
    if (roomNumber.trim() === "" || floor.trim() === "" || building.trim() === "") {
        return false;
    }
    return true;
}

    </script> 

    <script>
        
        document.getElementById("classroomForm").addEventListener("submit", function(event) {
    // ตรวจสอบความถูกต้องของข้อมูลและความครบถ้วน
    if (!this.checkValidity() || !checkDataCompleteness()) {
        // หยุดการกระทำของฟอร์ม
        event.preventDefault();
        event.stopPropagation();
        // แสดงข้อความแจ้งเตือน
        alert('กรุณากรอกข้อมูลให้ครบ');
    }
    this.classList.add("was-validated");
}, false);

// ฟังก์ชันเพิ่มเติมเพื่อตรวจสอบความครบถ้วนของข้อมูล
function checkDataCompleteness() {
    var roomNumber = document.getElementById("room_number").value;
    var floor = document.getElementById("floor").value;
    var building = document.getElementById("building").value;

    // ตรวจสอบว่าข้อมูลมีค่าว่างหรือไม่
    if (roomNumber.trim() === "" || floor.trim() === "" || building.trim() === "") {
        return false;
    }
    return true;
}

function validateNumberInput(input) {
    // กรองเฉพาะตัวเลข
    input.value = input.value.replace(/\D/g, '');
}

    </script>  
    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="js/scripts.js"></script>  
</body>
</html>