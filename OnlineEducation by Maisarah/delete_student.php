<?php

session_start();
include "db.php";

if(!isset($_SESSION['user_code']))
{
    echo
    "<script>
        alert('Please login first');
        window.location.href='login.php';
    </script>";
    exit();
}

if($_SESSION['role'] != "Admin")
{
    echo
    "<script>
        alert('Access denied');
        window.location.href='login.php';
    </script>";
    exit();
}

if(isset($_GET['id']))
{
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $conn->query("DELETE FROM student_progress WHERE user_code='$id'");

    $sql =
    "DELETE FROM users
    WHERE user_code='$id'
    AND role='Student'";

    if($conn->query($sql))
    {
        echo
        "<script>
            alert('Student Deleted');
            window.location.href='s_profile.php';
        </script>";
        exit();
    }
    else
    {
        echo
        "<script>
            alert('Delete failed: ".$conn->error."');
            window.location.href='s_profile.php';
        </script>";
        exit();
    }
}
else
{
    echo
    "<script>
        alert('No student selected');
        window.location.href='s_profile.php';
    </script>";
    exit();
}

?>