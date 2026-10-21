<?php

$conn = new mysqli
(
    "127.0.0.1",
    "root",
    "",
    "education_system",
    3307
);

if($conn->connect_error)
{
    die("Connection Failed: " . $conn->connect_error);
}

?>