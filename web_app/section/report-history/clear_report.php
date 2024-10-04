<?php
session_start();
include('config.php');


$table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
$table_weeks_name = isset($_POST['table_weeks_name']) ? $_POST['table_weeks_name'] : '';
$academic_semesterNav = isset($_POST['academic_semester']) ? $_POST['academic_semester'] : '';
$section = isset($_POST['section']) ? $_POST['section'] : '';
$subject_name = isset($_POST['subject_name']) ? $_POST['subject_name'] : '';
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';
$semester = isset($_POST['semester']) ? $_POST['semester'] : '';

$table_report = "report_daily_" . preg_replace('/\s+/', '_', $subject_name) . "_" . $section . "_" . $academic_year . "_" . $semester;

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "DELETE FROM $table_report";
if ($conn->query($sql) === TRUE) {
    echo "All records deleted successfully";
} else {
    echo "Error deleting records: " . $conn->error;
}

$conn->close();

$url_report = './summary_report.php?table_name=' . urlencode($table_name) . '&table_weeks_name=' . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section);
// Redirect back to the report page
header("Location: $url_report");
exit;
?>
