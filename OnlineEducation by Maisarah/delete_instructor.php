<?php

session_start();
include "db.php";

if(isset($_GET['id']))
{
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    $sql =
    "DELETE FROM users
    WHERE user_code='$id'
    AND role='Instructor'";

    if($conn->query($sql))
    {
        echo
        "<script>

            alert('Instructor Deleted');

            window.location.href=
            'i_profile.php';

        </script>";
    }
    else
    {
        echo
        "<script>

            alert('Delete Failed');

            window.location.href=
            'i_profile.php';

        </script>";
    }
}

?>