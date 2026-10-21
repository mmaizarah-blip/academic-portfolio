<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']) || strtolower(trim($_SESSION['role'])) != 'instructor')
{
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];

$userSql = "SELECT * FROM users WHERE user_code='$userCode'";
$userResult = $conn->query($userSql);

if(!$userResult || $userResult->num_rows == 0)
{
    header("Location: login.php");
    exit();
}

$user = $userResult->fetch_assoc();

$subjectTaught = "";

if(isset($user['subject_taught']) && $user['subject_taught'] != "")
{
    $subjectTaught = $user['subject_taught'];
}

if($subjectTaught == "")
{
    $subjectTaught = null;
}

if($subjectTaught)
{
    $questionsSql = "SELECT COUNT(*) AS total FROM worksheet_questions WHERE subject='$subjectTaught'";
    $questionsResult = $conn->query($questionsSql);
    $totalQuestions = $questionsResult->fetch_assoc()['total'];
}
else
{
    $totalQuestions = 0;
}

$studentsSql = "SELECT COUNT(*) AS total FROM users WHERE LOWER(role)='student'";
$studentsResult = $conn->query($studentsSql);
$totalStudents = $studentsResult->fetch_assoc()['total'];

if($subjectTaught)
{
    $recentSql =
    "SELECT question_text, created_at
    FROM worksheet_questions
    WHERE subject='$subjectTaught'
    ORDER BY created_at DESC
    LIMIT 5";

    $recentResult = $conn->query($recentSql);
}
else
{
    $recentResult = false;
}

if($subjectTaught)
{
    $easySql =
    "SELECT COUNT(*) AS count
    FROM worksheet_questions
    WHERE subject='$subjectTaught'
    AND standard IN ('Standard 1', 'Standard 2')";

    $mediumSql =
    "SELECT COUNT(*) AS count
    FROM worksheet_questions
    WHERE subject='$subjectTaught'
    AND standard IN ('Standard 3', 'Standard 4')";

    $hardSql =
    "SELECT COUNT(*) AS count
    FROM worksheet_questions
    WHERE subject='$subjectTaught'
    AND standard IN ('Standard 5', 'Standard 6')";

    $easyCount = $conn->query($easySql)->fetch_assoc()['count'];
    $mediumCount = $conn->query($mediumSql)->fetch_assoc()['count'];
    $hardCount = $conn->query($hardSql)->fetch_assoc()['count'];
}
else
{
    $easyCount = 0;
    $mediumCount = 0;
    $hardCount = 0;
}

$total = $easyCount + $mediumCount + $hardCount;
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Instructor Dashboard | One Step Ahead</title>
<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
*
{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
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
    margin-left: 270px;
    width: calc(100% - 270px);
    padding: 30px 40px;
}

.header
{
    background: white;
    border-radius: 28px;
    padding: 16px 25px;
    margin-bottom: 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.06);
}

.logo-area
{
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo-area img
{
    width: 55px;
    height: 55px;
    border-radius: 50%;
}

.logo-area h1
{
    font-size: 22px;
    color: #1E3A5F;
}

.user-info
{
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-info span
{
    font-weight: bold;
    color: #1E3A5F;
    background: #E8F0F8;
    padding: 9px 18px;
    border-radius: 30px;
}

.logout-btn
{
    background: #2B5B84;
    color: white;
    padding: 9px 20px;
    border-radius: 30px;
    text-decoration: none;
}

.welcome-card
{
    background: white;
    border-radius: 30px;
    padding: 30px;
    margin-bottom: 25px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.06);
}

.welcome-card h2
{
    color: #1E3A5F;
    font-size: 30px;
    margin-bottom: 8px;
}

.welcome-card p
{
    color: #666;
}

.subject-badge
{
    display: inline-block;
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    color: white;
    padding: 9px 22px;
    border-radius: 30px;
    margin-top: 15px;
    font-weight: bold;
}

.stats-grid
{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
    margin-bottom: 25px;
}

.stat-card
{
    background: white;
    border-radius: 25px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.06);
    transition: 0.3s;
}

.stat-card:hover
{
    transform: translateY(-5px);
}

.stat-card i
{
    font-size: 36px;
    color: #4A90E2;
}

.stat-card h3
{
    font-size: 34px;
    color: #1E3A5F;
    margin: 10px 0;
}

.stat-card p
{
    color: #777;
}

.subject-card,
.recent-section,
.level-section,
.tips-card,
.warning-box
{
    background: white;
    border-radius: 28px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.06);
}

.subject-card
{
    text-align: center;
}

.subject-card i
{
    font-size: 55px;
    color: #4A90E2;
    margin-bottom: 12px;
}

.subject-card h2
{
    color: #1E3A5F;
    font-size: 28px;
    margin-bottom: 10px;
}

.subject-card p
{
    color: #666;
    margin-bottom: 18px;
}

.btn
{
    display: inline-block;
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    color: white;
    padding: 11px 24px;
    border-radius: 30px;
    text-decoration: none;
    font-weight: bold;
    margin: 5px;
}

.btn-outline
{
    background: white;
    border: 2px solid #4A90E2;
    color: #1E3A5F;
}

.section-title
{
    color: #1E3A5F;
    margin-bottom: 15px;
    font-size: 22px;
}

.activity-item
{
    padding: 12px 0;
    border-bottom: 1px solid #E8F0F8;
    display: flex;
    align-items: center;
    gap: 10px;
}

.level-bar
{
    margin-bottom: 18px;
}

.level-label
{
    display: flex;
    justify-content: space-between;
    margin-bottom: 7px;
}

.progress-bar-bg
{
    background: #E8F0F8;
    border-radius: 20px;
    height: 12px;
    overflow: hidden;
}

.progress-fill-easy
{
    background: linear-gradient(90deg, #6BCB77, #4D96FF);
    width: <?php echo $total > 0 ? ($easyCount / $total) * 100 : 0; ?>%;
    height: 100%;
}

.progress-fill-medium
{
    background: linear-gradient(90deg, #FFD93D, #FF9B50);
    width: <?php echo $total > 0 ? ($mediumCount / $total) * 100 : 0; ?>%;
    height: 100%;
}

.progress-fill-hard
{
    background: linear-gradient(90deg, #FF6B9E, #C084FC);
    width: <?php echo $total > 0 ? ($hardCount / $total) * 100 : 0; ?>%;
    height: 100%;
}

.warning-box
{
    background: #fff1f1;
    border-left: 6px solid red;
    text-align: center;
}

.warning-box h3
{
    color: red;
    margin: 10px 0;
}
.profile-pic
{
    width:45px;
    height:45px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid #D1E2F0;
}

.footer
{
    text-align: center;
    color: #888;
    padding: 20px;
}

@media(max-width: 900px)
{
    .stats-grid
    {
        grid-template-columns: 1fr;
    }
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
        <a href="I_home.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_questions.php"><i class="fas fa-question-circle"></i> Manage Questions</a>
        <a href="add_question.php"><i class="fas fa-plus-circle"></i> Add Question</a>
		<a href="manage_videos.php"><i class="fas fa-video"></i> Manage Videos</a>
		<a href="addVideo.php"><i class="fas fa-video"></i>  Add Video Lessons</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="progressI.php"><i class="fas fa-chart-line"></i> Progress</a>
        <a href="profilei.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="quoteBox">
        🎓<br><br>
        Teach with passion,<br>
        inspire with love.
    </div>

</div>

<div class="main">

    <div class="header">
        <div class="logo-area">
            <img src="logo.png">
            <h1>One Step Ahead</h1>
        </div>

        <div class="user-info">

    <img
    src="<?php echo !empty($user['avatar']) ? $user['avatar'] : 'avatar1.jpg'; ?>"
    class="profile-pic">

    <span>
        <?php echo htmlspecialchars($user['username']); ?>
    </span>

    <a href="logout.php" class="logout-btn">Logout</a>

</div>
    </div>

    <div class="welcome-card">
        <h2>Welcome back, Instructor <?php echo htmlspecialchars($user['username']); ?>! 👋</h2>

        <p>
            You are teaching
            <strong>
                <?php echo $subjectTaught ? htmlspecialchars($subjectTaught) : 'No subject assigned'; ?>
            </strong>
        </p>

        <?php if($subjectTaught) { ?>
            <div class="subject-badge">
                <i class="fas fa-chalkboard-teacher"></i>
                Your Subject: <?php echo htmlspecialchars($subjectTaught); ?>
            </div>
        <?php } ?>
    </div>

    <?php if($subjectTaught) { ?>

        <div class="stats-grid">

            <div class="stat-card">
                <i class="fas fa-question-circle"></i>
                <h3><?php echo $totalQuestions; ?></h3>
                <p>Total Questions</p>
            </div>

            <div class="stat-card">
                <i class="fas fa-users"></i>
                <h3><?php echo $totalStudents; ?></h3>
                <p>Total Students</p>
            </div>

            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <h3><?php echo $easyCount + $mediumCount + $hardCount; ?></h3>
                <p>Questions Created</p>
            </div>

        </div>

        <div class="subject-card">

            <?php if(strtolower($subjectTaught) == 'math' || strtolower($subjectTaught) == 'mathematics') { ?>
                <i class="fas fa-calculator"></i>
            <?php } else { ?>
                <i class="fas fa-flask"></i>
            <?php } ?>

            <h2><?php echo htmlspecialchars($subjectTaught); ?></h2>

            <p>
                Manage worksheet questions and learning content for
                <?php echo htmlspecialchars($subjectTaught); ?>.
            </p>

            <a href="manage_questions.php?subject=<?php echo urlencode($subjectTaught); ?>" class="btn">
                Manage Questions
            </a>

            <a href="add_question.php?subject=<?php echo urlencode($subjectTaught); ?>" class="btn btn-outline">
                Add New Question
            </a>

            <a href="view_students.php" class="btn btn-outline">
                View Students
            </a>

        </div>

        <div class="recent-section">

            <h3 class="section-title">Recent Questions</h3>

            <?php if($recentResult && $recentResult->num_rows > 0) { ?>

                <?php while($row = $recentResult->fetch_assoc()) { ?>

                    <div class="activity-item">
                        <i class="fas fa-plus-circle" style="color:#4A90E2;"></i>

                        Added:
                        "<?php echo htmlspecialchars(substr($row['question_text'], 0, 50)); ?>..."

                        <small style="margin-left:auto;color:#999;">
                            <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                        </small>
                    </div>

                <?php } ?>

            <?php } else { ?>

                <p style="text-align:center;color:#999;">
                    No recent activity yet. Add your first question!
                </p>

            <?php } ?>

        </div>

        <div class="level-section">

            <h3 class="section-title">Question Distribution</h3>

            <div class="level-bar">
                <div class="level-label">
                    <span>🟢 Easy (Standard 1-2)</span>
                    <span><?php echo $easyCount; ?> questions</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill-easy"></div>
                </div>
            </div>

            <div class="level-bar">
                <div class="level-label">
                    <span>🟡 Medium (Standard 3-4)</span>
                    <span><?php echo $mediumCount; ?> questions</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill-medium"></div>
                </div>
            </div>

            <div class="level-bar">
                <div class="level-label">
                    <span>🔴 Hard (Standard 5-6)</span>
                    <span><?php echo $hardCount; ?> questions</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-fill-hard"></div>
                </div>
            </div>

        </div>

        <div class="tips-card">
            <h3>💡 Quick Tips</h3>
            <p>📝 Add questions regularly to keep students engaged.</p>
            <p>📊 Review student progress to identify difficult topics.</p>
            <p>🎯 Create questions based on the selected standard.</p>
        </div>

    <?php } else { ?>

        <div class="warning-box">
            <i class="fas fa-exclamation-triangle"></i>
            <h3>No Subject Assigned Yet</h3>
            <p>Please contact admin to assign Math or Science for this instructor.</p>
        </div>

    <?php } ?>

    <div class="footer">
        <p>© 2026 One Step Ahead | Instructor Dashboard</p>
    </div>

</div>

</body>
</html>