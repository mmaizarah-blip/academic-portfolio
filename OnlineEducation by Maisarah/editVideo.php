<?php

$video_id = $_POST['video_id'];
$video_name = $_POST['video_title'];
$video_link = $_POST['video_link'];

echo "Data berjaya update:<br>";
echo "ID: " . $video_id . "<br>";
echo "Title: " . $video_name . "<br>";
echo "Link: " . $video_link . "<br>";

?>