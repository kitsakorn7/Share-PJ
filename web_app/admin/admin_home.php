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
    <link href="css/styles.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        h1 {
            text-align: center;
            font-size: 3rem; /* Bigger font size */
            color: #343a40; /* Dark grey color */
            margin-bottom: 30px; /* Add space below the heading */
        }
        .vertical-buttons {
            display: flex;
            flex-direction: column;
            align-items: center; /* Center the buttons horizontally */
            justify-content: center; /* Center the buttons vertically */
            gap: 15px; /* Space between buttons */
        }
        .vertical-buttons a {
            display: block;
            width: 250px; /* Set a fixed width for the buttons */
            padding: 15px 20px; /* More padding for larger buttons */
            font-size: 1.2rem; /* Increase font size */
            color: #fff; /* White text color */
            background-color: #28a745; /* Green color */
            border-radius: 50px; /* Rounded corners */
            text-align: center; /* Center the text inside the buttons */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add subtle shadow */
            transition: background-color 0.3s ease; /* Smooth transition on hover */
        }
        .vertical-buttons a:hover {
            background-color: #218838; /* Darken green on hover */
        }
        .navbar {
            background-color: #f8f9fa;
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
            <nav class="navbar navbar-expand-lg navbar-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">Menu</button>
                </div>
            </nav>
            <br>
            <br>
            <div class="container mt-5">
                <h1>Welcome to Admin Website!</h1>
                <!-- End Top navigation-->
                <br>
                <div class="vertical-buttons">
                    <a href="./add-rooms/index.php" class="btn btn-success">Add Rooms</a>
                    <a href="./add-students/index.php" class="btn btn-success">Add Students</a>
                    <a href="./add-teachers/index.php" class="btn btn-success">Add Teachers</a>
                    <a href="../../course-app/add-courses/add.php" class="btn btn-success">Manage Subjects</a>
                </div>
            </div>
            <script src="js/scriptss.js"></script>
        </div>
    </div>
</body>
</html>
