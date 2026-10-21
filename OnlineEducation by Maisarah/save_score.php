<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']))
{
    echo "not logged in";
    exit();
}

$userCode = $_SESSION['user_code'];

$points = $_POST['points'];
$attempt = $_POST['attempt'];
$correct = $_POST['correct'];
$standard = $_POST['standard'];
$subject = $_POST['subject'];

$sql =
"INSERT INTO student_progress
(
    user_code,
    standard,
    subject,
    total_points,
    total_attempt,
    total_correct
)
VALUES
(
    '$userCode',
    '$standard',
    '$subject',
    '$points',
    '$attempt',
    '$correct'
)";

if($conn->query($sql))
{
    echo "success";
}
else
{
    echo "SQL ERROR: " . $conn->error;
}
?>