<?php
session_start();
require "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Manage Teacher</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <link rel="stylesheet" href="./style.css">

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
                <h1 class="mt-3">Manage-Teacher</h1>
                <?php if (isset($_SESSION["success"])) { ?>
                    <div class="alert alert-success">
                        <?php 
                            echo $_SESSION["success"];
                            unset($_SESSION["success"]);
                        ?>
                    </div>
                <?php } ?>
                <?php if (isset($_SESSION["error"])) { ?>
                    <div class="alert alert-danger">
                        <?php 
                            echo $_SESSION["error"];
                            unset($_SESSION["error"]);
                        ?>
                    </div>
                <?php } ?>
                <hr>
                <br>
                <form action="./submit.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label for="teacher_id">Teacher ID:<span style="color: red;">*</span></label>
                        <input type="text" id="teacher_id" class="form-control" name="teacher_id" maxlength="12" required oninput="validateTeacherId(this)">
                        <div id="teacherIdError" style="color: red; display: none;">Please enter the complete 12-digit identification number.</div>
                    </div>

                    <div class="mb-3">
                        <label for="first_name">First Name:<span style="color: red;">*</span></label>
                        <input type="text" id="first_name" class="form-control" name="first_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="last_name">Last Name:<span style="color: red;">*</span></label>
                        <input type="text" id="last_name" class="form-control" name="last_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="first_name_eng">First Name (in English):<span style="color: red;">*</span></label>
                        <input type="text" id="first_name_eng" class="form-control" name="first_name_eng" required oninput="validateEnglishLetters(this)">
                    </div>

                    <div class="mb-3">
                        <label for="last_name_eng">Last Name (in English):<span style="color: red;">*</span></label>
                        <input type="text" id="last_name_eng" class="form-control" name="last_name_eng" required oninput="validateEnglishLetters(this)">
                    </div>

                    <div class="mb-3">
                        <label for="email">E-mail:<span style="color: red;">*</span></label>
                        <input type="email" id="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="faculty">Faculty:<span style="color: red;">*</span></label>
                        <input type="text" id="faculty" class="form-control" name="faculty" required>
                    </div>

                    <div class="mb-3">
                        <label for="department">Field of Study:<span style="color: red;">*</span></label>
                        <input type="text" id="department" class="form-control" name="department" required>
                    </div>

                    <button type="submit" class="btn btn-success">Upload</button>
                
                </form>
                <br>
            </div>
        </div>
    </div>

  <!-- Modal -->
<div class="modal fade" id="resultModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: transparent;"> <!-- ทำให้พื้นหลังโปร่งใส -->
                <h5 class="modal-title" id="exampleModalLabel">Upload result</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- ข้อความจะถูกเพิ่มที่นี่ -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/scriptss.js"></script>
    <script>
    function validateTeacherId(input) {
        // กรองเฉพาะตัวเลข
        input.value = input.value.replace(/\D/g, '');

        // ตรวจสอบความยาวของรหัสประจำตัว
        if (input.value.length < 12) {
            document.getElementById('teacherIdError').style.display = 'block';
        } else {
            document.getElementById('teacherIdError').style.display = 'none';
        }
    }

    function validateEnglishLetters(input) {
        // กรองเฉพาะตัวอักษรภาษาอังกฤษ (a-z, A-Z) และช่องว่าง
        input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
    }
    </script>
    <script>
$(document).ready(function() {
    // ตรวจสอบถ้ามีข้อความในเซสชัน
    <?php if (isset($_SESSION['result_message'])) { ?>
        $('#resultModal .modal-body').text("<?php echo $_SESSION['result_message']; ?>");
        
        // แสดง modal
        $('#resultModal').modal('show');

        // เมื่อ modal ถูกปิด ให้เปลี่ยนเส้นทางไปยัง index.php
        $('#resultModal').on('hidden.bs.modal', function () {
            window.location.href = 'index.php';
        });

        // ล้างเซสชันข้อความหลังจากแสดงแล้ว
        <?php unset($_SESSION['result_message']); ?>
    <?php } ?>
});
</script>

</body>
</html>
