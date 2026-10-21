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

$selectedStandard = "";

if(isset($_GET['standard']))
{
    $selectedStandard = $_GET['standard'];
}

$sql =
"SELECT users.username,
        users.standard,
        SUM(student_progress.total_points) AS total_points,
        SUM(student_progress.total_attempt) AS total_attempt,
        SUM(student_progress.total_correct) AS total_correct
FROM users
INNER JOIN student_progress
ON users.user_code = student_progress.user_code
WHERE users.role='Student'";

if($selectedStandard != "")
{
    $sql .= " AND users.standard='$selectedStandard'";
}

$sql .= "
GROUP BY users.username, users.standard
ORDER BY total_points DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Progress</title>

<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">
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
    padding: 20px 28px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 8px 20px rgba(30,58,95,0.08);
}

.header h1 {
    font-family: 'Fredoka One', cursive;
    color: #1E3A5F;
    font-size: 1.8rem;
}

.header p {
    color: #64748B;
    margin-top: 6px;
    font-weight: 700;
}

.user-pill {
    background: #E8F0F8;
    color: #1E3A5F;
    padding: 10px 20px;
    border-radius: 40px;
    font-weight: 800;
}

.card {
    background: white;
    border-radius: 30px;
    padding: 25px;
    box-shadow: 0 8px 20px rgba(30,58,95,0.08);
}

.badge {
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    color: white;
    padding: 10px 22px;
    border-radius: 40px;
    font-weight: 800;
    display: inline-block;
    margin-bottom: 20px;
}

.filter-box {
    margin-bottom: 20px;
}

.table-wrap {
    overflow-x: auto;
    border-radius: 22px;
}

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 850px;
}

th {
    background: #1E3A5F;
    color: white;
    padding: 15px;
    text-align: center;
}

td {
    padding: 15px;
    border-bottom: 1px solid #E8F0F8;
    color: #334155;
    font-weight: 700;
    text-align: center;
}

tr:hover {
    background: #F5F9FF;
}

.name {
    color: #1E3A5F;
    font-weight: 800;
}

.progress-bg {
    width: 120px;
    height: 12px;
    background: #E8F0F8;
    border-radius: 20px;
    margin: auto;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    border-radius: 20px;
}

.empty {
    text-align: center;
    padding: 40px;
    color: #888;
    font-weight: 800;
}

.btn {
    display: inline-block;
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    color: white;
    padding: 11px 22px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: 800;
    margin-top: 10px;
    margin-right: 6px;
    transition: 0.3s;
}

.btn:hover {
    transform: scale(1.04);
}

@media(max-width: 800px) {
    .sidebar {
        display: none;
    }

    .main {
        margin-left: 0;
        width: 100%;
        padding: 20px;
    }

    .header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
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
        <a href="add_question.php"><i class="fas fa-plus-circle"></i> Add Question</a>
        <a href="manage_videos.php"><i class="fas fa-video"></i> Manage Videos</a>
        <a href="addVideo.php"><i class="fas fa-video"></i> Add Video Lessons</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="progressI.php" class="active"><i class="fas fa-chart-line"></i> Progress</a>
        <a href="profilei.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="quoteBox">
        📊<br><br>
        "Track progress, guide students better."
        <br><br>
        - One Step Ahead
    </div>
</div>

<div class="main">

    <div class="header">
        <div>
            <h1>Student Progress 📊</h1>
            <p>View students' worksheet performance and accuracy.</p>
        </div>

        <div class="user-pill">
            <i class="fas fa-user-tie"></i>
            <?php echo htmlspecialchars($user['username']); ?>
        </div>
    </div>

    <div class="card">

        <div class="badge">
            <i class="fas fa-chart-line"></i>
            Progress Report
        </div>

        <div class="filter-box">
            <a href="progressI.php" class="btn">All</a>
            <a href="progressI.php?standard=Standard 1" class="btn">Standard 1</a>
            <a href="progressI.php?standard=Standard 2" class="btn">Standard 2</a>
            <a href="progressI.php?standard=Standard 3" class="btn">Standard 3</a>
            <a href="progressI.php?standard=Standard 4" class="btn">Standard 4</a>
            <a href="progressI.php?standard=Standard 5" class="btn">Standard 5</a>
            <a href="progressI.php?standard=Standard 6" class="btn">Standard 6</a>
        </div>

        <div class="table-wrap">
            <table>
                <tr>
                    <th>No</th>
                    <th>Student Name</th>
                    <th>Standard</th>
                    <th>Stars</th>
                    <th>Attempts</th>
                    <th>Correct</th>
                    <th>Wrong</th>
                    <th>Accuracy</th>
                    <th>Progress Bar</th>
                </tr>

                <?php
                if($result && $result->num_rows > 0) {
                    $no = 1;

                    while($row = $result->fetch_assoc()) {
                        $points = $row['total_points'] ?? 0;
                        $attempt = $row['total_attempt'] ?? 0;
                        $correct = $row['total_correct'] ?? 0;
                        $wrong = $attempt - $correct;

                        if($attempt > 0) {
                            $accuracy = round(($correct / $attempt) * 100);
                        } else {
                            $accuracy = 0;
                        }

                        echo "<tr>";
                        echo "<td>".$no++."</td>";
                        echo "<td class='name'>".htmlspecialchars($row['username'])."</td>";
                        echo "<td>".htmlspecialchars($row['standard'] ?? '-')."</td>";
                        echo "<td>⭐ ".$points."</td>";
                        echo "<td>".$attempt."</td>";
                        echo "<td>".$correct."</td>";
                        echo "<td>".$wrong."</td>";
                        echo "<td>".$accuracy."%</td>";
                        echo "<td>
                                <div class='progress-bg'>
                                    <div class='progress-fill' style='width: ".$accuracy."%;'></div>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='empty'>No student progress found.</td></tr>";
                }
                ?>
            </table>
        </div>

        <a href="I_home.php" class="btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

    </div>

</div>

</body>
</html>