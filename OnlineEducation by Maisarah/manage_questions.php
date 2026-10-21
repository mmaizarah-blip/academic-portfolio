<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']) || strtolower($_SESSION['role']) != 'instructor') {
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];

$userSql = "SELECT * FROM users WHERE user_code='$userCode'";
$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

$subjectTaught = $user['subject_taught'];

if(isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $deleteSql = "DELETE FROM worksheet_questions 
                  WHERE question_id='$id' 
                  AND subject='$subjectTaught'";
    $conn->query($deleteSql);

    echo "<script>
        alert('Question deleted successfully');
        window.location.href='manage_questions.php';
    </script>";
}

$sql = "SELECT * FROM worksheet_questions 
        WHERE subject='$subjectTaught'
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Manage Questions</title>

   <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

       body {
    font-family: 'Comic Neue', 'Comic Neue', cursive;
    background: linear-gradient(135deg, #E8F0F8 0%, #D1E2F0 100%);
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .header h1 {
            font-family: 'Fredoka One', cursive;
            color: #1E3A5F;
            font-size: 1.6rem;
        }

        .header p {
            color: #666;
            margin-top: 5px;
        }

        .user-pill {
            background: #E8F0F8;
            color: #1E3A5F;
            padding: 10px 18px;
            border-radius: 40px;
            font-weight: bold;
        }

        .card {
            background: white;
            border-radius: 30px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .top-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 22px;
        }

        .subject-badge {
            background: linear-gradient(135deg, #4A90E2, #2B5B84);
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            display: inline-block;
            font-weight: bold;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #4A90E2, #2B5B84);
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            border: none;
        }

        .btn:hover {
            transform: scale(1.04);
        }

        .btn-edit {
            background: #4A90E2;
            padding: 7px 13px;
            border-radius: 20px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-delete {
            background: #DC2626;
            padding: 7px 13px;
            border-radius: 20px;
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 20px;
        }

        th {
            background: #1E3A5F;
            color: white;
            padding: 14px;
            text-align: left;
        }

        td {
            padding: 13px 14px;
            border-bottom: 1px solid #E8F0F8;
            color: #333;
            vertical-align: top;
        }

        tr:hover {
            background: #F5F9FF;
        }

        .question {
            max-width: 420px;
            line-height: 1.4;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #888;
            font-weight: bold;
        }

        .back {
            margin-top: 20px;
        }
    </style>
<link rel="stylesheet" href="sidebar.css">
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
        <a href="manage_questions.php" class="active"><i class="fas fa-question-circle"></i> Manage Questions</a>
        <a href="add_question.php"><i class="fas fa-plus-circle"></i> Add Question</a>
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
        <div>
            <h1>Manage Questions 📚</h1>
            <p>View, edit and delete your worksheet questions.</p>
        </div>

        <div class="user-pill">
            <i class="fas fa-user"></i>
            <?php echo htmlspecialchars($user['username']); ?>
        </div>
    </div>

    <div class="card">
        <div class="top-action">
            <div class="subject-badge">
                <i class="fas fa-book"></i>
                Subject: <?php echo htmlspecialchars($subjectTaught); ?>
            </div>

            <a href="add_question.php" class="btn">
                <i class="fas fa-plus"></i> Add Question
            </a>
        </div>

        <table>
            <tr>
                <th>No</th>
                <th>Standard</th>
                <th>Question</th>
                <th>Correct Answer</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>

            <?php
            if($result && $result->num_rows > 0) {
                $no = 1;

                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$no++."</td>";
                    echo "<td>".htmlspecialchars($row['standard'])."</td>";
                    echo "<td class='question'>".htmlspecialchars($row['question_text'])."</td>";
                    echo "<td>".htmlspecialchars($row['correct_answer'])."</td>";
                    echo "<td>".$row['created_at']."</td>";
                    echo "<td>
                            <a class='btn-edit' href='edit_question.php?id=".$row['question_id']."'>Edit</a>
                            <a class='btn-delete' href='manage_questions.php?delete=".$row['question_id']."' onclick=\"return confirm('Delete this question?')\">Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='empty'>No questions found. Add your first question 💡</td></tr>";
            }
            ?>
        </table>

        <div class="back">
            <a href="I_home.php" class="btn">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

</div>

</body>
</html>