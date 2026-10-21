<?php

include "db.php";

$id = $_GET['id'];

$sql =
"DELETE FROM worksheet_questions
WHERE question_id='$id'";

if($conn->query($sql))
{
    echo
    "<script>
        alert('Question Deleted');
        window.location.href='question_list.php';
    </script>";
}
else
{
    echo
    "<script>
        alert('Delete Failed');
        window.location.href='question_list.php';
    </script>";
}

?>