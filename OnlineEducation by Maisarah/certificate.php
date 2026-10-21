<?php

session_start();

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

include "db.php";

$userCode = $_SESSION['user_code'];

$userSql =
"SELECT *
FROM users
WHERE user_code='$userCode'";

$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

$progressSql =
"SELECT *
FROM student_progress
WHERE user_code='$userCode'";

$progressResult = $conn->query($progressSql);

$points = 0;

if($progressResult->num_rows > 0)
{
    $progress = $progressResult->fetch_assoc();
    $points = $progress['total_points'];
}

$certificateUnlocked = false;

if($points >= 100)
{
    $certificateUnlocked = true;
}

$progressPercent = min(100, $points);

?>

<!DOCTYPE html>
<html>
<head>

<title>Galaxy Certificate</title>

<style>

*
{
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
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 30px;
    color: #26345f;
}

/* CERTIFICATE */

.certificate
{
    width: 950px;
    background:
    linear-gradient(135deg, #fffef7, #ffeaf7, #eefcff);
    padding: 60px;
    border: 12px dashed #ffd93d;
    border-radius: 38px;
    text-align: center;
    box-shadow: 0px 15px 35px rgba(108,99,255,0.18);
    position: relative;
    overflow: hidden;
}

.certificate::before
{
    content: "⭐ 🪐 🚀";
    position: absolute;
    top: 25px;
    left: 35px;
    font-size: 28px;
}

.certificate::after
{
    content: "🛸 🌈 ⭐";
    position: absolute;
    top: 25px;
    right: 35px;
    font-size: 28px;
}

.cornerPlanet
{
    position: absolute;
    bottom: 25px;
    left: 35px;
    font-size: 45px;
}

.cornerRocket
{
    position: absolute;
    bottom: 25px;
    right: 35px;
    font-size: 45px;
}

.logo
{
    width: 120px;
    margin-bottom: 15px;
    background: white;
    border-radius: 50%;
    padding: 8px;
    border: 4px solid #52e5d8;
    box-shadow: 0 8px 18px rgba(82,229,216,0.25);
}

.title
{
    font-size: 52px;
    color: #6c63ff;
    margin-bottom: 20px;
}

.subtitle
{
    font-size: 22px;
    color: #555;
}

.name
{
    display: inline-block;
    font-size: 44px;
    color: #3b0764;
    margin: 30px 0;
    font-weight: bold;
    background: white;
    padding: 12px 45px;
    border-radius: 35px;
    border: 3px solid #d8f5ff;
    box-shadow: 0 8px 18px rgba(0,0,0,0.08);
}

.desc
{
    font-size: 22px;
    line-height: 1.8;
    color: #333;
}

.points
{
    margin-top: 25px;
    font-size: 30px;
    color: #6c63ff;
    font-weight: bold;
}

.badge
{
    display: inline-block;
    margin-top: 20px;
    background: linear-gradient(135deg, #fff7cc, #ffd93d);
    padding: 15px 35px;
    border-radius: 35px;
    color: #6b5200;
    font-size: 22px;
    font-weight: bold;
    border: 3px solid #ffd93d;
    box-shadow: 0 8px 18px rgba(255,217,61,0.25);
}

.footer
{
    margin-top: 55px;
    display: flex;
    justify-content: space-between;
}

.sign
{
    width: 220px;
    border-top: 3px solid #3b0764;
    padding-top: 10px;
    color: #3b0764;
    font-weight: bold;
}

.printBtn,
.backBtn
{
    margin-top: 30px;
    padding: 14px 30px;
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-weight: bold;
    box-shadow: 0 8px 18px rgba(108,99,255,0.25);
    transition: 0.3s;
}

.printBtn:hover,
.backBtn:hover
{
    transform: translateY(-3px);
}

/* LOCKED CARD */

.lockedCard
{
    width: 750px;
    background:
    linear-gradient(135deg, #fff9d8, #ffeaf7, #e5fbff);
    padding: 55px;
    border-radius: 38px;
    text-align: center;
    box-shadow: 0 12px 25px rgba(108,99,255,0.15);
    border: 3px dashed #ffd93d;
    position: relative;
    overflow: hidden;
}

.lockedCard::before
{
    content: "🔒";
    position: absolute;
    top: 25px;
    left: 35px;
    font-size: 42px;
}

.lockedCard::after
{
    content: "⭐ 🪐 🚀";
    position: absolute;
    right: 30px;
    top: 25px;
    font-size: 25px;
}

.lockedCard h1
{
    color: #6c63ff;
    font-size: 45px;
}

.lockedCard h2
{
    color: #3b0764;
}

.lockedCard p
{
    font-size: 18px;
    color: #555;
}

.progressOuter
{
    height: 28px;
    background: white;
    border-radius: 25px;
    overflow: hidden;
    margin-top: 20px;
    border: 2px solid #d8f5ff;
}

.progressInner
{
    height: 28px;
    width: <?php echo $progressPercent; ?>%;
    background: linear-gradient(90deg, #52e5d8, #6c63ff);
}

@media print
{
    .printBtn,
    .backBtn
    {
        display:none;
    }

    body
    {
        background:white;
        padding:0;
    }

    .certificate
    {
        box-shadow:none;
    }
}

@media(max-width:900px)
{
    .certificate,
    .lockedCard
    {
        width: 95%;
        padding: 35px;
    }

    .title
    {
        font-size: 38px;
    }

    .name
    {
        font-size: 32px;
    }

    .footer
    {
        flex-direction: column;
        align-items: center;
        gap: 35px;
    }
}

</style>

</head>

<body>

<?php if($certificateUnlocked) { ?>

<div class="certificate">

    <div class="cornerPlanet">🪐</div>
    <div class="cornerRocket">🚀</div>

    <img src="Logo.png" class="logo">

    <h1 class="title">
        🌟 Galaxy Learner Certificate 🌟
    </h1>

    <p class="subtitle">
        This certificate is proudly presented to
    </p>

    <div class="name">
        <?php echo $user['username']; ?>
    </div>

    <div class="desc">
        For completing learning missions in
        <b>One Step Ahead Learning Platform</b>
        and showing wonderful effort in
        Mathematics and Science exploration.
    </div>

    <div class="points">
        Total Stars Earned:
        <?php echo $points; ?> ⭐
    </div>

    <div class="badge">
        🏆 Galaxy Learning Champion
    </div>

    <div class="footer">

        <div class="sign">
            Instructor
        </div>

        <div class="sign">
            One Step Ahead
        </div>

    </div>

    <button
    class="printBtn"
    onclick="window.print()">

    🖨 Print Certificate

    </button>

    <a href="home.php" class="backBtn">
        🏠 Back to Home
    </a>

</div>

<?php } else { ?>

<div class="lockedCard">

    <h1>🔒 Certificate Locked</h1>

    <br>

    <h2>
        Earn 100 Stars to Unlock Your Galaxy Certificate
    </h2>

    <br>

    <p>
        Current Stars:
        <b><?php echo $points; ?></b> / 100 ⭐
    </p>

    <div class="progressOuter">
        <div class="progressInner"></div>
    </div>

    <br>

    <p>
        Keep learning and completing quiz missions to unlock your Galaxy Learner Certificate!
    </p>

    <a href="worksheet.php" class="backBtn">
        🚀 Continue Mission
    </a>

    <a href="home.php" class="backBtn">
        🏠 Back to Home
    </a>

</div>

<?php } ?>

</body>
</html>