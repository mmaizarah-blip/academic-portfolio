<?php

session_start();

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

include "db.php";

$userCode = $_SESSION['user_code'];
$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}
$subSql =
"SELECT *
FROM subscriptions
WHERE user_code='$userCode'
AND payment_status='Paid'
ORDER BY subscription_id DESC
LIMIT 1";

$subResult = $conn->query($subSql);

if($subResult->num_rows == 0)
{
    header("Location: subscription.php");
    exit();
}

$subscription = $subResult->fetch_assoc();
$planName = $subscription['plan_name'];

$canAccessVideo = false;
$canAccessCertificate = false;
$canAccessLeaderboard = false;

if($planName == "Standard Plan")
{
    $canAccessVideo = true;
}
else if($planName == "Premium Plan")
{
    $canAccessVideo = true;
    $canAccessCertificate = true;
    $canAccessLeaderboard = true;
}

$userSql =
"SELECT *
FROM users
WHERE user_code='$userCode'";

$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

if(isset($_GET['standard']))
{
    $_SESSION['selected_standard'] = $_GET['standard'];
}

if(isset($_SESSION['selected_standard']))
{
    $selectedStandard = $_SESSION['selected_standard'];
}
else
{
    $selectedStandard = $user['standard'];
}

$avatar = "avatar.jpg";

if(isset($user['avatar']) && $user['avatar'] != "")
{
    $avatar = $user['avatar'];
}

$progressSql =
"SELECT *
FROM student_progress
WHERE user_code='$userCode'";

$progressResult = $conn->query($progressSql);

if($progressResult->num_rows > 0)
{
    $progress = $progressResult->fetch_assoc();

    $points = $progress['total_points'];
    $attempt = $progress['total_attempt'];
    $correct = $progress['total_correct'];
}
else
{
    $points = 0;
    $attempt = 0;
    $correct = 0;
}

if($attempt > 0)
{
    $accuracy = round(($correct / $attempt) * 100);
}
else
{
    $accuracy = 0;
}

$mathSql =
"SELECT COUNT(*) AS total
FROM worksheet_questions
WHERE standard='".$selectedStandard."'
AND subject='Math'";

$mathResult = $conn->query($mathSql);
$mathRow = $mathResult->fetch_assoc();
$mathTotal = $mathRow['total'];

$scienceSql =
"SELECT COUNT(*) AS total
FROM worksheet_questions
WHERE standard='".$selectedStandard."'
AND subject='Science'";

$scienceResult = $conn->query($scienceSql);
$scienceRow = $scienceResult->fetch_assoc();
$scienceTotal = $scienceRow['total'];

$leaderSql =
"SELECT users.username, student_progress.total_points
FROM users
INNER JOIN student_progress
ON users.user_code = student_progress.user_code
WHERE users.role='Student'
ORDER BY student_progress.total_points DESC
LIMIT 3";

$leaderResult = $conn->query($leaderSql);

?>

<!DOCTYPE html>
<html>
<head>

<title>OneStepAhead Sdn. Bhd.</title>

<style>

*
{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body
{
    font-family: 'Comic Sans MS', Arial, sans-serif;
    background:
    radial-gradient(circle at 8% 10%, #ffeaf7 0, transparent 28%),
    radial-gradient(circle at 90% 8%, #dffaff 0, transparent 30%),
    radial-gradient(circle at 50% 100%, #f1e7ff 0, transparent 35%),
    linear-gradient(180deg, #fbfdff, #eef8ff);
    display: flex;
    color: #26345f;
}

/* SIDEBAR */

.sidebar
{
    width: 270px;
    min-height: 100vh;
    background: linear-gradient(180deg, #6c63ff, #ff8ed4);
    color: white;
    padding: 25px 20px;
    position: fixed;
    overflow: hidden;
}

.sidebar::before
{
    content: "🪐";
    position: absolute;
    top: 95px;
    right: 18px;
    font-size: 34px;
}

.sidebar::after
{
    content: "🚀";
    position: absolute;
    bottom: 20px;
    left: 25px;
    font-size: 42px;
}

.logoBox
{
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 40px;
    position: relative;
    z-index: 2;
}

.logoBox img
{
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: white;
    padding: 6px;
    object-fit: contain;
}

.logoBox h2
{
    font-size: 21px;
}

.logoBox p
{
    font-size: 12px;
    color: #f7edff;
}

.nav a
{
    display: block;
    color: white;
    text-decoration: none;
    padding: 14px 18px;
    margin-bottom: 10px;
    border-radius: 25px;
    font-size: 16px;
    transition: 0.3s;
    font-weight: bold;
    position: relative;
    z-index: 2;
}

.nav a:hover,
.nav .active
{
    background: linear-gradient(135deg, #52e5d8, #ffd93d);
    color: #3b0764;
    transform: translateX(5px);
}

.quoteBox
{
    margin-top: 35px;
    background: rgba(255,255,255,0.20);
    padding: 22px;
    border-radius: 25px;
    text-align: center;
    line-height: 1.6;
    font-size: 14px;
    border: 2px dashed rgba(255,255,255,0.55);
    position: relative;
    z-index: 2;
}

/* MAIN */

.main
{
    margin-left: 270px;
    width: calc(100% - 270px);
    padding: 30px 45px;
}

/* TOPBAR */

.topbar
{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.searchBox
{
    width: 480px;
    padding: 15px 25px;
    border-radius: 35px;
    border: 2px solid #d8f5ff;
    font-size: 15px;
    outline: none;
    box-shadow: 0 8px 20px rgba(82,229,216,0.15);
}

.profileMini
{
    display: flex;
    align-items: center;
    gap: 12px;
    background: white;
    padding: 10px 16px;
    border-radius: 25px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
}

.profileMini img
{
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
}

/* INSTRUCTION */

.instructionBox
{
    background:
    linear-gradient(135deg, #eefbff, #fff2fb, #fff8d8);
    padding: 35px;
    border-radius: 35px;
    margin-bottom: 30px;
    box-shadow: 0 12px 25px rgba(108,99,255,0.12);
    position: relative;
    overflow: hidden;
    border: 3px solid white;
}

.instructionBox::before
{
    content: "👩‍🚀";
    position: absolute;
    top: 25px;
    right: 42px;
    font-size: 62px;
}

.instructionBox::after
{
    content: "⭐ 🪐 🚀";
    position: absolute;
    bottom: 20px;
    right: 38px;
    font-size: 22px;
}

.instructionBox h1
{
    color: #3b0764;
    font-size: 42px;
    margin-bottom: 12px;
}

.instructionBox p
{
    color: #344062;
    font-size: 18px;
    line-height: 1.7;
    max-width: 900px;
}

.guideGrid
{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-top: 25px;
    margin-bottom: 25px;
}

.guideCard
{
    background: white;
    padding: 22px;
    border-radius: 28px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.06);
    font-size: 34px;
    transition: 0.3s;
    border: 2px solid #e0f7ff;
}

.guideCard:hover
{
    transform: translateY(-8px) rotate(-1deg);
}

.guideCard h3
{
    color: #3b0764;
    font-size: 18px;
    margin-top: 10px;
    margin-bottom: 8px;
}

.guideCard p
{
    font-size: 14px;
    color: #555;
}

.instructionBox button,
.hero button,
.btn
{
    padding: 13px 24px;
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-size: 16px;
    margin-right: 10px;
    font-weight: bold;
    box-shadow: 0 8px 18px rgba(108,99,255,0.25);
    text-decoration: none;
    display: inline-block;
}

.skipBtn
{
    background: white !important;
    color: #6c63ff !important;
    border: 2px solid #6c63ff !important;
}

/* HERO */

.hero
{
    background:
    linear-gradient(135deg, #fff9d8, #ffeaf7, #e5fbff);
    padding: 40px;
    border-radius: 38px;
    margin-bottom: 28px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 12px 25px rgba(108,99,255,0.12);
    border: 3px dashed #ffd93d;
    position: relative;
    overflow: hidden;
}

.hero::before
{
    content: "⭐";
    position: absolute;
    top: 22px;
    left: 24px;
    font-size: 30px;
}

.hero h1
{
    color: #3b0764;
    font-size: 44px;
    margin-bottom: 12px;
    padding-left: 25px;
}

.hero p
{
    color: #344062;
    font-size: 20px;
    line-height: 1.5;
    padding-left: 25px;
}

.heroImg
{
    width: 310px;
    height: 230px;
    object-fit: contain;
    animation: floatImg 3s ease-in-out infinite;
}

@keyframes floatImg
{
    0%
    {
        transform: translateY(0);
    }

    50%
    {
        transform: translateY(-12px);
    }

    100%
    {
        transform: translateY(0);
    }
}

/* SECTION */

.sectionTitle
{
    color: #3b0764;
    margin-bottom: 16px;
    font-size: 26px;
}

/* STATS */

.stats
{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
    margin-bottom: 28px;
}

.statCard
{
    background: white;
    padding: 25px;
    border-radius: 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #e0f7ff;
    transition: 0.3s;
}

.statCard:hover
{
    transform: translateY(-8px);
}

.stats .statCard:nth-child(1)
{
    background: #fff7cc;
}

.stats .statCard:nth-child(2)
{
    background: #e0f7ff;
}

.stats .statCard:nth-child(3)
{
    background: #e8ffe8;
}

.stats .statCard:nth-child(4)
{
    background: #ffe5f3;
}

.statIcon
{
    font-size: 34px;
    margin-bottom: 12px;
}

.statCard h3
{
    color: #667;
    font-size: 15px;
    margin-bottom: 8px;
}

.statCard h1
{
    color: #6c63ff;
    font-size: 36px;
}

/* STANDARD */

.standardGrid
{
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
    margin-bottom: 28px;
}

.standardCard
{
    background: white;
    padding: 18px;
    border-radius: 28px;
    text-align: center;
    text-decoration: none;
    color: #3b0764;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
    transition: 0.3s;
}

.standardCard:hover
{
    transform: translateY(-8px) scale(1.04);
    border-color: #52e5d8;
}

.activeStandard
{
    border-color: #6c63ff;
    background: #eef0ff;
}

.standardCard h1
{
    font-size: 34px;
    color: #6c63ff;
}

.standardCard p
{
    font-size: 14px;
    margin-top: 5px;
}

/* SUBJECTS */

.subjects
{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 28px;
}

.subjectCard
{
    background: white;
    padding: 30px;
    border-radius: 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    transition: 0.3s;
    position: relative;
    overflow: hidden;
}

.subjectCard:hover
{
    transform: translateY(-8px);
}

.subjectCard h2
{
    margin-bottom: 12px;
    color: #3b0764;
}

.subjectCard p
{
    color: #555;
    margin-bottom: 20px;
    line-height: 1.5;
}

.subjectCard a
{
    display: inline-block;
    padding: 12px 20px;
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
}

.math
{
    border-top: 8px solid #6c63ff;
}

.math::after
{
    content: "🚀";
    position: absolute;
    right: 28px;
    top: 25px;
    font-size: 35px;
}

.math a
{
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
}

.science
{
    border-top: 8px solid #52e5d8;
}

.science::after
{
    content: "🛸";
    position: absolute;
    right: 28px;
    top: 25px;
    font-size: 35px;
}

.science a
{
    background: linear-gradient(135deg, #52e5d8, #34d399);
}

/* QUICK ACCESS */

.quick
{
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 18px;
    margin-bottom: 28px;
}

.quickCard
{
    background: white;
    padding: 22px 15px;
    border-radius: 30px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    transition: 0.3s;
    border: 2px solid #e0f7ff;
}

.quickCard:hover
{
    transform: translateY(-10px) scale(1.05);
}

.quickCard a
{
    text-decoration: none;
    color: inherit;
}

.quickCard img
{
    width: 58px;
    height: 58px;
    object-fit: contain;
    margin-bottom: 12px;
    border-radius: 50%;
}

.quickCard h3
{
    color: #3b0764;
    font-size: 16px;
}

/* BOTTOM */

.bottom
{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.panel
{
    background: white;
    padding: 28px;
    border-radius: 30px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #e0f7ff;
}

.panel h2
{
    color: #3b0764;
    margin-bottom: 18px;
}

.progressBar
{
    width: 100%;
    background: #eef0ff;
    border-radius: 25px;
    margin-top: 15px;
}

.progressFill
{
    width: <?php echo $accuracy; ?>%;
    background: linear-gradient(90deg, #52e5d8, #6c63ff);
    color: white;
    padding: 12px 0;
    border-radius: 25px;
    text-align: center;
    font-weight: bold;
}

.rankRow
{
    display: flex;
    justify-content: space-between;
    padding: 13px 0;
    border-bottom: 1px solid #e0f7ff;
}

.rankRow b
{
    color: #3b0764;
}

@media(max-width: 900px)
{
    .stats,
    .subjects,
    .quick,
    .bottom,
    .guideGrid,
    .standardGrid
    {
        grid-template-columns: 1fr;
    }

    .hero
    {
        flex-direction: column;
        text-align: center;
    }

    .searchBox
    {
        width: 280px;
    }
}

</style>

</head>

<body>

<div class="sidebar">

    <div class="logoBox">
        <img src="Logo.png">

        <div>
            <h2>OneStepAhead</h2>
            <p>Learn • Practice • Achieve</p>
        </div>
    </div>

    <div class="nav">
	<a href="home.php" class="active">🏠 Home</a>
	<a href="worksheet.php">📝 Worksheet</a>
<?php if($canAccessVideo) { ?>
    <a href="video.php">🎥 Video Lessons</a>
<?php } else { ?>
    <a href="#" onclick="alert('Video Lessons are only available for Standard and Premium Plan'); return false;">🔒 Video Lessons</a>
<?php } ?>

<a href="progress.php">📊 Progress</a>

<?php if($canAccessLeaderboard) { ?>
    <a href="leaderboard.php">🏆 Leaderboard</a>
<?php } else { ?>
    <a href="#" onclick="alert('Leaderboard is only available for Premium Plan'); return false;">🔒 Leaderboard</a>
<?php } ?>

<a href="profile.php">👤 Profile</a>

<?php if($canAccessCertificate) { ?>
    <a href="certificate.php">📜 Certificate</a>
<?php } else { ?>
    <a href="#" onclick="alert('Certificate is only available for Premium Plan'); return false;">🔒 Certificate</a>
<?php } ?>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="quoteBox">
        🚀<br><br>
        Small steps today,<br>
        big stars tomorrow!
        <br><br>
        Keep exploring! ✨
    </div>

</div>

<div class="main">

    <div class="topbar">

        <form method="GET" action="home.php">
    <input
    type="text"
    name="search"
    class="searchBox"
    placeholder="🔍 Search worksheets, videos, topics..."
    value="<?php echo $search; ?>">
</form>

        <a href="profile.php" style="text-decoration:none;color:inherit;">

            <div class="profileMini">

                <img src="<?php echo $avatar; ?>">

                <div>
                    <b><?php echo $user['username']; ?> ⭐</b><br>
                    <small><?php echo $selectedStandard; ?> Explorer</small>
                </div>

            </div>

        </a>

    </div>
	<?php if($search != "") { ?>

<div class="panel" style="margin-bottom:25px;">

    <h2>🔍 Search Results for "<?php echo $search; ?>"</h2>

    <?php
    $searchQuestionSql =
    "SELECT *
    FROM worksheet_questions
    WHERE question_text LIKE '%$search%'
    OR subject LIKE '%$search%'
    OR standard LIKE '%$search%'
    OR explanation LIKE '%$search%'";

    $searchQuestionResult = $conn->query($searchQuestionSql);

    $searchVideoSql =
    "SELECT *
    FROM learning_videos
    WHERE title LIKE '%$search%'
    OR subject LIKE '%$search%'
    OR standard LIKE '%$search%'
    OR chapter LIKE '%$search%'";

    $searchVideoResult = $conn->query($searchVideoSql);
    ?>

    <h3>📝 Worksheets</h3>

    <?php
    if($searchQuestionResult->num_rows > 0)
    {
        while($row = $searchQuestionResult->fetch_assoc())
        {
            echo "<p><b>".$row['question_text']."</b><br>";
            echo $row['standard']." | ".$row['subject']."<br>";

            if($row['subject'] == "Math" || $row['subject'] == "Mathematics")
            {
                echo "<a href='mathQuiz.php?standard=".$row['standard']."' class='btn'>Open Math</a>";
            }
            else
            {
                echo "<a href='scienceQuiz.php?standard=".$row['standard']."' class='btn'>Open Science</a>";
            }

            echo "</p><br>";
        }
    }
    else
    {
        echo "<p>No worksheet found.</p>";
    }
    ?>

    <br>

    <h3>🎥 Videos</h3>

    <?php
    if($searchVideoResult->num_rows > 0)
    {
        while($row = $searchVideoResult->fetch_assoc())
        {
            echo "<p><b>".$row['title']."</b><br>";
            echo $row['standard']." | ".$row['subject']." | Chapter ".$row['chapter']."<br>";
            echo "<a href='video.php?standard=".$row['standard']."&subject=".$row['subject']."&chapter=".$row['chapter']."' class='btn'>Open Video</a>";
            echo "</p><br>";
        }
    }
    else
    {
        echo "<p>No video found.</p>";
    }
    ?>

</div>

<?php } ?>

    <div class="instructionBox">

        <h1>
            Welcome, <?php echo $user['username']; ?>! 👋
        </h1>

        <p>
            Welcome to your Space Learning Academy! Choose your standard, launch into Math and Science missions, collect stars, watch videos, and become a Galaxy Learning Champion.
        </p>

        <div class="guideGrid">

            <div class="guideCard">
                🚀
                <h3>Math Mission</h3>
                <p>Answer Math questions and collect galaxy stars.</p>
            </div>

            <div class="guideCard">
                🛸
                <h3>Science Lab</h3>
                <p>Explore Science experiments and fun quizzes.</p>
            </div>

            <div class="guideCard">
                📊
                <h3>Mission Report</h3>
                <p>Check your score, accuracy and learning graph.</p>
            </div>

            <div class="guideCard">
                🏆
                <h3>Galaxy Rank</h3>
                <p>Climb the leaderboard and shine bright.</p>
            </div>

        </div>

        <button onclick="window.location.href='worksheet.php'">
            🚀 Start Mission
        </button>

        <button
        class="skipBtn"
        onclick="document.getElementById('dashboardSection').scrollIntoView({behavior:'smooth'})">
            Skip Intro
        </button>

    </div>

    <div id="dashboardSection"></div>

    <div class="hero">

        <div>
            <h1>
                Welcome Back,<br>
                <?php echo $user['username']; ?>! 🌈
            </h1>

            <p>
                You are exploring
                <b><?php echo $selectedStandard; ?> Galaxy</b>
                today.
            </p>
			<p style="margin-top:10px;">
    💎 Current Plan:
    <b><?php echo $planName; ?></b>
</p>


            <button onclick="window.location.href='worksheet.php'">
                Launch Learning →
            </button>
        </div>

        <img src="education1.png" class="heroImg">

    </div>

    <h2 class="sectionTitle">⭐ Your Mission Overview</h2>

    <div class="stats">

        <div class="statCard">
            <div class="statIcon">⭐</div>
            <h3>Stars Collected</h3>
            <h1><?php echo $points; ?></h1>
        </div>

        <div class="statCard">
            <div class="statIcon">📚</div>
            <h3>Missions Tried</h3>
            <h1><?php echo $attempt; ?></h1>
        </div>

        <div class="statCard">
            <div class="statIcon">🌈</div>
            <h3>Correct Answers</h3>
            <h1><?php echo $correct; ?></h1>
        </div>

        <div class="statCard">
            <div class="statIcon">🎯</div>
            <h3>Accuracy</h3>
            <h1><?php echo $accuracy; ?>%</h1>
        </div>

    </div>

    <h2 class="sectionTitle">🎒 Choose Your Standard Galaxy</h2>

    <div class="standardGrid">

        <?php
        for($i = 1; $i <= 6; $i++)
        {
            $std = "Standard " . $i;
        ?>

        <a
        href="home.php?standard=<?php echo $std; ?>"
        class="standardCard <?php if($selectedStandard == $std){ echo 'activeStandard'; } ?>">

            <h1>🪐 <?php echo $i; ?></h1>
            <p><?php echo $std; ?></p>

        </a>

        <?php
        }
        ?>

    </div>

    <h2 class="sectionTitle">🚀 Choose Your Mission</h2>

    <div class="subjects">

        <div class="subjectCard math">
            <h2>🧮 Math Galaxy Mission</h2>

            <p>
                <?php echo $mathTotal; ?> questions available for
                <?php echo $selectedStandard; ?>.
            </p>

            <a href="mathQuiz.php?standard=<?php echo $selectedStandard; ?>">
                Launch Math Mission →
            </a>
        </div>

        <div class="subjectCard science">
            <h2>🔬 Science Space Lab</h2>

            <p>
                <?php echo $scienceTotal; ?> questions available for
                <?php echo $selectedStandard; ?>.
            </p>

            <a href="scienceQuiz.php?standard=<?php echo $selectedStandard; ?>">
                Enter Science Lab →
            </a>
        </div>

    </div>

    <h2 class="sectionTitle">🛸 Quick Access</h2>

    <div class="quick">

        <div class="quickCard">
            <a href="profile.php">
                <img src="<?php echo $avatar; ?>">
                <h3>Astronaut Profile</h3>
            </a>
        </div>

        <div class="quickCard">
            <a href="worksheet.php">
                <img src="worksheet.jpg">
                <h3>Worksheet</h3>
            </a>
        </div>

        <div class="quickCard">
    <?php if($canAccessVideo) { ?>
        <a href="video.php">
            <img src="video.jpg">
            <h3>Videos</h3>
        </a>
    <?php } else { ?>
        <a href="#" onclick="alert('Video Lessons are only available for Standard and Premium Plan'); return false;">
            <img src="video.jpg">
            <h3>🔒 Videos</h3>
        </a>
    <?php } ?>
</div>

        <div class="quickCard">
            <a href="progress.php">
                <img src="progress.jpg">
                <h3>Progress</h3>
            </a>
        </div>

       <div class="quickCard">
    <?php if($canAccessLeaderboard) { ?>
        <a href="leaderboard.php">
            <img src="leaderboard.jpg">
            <h3>Leaderboard</h3>
        </a>
    <?php } else { ?>
        <a href="#" onclick="alert('Leaderboard is only available for Premium Plan'); return false;">
            <img src="leaderboard.jpg">
            <h3>🔒 Leaderboard</h3>
        </a>
    <?php } ?>
</div>

    </div>

    <div class="bottom">

        <div class="panel">
            <h2>📊 Mission Report</h2>

            <p><b>Total Stars:</b> <?php echo $points; ?></p>
            <p><b>Missions Attempted:</b> <?php echo $attempt; ?></p>
            <p><b>Correct Answers:</b> <?php echo $correct; ?></p>
            <p><b>Accuracy:</b> <?php echo $accuracy; ?>%</p>

            <div class="progressBar">
                <div class="progressFill">
                    <?php echo $accuracy; ?>%
                </div>
            </div>

            <a href="progress.php" class="btn">
                View Mission Report
            </a>
        </div>

        <div class="panel">
            <h2>🏆 Galaxy Champions</h2>

            <?php

            $rank = 1;

            if($leaderResult->num_rows > 0)
            {
                while($leader = $leaderResult->fetch_assoc())
                {

            ?>

            <div class="rankRow">
                <b>
                    <?php echo $rank; ?>.
                    <?php echo $leader['username']; ?>
                </b>

                <span>
                    <?php echo $leader['total_points']; ?> stars ⭐
                </span>
            </div>

            <?php

                    $rank++;
                }
            }
            else
            {

            ?>

            <p>No leaderboard data yet.</p>

            <?php

            }

            ?>

            <a href="leaderboard.php" class="btn">
                View Galaxy Ranking
            </a>
        </div>

    </div>

</div>

</body>
</html>