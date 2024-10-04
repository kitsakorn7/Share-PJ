<?php
include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $week_number = isset($_POST['week_number']) ? $_POST['week_number'] : '';
    $week_date = isset($_POST['week_date']) ? $_POST['week_date'] : '';
    $table_name = isset($_POST['table_name']) ? $_POST['table_name'] : '';
    $table_weeks_name = isset($_POST['table_weeks_name']) ? $_POST['table_weeks_name'] : '';
    $academic_semesterNav = isset($_POST['academic_semester']) ? $_POST['academic_semester'] : '';
    $on_time_time = isset($_POST['on_time_time']) ? $_POST['on_time_time'] : '';
    $late_time = isset($_POST['late_time']) ? $_POST['late_time'] : '';
    $absent_time = isset($_POST['absent_time']) ? $_POST['absent_time'] : '';
    $section = isset($_POST['section']) ? $_POST['section'] : '';

    try {
        $sql = "UPDATE $table_weeks_name SET week_date = :week_date, on_time_time = :on_time_time, late_time = :late_time, absent_time = :absent_time WHERE week_number = :week_number";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':week_number', $week_number);
        $stmt->bindParam(':week_date', $week_date);
        $stmt->bindParam(':on_time_time', $on_time_time);
        $stmt->bindParam(':late_time', $late_time);
        $stmt->bindParam(':absent_time', $absent_time);
        $stmt->execute();

        header("Location: attendance-check.php?table_name=" . urlencode($table_name) . "&table_weeks_name=" . urlencode($table_weeks_name) . '&academic_semester=' . urlencode($academic_semesterNav) . '&section=' . urlencode($section));
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
