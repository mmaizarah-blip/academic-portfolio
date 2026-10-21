<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']) || strtolower($_SESSION['role']) != 'instructor')
{
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];

$userSql = "SELECT * FROM users WHERE user_code='$userCode'";
$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

$subjectTaught = $user['subject_taught'];

if(isset($_POST['save']))
{
    $standard = $_POST['standard'];
$subject = $subjectTaught;
$chapter = $_POST['chapter'];
$title = $_POST['title'];
$video_file = $_POST['video_file'];

$sql = "INSERT INTO learning_videos
        (
            standard,
            subject,
            chapter,
            title,
            video_file
        )
        VALUES
        (
            '$standard',
            '$subject',
            '$chapter',
            '$title',
            '$video_file'
        )";

    if($conn->query($sql))
    {
        echo "<script>
        alert('Video lesson added successfully');
        window.location.href='addVideo.php';
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Video Lesson</title>

<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="sidebar.css">

<style>

*
{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body
{
    font-family: 'Comic Neue', cursive;
    background: #eef6ff;
    min-height: 100vh;
    display: flex;
}


.main
{
    margin-left:270px;
    width:calc(100% - 270px);
    padding:30px;
}

.header
{
    background:white;
    border-radius:25px;
    padding:20px;
    margin-bottom:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.06);
}

.header h1
{
    color:#1E3A5F;
}

.card
{
    background:white;
    padding:30px;
    border-radius:25px;
    max-width:800px;
    box-shadow:0 5px 15px rgba(0,0,0,0.06);
}

label
{
    display:block;
    margin-bottom:8px;
    font-weight:bold;
    color:#1E3A5F;
}
.quoteBox {
    margin-top: 40px;
    background: rgba(255,255,255,0.1);
    padding: 18px;
    border-radius: 15px;
    text-align: center;
    font-size: 0.75rem;
    line-height: 1.5;
    color: white;
}

input
{
    width:100%;
    padding:12px;
    border:2px solid #D1E2F0;
    border-radius:15px;
    margin-bottom:18px;
}

input:focus
{
    outline:none;
    border-color:#4A90E2;
}
select
{
    width:100%;
    padding:12px;
    border:2px solid #D1E2F0;
    border-radius:15px;
    margin-bottom:18px;
}

select:focus
{
    outline:none;
    border-color:#4A90E2;
}
button
{
    background:linear-gradient(135deg,#4A90E2,#2B5B84);
    color:white;
    border:none;
    padding:12px 25px;
    border-radius:25px;
    cursor:pointer;
    font-weight:bold;
}

button:hover
{
    opacity:0.9;
}

</style>
</head>

<body>

<div class="sidebar">

    <div class="logoBox">
        <img src="logo.png">
        <div>
            <h2>OneStepAhead</h2>
            <p>Instructor Panel</p>
        </div>
    </div>

    <div class="nav">
        <a href="I_home.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_questions.php"><i class="fas fa-question-circle"></i> Manage Questions</a>
        <a href="add_question.php"><i class="fas fa-plus-circle"></i> Add Question</a>
        <a href="manage_videos.php"><i class="fas fa-video"></i> Manage Videos</a>
		<a href="addVideo.php" class="active"><i class="fas fa-video"></i> Add Video Lessons</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="progressI.php"><i class="fas fa-chart-line"></i> Progress</a>
        <a href="profilei.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
	<div class="quoteBox">
    🎥<br><br>
    "Add videos, inspire learning."
    <br><br>
    - One Step Ahead
</div>

</div>

<div class="main">

    <div class="header">
        <h1>📹 Add Video Lesson</h1>
        <p>Add learning videos for students.</p>
    </div>

    <div class="card">

        <form method="POST">

            <label>Standard</label>
<select name="standard" required>
    <option value="">Select Standard</option>
    <option>Standard 1</option>
    <option>Standard 2</option>
    <option>Standard 3</option>
    <option>Standard 4</option>
    <option>Standard 5</option>
    <option>Standard 6</option>
</select>

<label>Subject</label>
<input type="text" value="<?php echo htmlspecialchars($subjectTaught); ?>" readonly>

<label>Chapter</label>
<input type="number" name="chapter" required>

<label>Video Title</label>
<input type="text" name="title" required>

<label>Video File</label>
<input type="text" name="video_file" placeholder="example.mp4" required>
            <button type="submit" name="save">
                <i class="fas fa-save"></i> Save Video
            </button>

        </form>

    </div>

</div>

</body>
</html>