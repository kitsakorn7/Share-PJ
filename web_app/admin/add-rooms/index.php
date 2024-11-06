<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Rooms</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="../style.css">

    <!-- Menu left Sidebar -->
    <link href="./css/styles.css" rel="stylesheet"/>
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
                <h1 class="mt-3">Add Classroom</h1>
                <hr>
                <br>
                <form id="classroomForm" action="add_classroom.php" method="post">
                    <div class="mb-3">
                        <label for="room_number" class="form-label">Room Number:<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="room_number" oninput="validateNumberInput(this)" required>
                    </div>

                    <div class="mb-3">
                        <label for="floor" class="form-label">Floor:<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="floor" required>
                    </div>

                    <div class="mb-3">
                        <label for="building" class="form-label">Building:<span style="color: red;">*</span></label>
                        <input type="text" class="form-control" name="building" required>
                    </div>

                    <button type="submit" class="btn btn-success">Upload</button>
                    
                </form>
            </div>

 <!-- Modal สำหรับแจ้งเตือนสำเร็จ -->
 <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                The data has been successfully saved.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" id="confirmSubmit">Close</button>
            </div>

        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/scripts.js"></script> 
    <script>
        document.getElementById("classroomForm").addEventListener("submit", function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
            
            if (checkDataCompleteness()) {
                $('#successModal').modal('show'); // แสดง Modal สำเร็จถ้าข้อมูลครบ
            }
        }, false);

        // เมื่อคลิกปุ่มปิดใน Modal สำเร็จให้ทำการ submit ฟอร์ม
        document.getElementById("confirmSubmit").addEventListener("click", function() {
            document.getElementById("classroomForm").submit(); // ทำการส่งฟอร์มจริง
        });

        function checkDataCompleteness() {
            var roomNumber = document.getElementById("room_number").value;
            var floor = document.getElementById("floor").value;
            var building = document.getElementById("building").value;

            // ตรวจสอบว่าข้อมูลมีค่าว่างหรือไม่
            return !(roomNumber.trim() === "" || floor.trim() === "" || building.trim() === "");
        }

        function validateNumberInput(input) {
            input.value = input.value.replace(/\D/g, '');  // กรองเฉพาะตัวเลข
        }
    </script>
</body>
</html>