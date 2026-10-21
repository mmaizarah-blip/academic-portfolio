<?php

include "db.php";

$sql =
"SELECT *
FROM worksheet_questions
ORDER BY question_id DESC";

$result =
$conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>

<title>Question List</title>

<style>

body
{
    font-family: Arial;
    background-color: #f2f2f2;
    padding: 30px;
}

.container
{
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0px 0px 10px lightgray;
}

h1
{
    color: purple;
}

.addBtn
{
    background: purple;
    color: white;
    padding: 10px 15px;
    text-decoration: none;
    border-radius: 5px;
}

table
{
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th
{
    background: purple;
    color: white;
    padding: 10px;
}

td
{
    border-bottom: 1px solid #ddd;
    padding: 10px;
}

.editBtn
{
    background: orange;
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 5px;
}

.deleteBtn
{
    background: red;
    color: white;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 5px;
}
.backBtn
{
    display: inline-block;

    background: gray;

    color: white;

    padding: 10px 15px;

    border-radius: 5px;

    text-decoration: none;

    margin-left: 10px;
}

.backBtn:hover
{
    background: #555;
}

</style>

</head>

<body>

<div class="container">

<h1>Worksheet Questions</h1>

<a href="add_question.php" class="addBtn">
+ Add Question
</a>

<a href="i_home.html" class="backBtn">
    ← Back to Home
</a>

<table>

<tr>
<th>ID</th>
<th>Question</th>
<th>Standard</th>
<th>Subject</th>
<th>Correct Answer</th>
<th>Video</th>
<th>Action</th>
</tr>

<?php

while($row = $result->fetch_assoc())
{

?>

<tr>

<td>
<?php echo $row['question_id']; ?>
</td>

<td>
<?php echo $row['question_text']; ?>
</td>

<td>
<?php echo $row['standard']; ?>
</td>

<td>
<?php echo $row['subject']; ?>
</td>

<td>
<?php echo $row['correct_answer']; ?>
</td>

<td>
<?php echo $row['video_link']; ?>
</td>

<td>

<a
href="edit_question.php?id=<?php echo $row['question_id']; ?>"
class="editBtn">

Edit

</a>

<a
href="delete_question.php?id=<?php echo $row['question_id']; ?>"
class="deleteBtn"
onclick="return confirm('Delete Question?')">

Delete

</a>

</td>

</tr>

<?php

}

?>

</table>

</div>

</body>
</html>