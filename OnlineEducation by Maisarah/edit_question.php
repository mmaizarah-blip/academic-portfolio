<?php

include "db.php";

$id = $_GET['id'];

$sql =
"SELECT *
FROM worksheet_questions
WHERE question_id='$id'";

$result =
$conn->query($sql);

$row =
$result->fetch_assoc();

if(isset($_POST['update']))
{
    $question = $_POST['question'];
    $optionA = $_POST['option_a'];
    $optionB = $_POST['option_b'];
    $optionC = $_POST['option_c'];
    $optionD = $_POST['option_d'];
    $correct = $_POST['correct_answer'];
    $video = $_POST['video_link'];
    $standard = $_POST['standard'];
	$subject = $_POST['subject'];

    $update =
    "UPDATE worksheet_questions
    SET
    question_text='$question',
    option_a='$optionA',
    option_b='$optionB',
    option_c='$optionC',
    option_d='$optionD',
    correct_answer='$correct',
    video_link='$video',
    standard='$standard',
	subject='$subject'
    WHERE question_id='$id'";

    if($conn->query($update))
    {
        echo
        "<script>
            alert('Question Updated');
            window.location.href='question_list.php';
        </script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Question</title>

<style>

body
{
    font-family: Arial;
    background-color: #f2f2f2;
}

.container
{
    width: 650px;
    margin: auto;
    margin-top: 40px;
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0px 0px 10px lightgray;
}

h1
{
    color: purple;
    text-align: center;
}

input,
select
{
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    margin-bottom: 15px;
}

button
{
    width: 100%;
    padding: 12px;
    background: purple;
    color: white;
    border: none;
    border-radius: 5px;
}

.backBtn
{
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: purple;
}

</style>

</head>

<body>

<div class="container">

<h1>Edit Question</h1>

<form method="POST">

Question:

<input
type="text"
name="question"
value="<?php echo $row['question_text']; ?>"
required>

Option A:

<input
type="text"
name="option_a"
value="<?php echo $row['option_a']; ?>"
required>

Option B:

<input
type="text"
name="option_b"
value="<?php echo $row['option_b']; ?>"
required>

Option C:

<input
type="text"
name="option_c"
value="<?php echo $row['option_c']; ?>"
required>

Option D:

<input
type="text"
name="option_d"
value="<?php echo $row['option_d']; ?>"
required>

Correct Answer:

<select name="correct_answer">

<option value="A" <?php if($row['correct_answer']=="A") echo "selected"; ?>>
Option A
</option>

<option value="B" <?php if($row['correct_answer']=="B") echo "selected"; ?>>
Option B
</option>

<option value="C" <?php if($row['correct_answer']=="C") echo "selected"; ?>>
Option C
</option>

<option value="D" <?php if($row['correct_answer']=="D") echo "selected"; ?>>
Option D
</option>

</select>

Video Link / File Name:

<input
type="text"
name="video_link"
value="<?php echo $row['video_link']; ?>">

Standard:

<select name="standard">

<option <?php if($row['standard']=="Standard 1") echo "selected"; ?>>
Standard 1
</option>

<option <?php if($row['standard']=="Standard 2") echo "selected"; ?>>
Standard 2
</option>

<option <?php if($row['standard']=="Standard 3") echo "selected"; ?>>
Standard 3
</option>

<option <?php if($row['standard']=="Standard 4") echo "selected"; ?>>
Standard 4
</option>

<option <?php if($row['standard']=="Standard 5") echo "selected"; ?>>
Standard 5
</option>

<option <?php if($row['standard']=="Standard 6") echo "selected"; ?>>
Standard 6
</option>

</select>

<button
type="submit"
name="update">
Subject:

<select name="subject">
    <option value="Math">Math</option>
    <option value="Science">Science</option>
</select>
Update Question

</button>

</form>

<a
href="question_list.php"
class="backBtn">

← Back to Question List

</a>

</div>

</body>
</html>