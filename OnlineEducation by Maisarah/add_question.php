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
    $question = $_POST['question'];
    $optionA = $_POST['option_a'];
    $optionB = $_POST['option_b'];
    $optionC = $_POST['option_c'];
    $optionD = $_POST['option_d'];
    $correct = $_POST['correct_answer'];
    $standard = $_POST['standard'];
    $subject = $subjectTaught;
    $explanation = $_POST['explanation'];

    $sql =
    "INSERT INTO worksheet_questions
    (
        question_text,
        option_a,
        option_b,
        option_c,
        option_d,
        correct_answer,
        standard,
        subject,
        explanation
    )
    VALUES
    (
        '$question',
        '$optionA',
        '$optionB',
        '$optionC',
        '$optionD',
        '$correct',
        '$standard',
        '$subject',
        '$explanation'
    )";

    if($conn->query($sql))
    {
        echo
        "<script>
            alert('Question Added Successfully');
            window.location.href='manage_questions.php';
        </script>";
    }
    else
    {
        echo
        "<script>
            alert('Failed to Add Question');
        </script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Question</title>

<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="sidebar.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Comic Neue', cursive;
    background: #eef6ff;
    min-height: 100vh;
    display: flex;
}



.main {
    margin-left: 270px;
    width: calc(100% - 270px);
    padding: 30px 35px;
}

.header {
    background: white;
    border-radius: 30px;
    padding: 18px 25px;
    margin-bottom: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.header h1 {
    font-family: 'Fredoka One', cursive;
    color: #1E3A5F;
    font-size: 1.7rem;
}

.header p {
    color: #666;
    margin-top: 5px;
}

.form-card {
    background: white;
    border-radius: 30px;
    padding: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    max-width: 850px;
}

.form-title {
    color: #1E3A5F;
    margin-bottom: 20px;
    font-size: 1.2rem;
    font-weight: 800;
}

label {
    display: block;
    margin-bottom: 7px;
    color: #1E3A5F;
    font-weight: 800;
}

input,
select,
textarea {
    width: 100%;
    padding: 13px 15px;
    margin-bottom: 18px;
    border: 2px solid #D1E2F0;
    border-radius: 18px;
    outline: none;
    font-family: 'Comic Neue', cursive;
    font-size: 1rem;
    background: #F8FBFF;
}

input:focus,
select:focus,
textarea:focus {
    border-color: #4A90E2;
    background: white;
    box-shadow: 0 0 0 4px rgba(74,144,226,0.12);
}

textarea {
    min-height: 100px;
    resize: vertical;
}

.grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.btn-row {
    display: flex;
    gap: 12px;
    margin-top: 10px;
}

button,
.btn {
    border: none;
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    color: white;
    padding: 13px 25px;
    border-radius: 30px;
    font-family: 'Comic Neue', cursive;
    font-weight: 800;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    transition: 0.3s;
}

button:hover,
.btn:hover {
    transform: scale(1.04);
}

.btn-light {
    background: white;
    color: #1E3A5F;
    border: 2px solid #4A90E2;
}

.note {
    background: #E8F0F8;
    color: #1E3A5F;
    padding: 14px 18px;
    border-radius: 20px;
    margin-bottom: 22px;
    font-weight: 700;
}

@media(max-width: 800px) {
    .sidebar {
        display: none;
    }

    .main {
        margin-left: 0;
        width: 100%;
    }

    .grid {
        grid-template-columns: 1fr;
    }
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
        <a href="add_question.php" class="active"><i class="fas fa-plus-circle"></i> Add Question</a>
		<a href="manage_videos.php"><i class="fas fa-video"></i> Manage Videos</a>
		<a href="addVideo.php"><i class="fas fa-video"></i>  Add Video Lessons</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="progressI.php"><i class="fas fa-chart-line"></i> Progress</a>
		<a href="profilei.php"> <i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="quoteBox">
        🎓<br><br>
        "Teach with passion, inspire with love."
        <br><br>
        - One Step Ahead
    </div>
</div>

<div class="main">

    <div class="header">
        <h1>Add New Question ✨</h1>
        <p>Create worksheet questions for students.</p>
    </div>

    <div class="form-card">

        <div class="note">
            <i class="fas fa-lightbulb"></i>
            Fill in the question, options, correct answer, standard and subject.
        </div>

        <form method="POST">

            <p class="form-title">Question Details</p>

            <label>Question</label>
            <textarea name="question" placeholder="Type your question here..." required></textarea>

            <div class="grid">
                <div>
                    <label>Option A</label>
                    <input type="text" name="option_a" placeholder="Answer option A" required>
                </div>

                <div>
                    <label>Option B</label>
                    <input type="text" name="option_b" placeholder="Answer option B" required>
                </div>

                <div>
                    <label>Option C</label>
                    <input type="text" name="option_c" placeholder="Answer option C" required>
                </div>

                <div>
                    <label>Option D</label>
                    <input type="text" name="option_d" placeholder="Answer option D" required>
                </div>
            </div>

            <div class="grid">
                <div>
                    <label>Correct Answer</label>
                    <select name="correct_answer" required>
                        <option value="">-- Select correct answer --</option>
                        <option value="A">Option A</option>
                        <option value="B">Option B</option>
                        <option value="C">Option C</option>
                        <option value="D">Option D</option>
                    </select>
                </div>

                <div>
                    <label>Standard</label>
                    <select name="standard" required>
                        <option value="">-- Select standard --</option>
                        <option>Standard 1</option>
                        <option>Standard 2</option>
                        <option>Standard 3</option>
                        <option>Standard 4</option>
                        <option>Standard 5</option>
                        <option>Standard 6</option>
                    </select>
                </div>
            </div>
			
		<label>Subject</label>
		<input type="text" value="<?php echo htmlspecialchars($subjectTaught); ?>" readonly>
			

			<label>Explanation</label>
			<textarea
				name="explanation"
				placeholder="Explain why this answer is correct..."
				required>
			</textarea>

            <div class="btn-row">
                <button type="submit" name="save">
                    <i class="fas fa-save"></i> Save Question
                </button>

                <a href="manage_questions.php" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

        </form>
    </div>

</div>

</body>
</html>