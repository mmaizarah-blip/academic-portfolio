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

$sql = "SELECT * FROM users 
        WHERE role='Student'
        ORDER BY user_code ASC";

$result = $conn->query($sql);
$totalStudents = $result ? $result->num_rows : 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>View Students</title>

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

.summary-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.summary-card {
    background: white;
    border-radius: 25px;
    padding: 22px;
    box-shadow: 0 8px 20px rgba(30,58,95,0.08);
    text-align: center;
}

.summary-card i {
    font-size: 2rem;
    color: #4A90E2;
    margin-bottom: 10px;
}

.summary-card h2 {
    font-family: 'Fredoka One', cursive;
    color: #1E3A5F;
    font-size: 2rem;
}

.summary-card p {
    color: #64748B;
    font-weight: 800;
}

.card {
    background: white;
    border-radius: 30px;
    padding: 25px;
    box-shadow: 0 8px 20px rgba(30,58,95,0.08);
}

.card-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.card-title h2 {
    color: #1E3A5F;
    font-family: 'Fredoka One', cursive;
    font-size: 1.4rem;
}

.badge {
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    color: white;
    padding: 9px 20px;
    border-radius: 40px;
    font-weight: 800;
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
    text-align: left;
    font-size: 0.95rem;
}

td {
    padding: 15px;
    border-bottom: 1px solid #E8F0F8;
    color: #334155;
    font-weight: 700;
}

tr:hover {
    background: #F5F9FF;
}

.avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #E8F0F8;
    object-fit: cover;
    padding: 4px;
    border: 2px solid #D1E2F0;
}

.student-name {
    color: #1E3A5F;
    font-weight: 800;
}

.standard-pill {
    background: #E8F0F8;
    color: #1E3A5F;
    padding: 6px 14px;
    border-radius: 30px;
    font-weight: 800;
    display: inline-block;
}

.empty {
    text-align: center;
    padding: 45px;
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
    margin-top: 22px;
    transition: 0.3s;
}

.btn:hover {
    transform: scale(1.04);
}

@media(max-width: 900px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
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
        <a href="I_home.php">
            <i class="fas fa-home"></i> Dashboard
        </a>

        <a href="manage_questions.php">
            <i class="fas fa-question-circle"></i> Manage Questions
        </a>

        <a href="add_question.php">
            <i class="fas fa-plus-circle"></i> Add Question
        </a>
		

			<a href="manage_videos.php"><i class="fas fa-video"></i> Manage Videos</a>
		<a href="addVideo.php"><i class="fas fa-video"></i>  Add Video Lessons</a>

        <a href="view_students.php" class="active">
            <i class="fas fa-users"></i> View Students
        </a>

        <a href="progressI.php">
            <i class="fas fa-chart-line"></i> Progress
        </a>

        <a href="profilei.php">
            <i class="fas fa-user"></i> Profile
        </a>

        <a href="logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <div class="quoteBox">
        🎓<br><br>
        Teach with passion,<br>
        inspire with love.
    </div>

</div>

<div class="main">

    <div class="header">
        <div>
            <h1>View Students 👩‍🎓</h1>
            <p>Monitor all registered students in One Step Ahead.</p>
        </div>

        <div class="user-pill">
            <i class="fas fa-user-tie"></i>
            <?php echo htmlspecialchars($user['username']); ?>
        </div>
    </div>

    <div class="summary-grid">

        <div class="summary-card">
            <i class="fas fa-users"></i>
            <h2><?php echo $totalStudents; ?></h2>
            <p>Total Students</p>
        </div>

        <div class="summary-card">
            <i class="fas fa-school"></i>
            <h2>6</h2>
            <p>Standards</p>
        </div>

        <div class="summary-card">
            <i class="fas fa-book-open"></i>
            <h2>Math & Science</h2>
            <p>Learning Subjects</p>
        </div>

    </div>

    <div class="card">

        <div class="card-title">
            <h2>Student List</h2>

            <div class="badge">
                <i class="fas fa-list"></i>
                Registered Students
            </div>
        </div>

        <div class="table-wrap">

            <table>

                <tr>
                    <th>No</th>
                    <th>Avatar</th>
                    <th>Student Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                   
                </tr>

                <?php
                if($result && $result->num_rows > 0)
                {
                    $no = 1;

                    while($row = $result->fetch_assoc())
                    {
                        $studentAvatar = !empty($row['avatar']) ? $row['avatar'] : 'avatar1.jpg';
                ?>

                <tr>
                    <td><?php echo $no++; ?></td>

                    <td>
                        <img class="avatar" src="<?php echo htmlspecialchars($studentAvatar); ?>">
                    </td>

                    <td><?php echo htmlspecialchars($row['user_code']); ?></td>

                    <td class="student-name">
                        <?php echo htmlspecialchars($row['username']); ?>
                    </td>

                    <td><?php echo htmlspecialchars($row['email']); ?></td>

                    <td><?php echo htmlspecialchars($row['phone']); ?></td>

                   <td>
   
                </tr>

                <?php
                    }
                }
                else
                {
                ?>

                <tr>
                    <td colspan="6" class="empty">
                        No students found.
                    </td>
                </tr>

                <?php
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