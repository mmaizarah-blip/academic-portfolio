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
$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$getStudent =
"SELECT *
FROM users
WHERE user_code='$userCode'";

$studentResult = $conn->query($getStudent);
$student = $studentResult->fetch_assoc();

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
    $selectedStandard = $student['standard'];
}

$mathSql =
"SELECT COUNT(*) AS total
FROM worksheet_questions
WHERE standard='$selectedStandard'
AND subject='Math'";

$mathResult = $conn->query($mathSql);
$mathRow = $mathResult->fetch_assoc();
$mathTotal = $mathRow['total'];

$scienceSql =
"SELECT COUNT(*) AS total
FROM worksheet_questions
WHERE standard='$selectedStandard'
AND subject='Science'";

$scienceResult = $conn->query($scienceSql);
$scienceRow = $scienceResult->fetch_assoc();
$scienceTotal = $scienceRow['total'];

?>

<!DOCTYPE html>
<html>
<head>

<title>Worksheet Mission</title>

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
    margin-left: 260px;
    width: calc(100% - 260px);
    padding: 30px 45px;
}

.topbar
{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
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
    margin-bottom: 30px;
    box-shadow: 0 12px 25px rgba(108,99,255,0.12);
    border: 3px dashed #ffd93d;
    position: relative;
    overflow: hidden;
}

.hero::before
{
    content: "🚀";
    position: absolute;
    top: 28px;
    right: 42px;
    font-size: 58px;
}

.hero::after
{
    content: "⭐ 🪐 🛸";
    position: absolute;
    bottom: 22px;
    right: 35px;
    font-size: 22px;
}

.hero h1
{
    color: #3b0764;
    font-size: 42px;
    margin-bottom: 10px;
}

.hero p
{
    font-size: 20px;
    color: #344062;
    line-height: 1.6;
    max-width: 850px;
}

.hero b
{
    color: #6c63ff;
}

.title
{
    text-align: center;
    color: #3b0764;
    margin-bottom: 25px;
    font-size: 28px;
}

/* STANDARD */

.standardGrid
{
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
    margin-bottom: 35px;
}

.standardCard
{
    background: white;
    padding: 20px;
    border-radius: 30px;
    text-align: center;
    text-decoration: none;
    color: #3b0764;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
    transition: 0.3s;
}

.standardCard:nth-child(1)
{
    background: #fff7cc;
}

.standardCard:nth-child(2)
{
    background: #e0f7ff;
}

.standardCard:nth-child(3)
{
    background: #ffeaf7;
}

.standardCard:nth-child(4)
{
    background: #e8ffe8;
}

.standardCard:nth-child(5)
{
    background: #f0e6ff;
}

.standardCard:nth-child(6)
{
    background: #fff0d8;
}

.standardCard:hover
{
    transform: translateY(-8px) scale(1.04);
    border-color: #52e5d8;
}

.activeStandard
{
    border-color: #6c63ff !important;
    background: #eef0ff !important;
    box-shadow: 0 0 18px rgba(108,99,255,0.25);
}

.standardCard h1
{
    color: #6c63ff;
    font-size: 34px;
}

.standardCard p
{
    font-size: 14px;
    margin-top: 5px;
    font-weight: bold;
}

/* SUBJECT */

.subjectArea
{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.subjectCard
{
    background: white;
    padding: 35px;
    border-radius: 35px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.08);
    text-align: center;
    border: 3px solid transparent;
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
    font-size: 30px;
    margin-bottom: 12px;
    color: #3b0764;
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
    top: 22px;
    font-size: 38px;
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
    top: 22px;
    font-size: 38px;
}

.subjectCard p
{
    color: #555;
    margin-bottom: 20px;
    line-height: 1.6;
}

.subjectCard a
{
    display: inline-block;
    padding: 13px 24px;
    border-radius: 25px;
    color: white;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 8px 18px rgba(0,0,0,0.12);
}

.mathBtn
{
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
}

.scienceBtn
{
    background: linear-gradient(135deg, #52e5d8, #34d399);
}

/* INFO */

.infoPanel
{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.panel
{
    background: white;
    padding: 25px;
    border-radius: 30px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #e0f7ff;
}

.panel h2
{
    color: #3b0764;
    margin-bottom: 12px;
}

.panel p
{
    color: #555;
    line-height: 1.6;
}

@media(max-width:900px)
{
    .standardGrid,
    .subjectArea,
    .infoPanel
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
        <a href="worksheet.php" class="active">📝 Worksheet</a>
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
    Every question solved is<br>
    one step closer to the stars! ⭐
    <br><br>
    Keep exploring! ✨
</div>

</div>

<div class="main">

    <div class="topbar">

        <form method="GET" action="worksheet.php">
    <input
    type="text"
    name="search"
    class="searchBox"
    placeholder="🔍 Search missions, worksheets, topics..."
    value="<?php echo $search; ?>">
</form>

        <a href="profile.php" style="text-decoration:none;color:inherit;">

            <div class="profileMini">

                <img src="<?php echo empty($student['avatar']) ? 'avatar.jpg' : $student['avatar']; ?>">

                <div>
                    Hello, <b><?php echo $student['username']; ?> ⭐</b>
                    <br>
                    <?php echo $selectedStandard; ?> Explorer
                </div>

            </div>

        </a>

    </div>
	<?php if($search != "") { ?>

<div class="panel" style="margin-bottom:25px;">

    <h2>🔍 Search Results for "<?php echo $search; ?>"</h2>

    <?php
    $searchSql =
    "SELECT *
    FROM worksheet_questions
    WHERE question_text LIKE '%$search%'
    OR subject LIKE '%$search%'
    OR standard LIKE '%$search%'
    OR explanation LIKE '%$search%'";

    $searchResult = $conn->query($searchSql);

    if($searchResult->num_rows > 0)
    {
        while($row = $searchResult->fetch_assoc())
        {
            echo "<p><b>".$row['question_text']."</b><br>";
            echo $row['standard']." | ".$row['subject']."<br>";

            if($row['subject'] == "Math" || $row['subject'] == "Mathematics")
            {
                echo "<a href='mathQuiz.php?standard=".$row['standard']."' class='mathBtn'>Open Math Mission</a>";
            }
            else
            {
                echo "<a href='scienceQuiz.php?standard=".$row['standard']."' class='scienceBtn'>Open Science Lab</a>";
            }

            echo "</p><br>";
        }
    }
    else
    {
        echo "<p>No worksheet found.</p>";
    }
    ?>

</div>

<?php } ?>

    <div class="hero">

        <h1>🚀 Worksheet Mission</h1>

        <p>
            Choose your standard galaxy and launch into Math or Science missions.
            Answer questions, collect stars, and become a Galaxy Learning Champion!
        </p>

        <br>

        <b>Selected Galaxy:</b>
        <?php echo $selectedStandard; ?>

    </div>

    <h2 class="title">🪐 Choose Your Standard Galaxy</h2>

    <div class="standardGrid">

        <?php
        for($i = 1; $i <= 6; $i++)
        {
            $std = "Standard " . $i;
        ?>

        <a
        href="worksheet.php?standard=<?php echo $std; ?>"
        class="standardCard <?php if($selectedStandard == $std){ echo 'activeStandard'; } ?>">

            <h1>🪐 <?php echo $i; ?></h1>
            <p><?php echo $std; ?></p>

        </a>

        <?php
        }
        ?>

    </div>

    <h2 class="title">🚀 Choose Your Mission</h2>

    <div class="subjectArea">

        <div class="subjectCard math">
            <h2>🧮 Math Galaxy Mission</h2>

            <p>
                🚀 Numbers • ⭐ Operations • 🪐 Problem Solving
                <br><br>
                <?php echo $mathTotal; ?> questions available for <?php echo $selectedStandard; ?>.
            </p>

            <a
            href="mathQuiz.php?standard=<?php echo $selectedStandard; ?>"
            class="mathBtn">

                Launch Math Mission →

            </a>
        </div>

        <div class="subjectCard science">
            <h2>🔬 Science Space Lab</h2>

            <p>
                🛸 Living Things • 🧪 Experiments • 🌎 Earth Science
                <br><br>
                <?php echo $scienceTotal; ?> questions available for <?php echo $selectedStandard; ?>.
            </p>

            <a
            href="scienceQuiz.php?standard=<?php echo $selectedStandard; ?>"
            class="scienceBtn">

                Enter Science Lab →

            </a>
        </div>

    </div>

    <div class="infoPanel">

        <div class="panel">
            <h2>💡 Mission Info</h2>
            <p>
                Questions are loaded from the database based on your selected standard and subject.
                Try your best and collect learning stars!
            </p>
        </div>

        <div class="panel">
            <h2>🌈 Available Missions</h2>
            <p>
                Mathematics and Science missions are available for Standard 1 to Standard 6.
                Choose your galaxy and start exploring!
            </p>
        </div>

    </div>

</div>

</body>
</html>