<?php

session_start();

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

include "db.php";

$userCode = $_SESSION['user_code'];
$subSql =
"SELECT *
FROM subscriptions
WHERE user_code='$userCode'
AND payment_status='Paid'
ORDER BY subscription_id DESC
LIMIT 1";

$subResult = $conn->query($subSql);

$planName = "Basic Plan";

if($subResult->num_rows > 0)
{
    $subscription = $subResult->fetch_assoc();
    $planName = $subscription['plan_name'];
}

$canAccessVideo = false;
$canAccessLeaderboard = false;
$canAccessCertificate = false;

if($planName == "Standard Plan")
{
    $canAccessVideo = true;
}
else if($planName == "Premium Plan")
{
    $canAccessVideo = true;
    $canAccessLeaderboard = true;
    $canAccessCertificate = true;
}

$userSql = "SELECT * FROM users WHERE user_code='$userCode'";
$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

$progressSql =
"SELECT 
SUM(total_points) AS total_points,
SUM(total_attempt) AS total_attempt,
SUM(total_correct) AS total_correct
FROM student_progress
WHERE user_code='$userCode'";

$progressResult = $conn->query($progressSql);
$progress = $progressResult->fetch_assoc();

$points = $progress['total_points'] ?? 0;
$attempt = $progress['total_attempt'] ?? 0;
$correct = $progress['total_correct'] ?? 0;

if($points == NULL) { $points = 0; }
if($attempt == NULL) { $attempt = 0; }
if($correct == NULL) { $correct = 0; }

$wrong = $attempt - $correct;

if($attempt > 0)
{
    $accuracy = round(($correct / $attempt) * 100);
}
else
{
    $accuracy = 0;
}

$avatar = "avatar.jpg";

if(isset($user['avatar']) && $user['avatar'] != "")
{
    $avatar = $user['avatar'];
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Mission Report</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    width: 260px;
    min-height: 100vh;
    background: linear-gradient(180deg, #6c63ff, #ff8ed4);
    color: white;
    padding: 25px 18px;
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
    margin-bottom: 35px;
    position: relative;
    z-index: 2;
}

.logoBox img
{
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: white;
    padding: 5px;
    object-fit: contain;
}

.logoBox h2
{
    font-size: 22px;
}

.logoBox p
{
    font-size: 13px;
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
    font-weight: bold;
    transition: 0.3s;
    position: relative;
    z-index: 2;
}

.nav a:hover,
.active
{
    background: linear-gradient(135deg, #52e5d8, #ffd93d);
    color: #3b0764;
    transform: translateX(5px);
}

.quoteBox
{
    margin-top: 40px;
    background: rgba(255,255,255,0.20);
    padding: 20px;
    border-radius: 25px;
    text-align: center;
    line-height: 1.6;
    border: 2px dashed rgba(255,255,255,0.55);
    position: relative;
    z-index: 2;
}

/* MAIN */

.main
{
    margin-left: 260px;
    width: calc(100% - 260px);
    padding: 30px 45px;
}

.topbar
{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.searchBox
{
    width: 450px;
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
    padding: 10px 15px;
    border-radius: 25px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
}

.profileMini img
{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

/* HERO */

.hero
{
    background:
    linear-gradient(135deg, #fff9d8, #ffeaf7, #e5fbff);
    padding: 40px;
    border-radius: 38px;
    margin-bottom: 25px;
    box-shadow: 0 12px 25px rgba(108,99,255,0.12);
    border: 3px dashed #ffd93d;
    position: relative;
    overflow: hidden;
}

.hero::before
{
    content: "📊";
    position: absolute;
    top: 28px;
    right: 42px;
    font-size: 58px;
}

.hero::after
{
    content: "⭐ 🪐 🚀";
    position: absolute;
    bottom: 22px;
    right: 35px;
    font-size: 22px;
}

.hero h1
{
    color: #3b0764;
    font-size: 42px;
}

.hero p
{
    color: #344062;
    font-size: 19px;
    margin-top: 10px;
    line-height: 1.6;
}

/* CARDS */

.stats
{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 22px;
    margin-bottom: 25px;
}

.card
{
    padding: 25px;
    border-radius: 30px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
    transition: 0.3s;
}

.card:hover
{
    transform: translateY(-8px) scale(1.03);
}

.stats .card:nth-child(1)
{
    background: #fff7cc;
}

.stats .card:nth-child(2)
{
    background: #e0f7ff;
}

.stats .card:nth-child(3)
{
    background: #e8ffe8;
}

.stats .card:nth-child(4)
{
    background: #ffeaf7;
}

.card h3
{
    color: #555;
    margin-bottom: 10px;
    font-size: 16px;
}

.card h1
{
    color: #6c63ff;
    font-size: 38px;
}

.cardIcon
{
    font-size: 36px;
    margin-bottom: 10px;
}

/* CHARTS */

.chartGrid
{
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 25px;
}

.chartBox
{
    background: white;
    padding: 30px;
    border-radius: 35px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
}

.chartBox h2
{
    color: #3b0764;
    margin-bottom: 20px;
}

canvas
{
    max-height: 320px;
}

@media(max-width:900px)
{
    .stats,
    .chartGrid
    {
        grid-template-columns: 1fr;
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
        <a href="home.php">🏠 Home</a>
        <a href="worksheet.php">📝 Worksheet</a>
        <?php if($canAccessVideo) { ?>
    <a href="video.php">🎥 Video Lessons</a>
<?php } else { ?>
    <a href="#" onclick="alert('Video Lessons are only available for Standard and Premium Plan'); return false;">🔒 Video Lessons</a>
<?php } ?>
        <a href="progress.php" class="active">📊 Progress</a>
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
        📊<br><br>
        Track your stars,<br>
        grow your skills,<br>
        and keep exploring! ✨
    </div>

</div>

<div class="main">

    <div class="topbar">

        <form method="GET" action="progress.php">
    <input
    type="text"
    name="search"
    class="searchBox"
    placeholder="🔍 Search progress, accuracy, correct, wrong..."
    value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
</form>

        <a href="profile.php" style="text-decoration:none;color:inherit;">
            <div class="profileMini">
                <img src="<?php echo $avatar; ?>">

                <div>
                    <b><?php echo $user['username']; ?> ⭐</b><br>
                    <small><?php echo $user['standard']; ?> Explorer</small>
                </div>
            </div>
        </a>

    </div>
	<?php
if(isset($_GET['search']) && $_GET['search'] != "")
{
    $search = strtolower($_GET['search']);

    echo "<div class='chartBox' style='margin-bottom:25px;'>";

    echo "<h2>🔍 Search Result</h2>";

    if(strpos($search, "star") !== false || strpos($search, "point") !== false)
    {
        echo "<p>Your total stars collected is <b>$points</b>.</p>";
    }
    else if(strpos($search, "attempt") !== false || strpos($search, "mission") !== false)
    {
        echo "<p>Your total missions tried is <b>$attempt</b>.</p>";
    }
    else if(strpos($search, "correct") !== false)
    {
        echo "<p>Your total correct answers is <b>$correct</b>.</p>";
    }
    else if(strpos($search, "wrong") !== false)
    {
        echo "<p>Your total wrong answers is <b>$wrong</b>.</p>";
    }
    else if(strpos($search, "accuracy") !== false)
    {
        echo "<p>Your current accuracy is <b>$accuracy%</b>.</p>";
    }
    else
    {
        echo "<p>No matching progress result found. Try searching: stars, attempts, correct, wrong, or accuracy.</p>";
    }

    echo "</div>";
}
?>

    <div class="hero">

        <h1>📊 Mission Report</h1>

        <p>
            Track your space learning journey, collect stars, improve your accuracy,
            and become a Galaxy Learning Champion!
        </p>

    </div>

    <div class="stats">

        <div class="card">
            <div class="cardIcon">⭐</div>
            <h3>Stars Collected</h3>
            <h1><?php echo $points; ?></h1>
        </div>

        <div class="card">
            <div class="cardIcon">📚</div>
            <h3>Missions Tried</h3>
            <h1><?php echo $attempt; ?></h1>
        </div>

        <div class="card">
            <div class="cardIcon">🌈</div>
            <h3>Correct Answers</h3>
            <h1><?php echo $correct; ?></h1>
        </div>

        <div class="card">
            <div class="cardIcon">🚀</div>
            <h3>Accuracy</h3>
            <h1><?php echo $accuracy; ?>%</h1>
        </div>

    </div>

    <div class="chartGrid">

        <div class="chartBox">
            <h2>📊 Performance Overview</h2>
            <canvas id="barChart"></canvas>
        </div>

        <div class="chartBox">
            <h2>🎯 Answer Accuracy</h2>
            <canvas id="doughnutChart"></canvas>
        </div>

    </div>

</div>

<script>

const barChart =
document.getElementById("barChart");

new Chart(barChart,
{
    type: "bar",
    data:
    {
        labels:
        [
            "Attempted",
            "Correct",
            "Wrong",
            "Accuracy %"
        ],

        datasets:
        [
            {
                label: "Progress",
                data:
                [
                    <?php echo $attempt; ?>,
                    <?php echo $correct; ?>,
                    <?php echo $wrong; ?>,
                    <?php echo $accuracy; ?>
                ],
                backgroundColor:
                [
                    "#6c63ff",
                    "#52e5d8",
                    "#ff8ed4",
                    "#ffd93d"
                ],
                borderRadius: 12
            }
        ]
    },
    options:
    {
        responsive: true,
        plugins:
        {
            legend:
            {
                display: false
            }
        },
        scales:
        {
            y:
            {
                beginAtZero: true
            }
        }
    }
});

const doughnutChart =
document.getElementById("doughnutChart");

new Chart(doughnutChart,
{
    type: "doughnut",
    data:
    {
        labels:
        [
            "Correct",
            "Wrong"
        ],

        datasets:
        [
            {
                data:
                [
                    <?php echo $correct; ?>,
                    <?php echo $wrong; ?>
                ],
                backgroundColor:
                [
                    "#52e5d8",
                    "#ff8ed4"
                ],
                borderWidth: 0
            }
        ]
    },
    options:
    {
        responsive: true,
        plugins:
        {
            legend:
            {
                position: "bottom"
            }
        }
    }
});

</script>

</body>
</html>