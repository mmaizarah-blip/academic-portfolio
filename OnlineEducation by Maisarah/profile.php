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
    $search = strtolower($_GET['search']);
}

$message = "";

if(isset($_POST['updateProfile']))
{
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $avatar = $_POST['avatar'];
    $password = $_POST['password'];

    if($password != "")
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $update =
        "UPDATE users
        SET username='$username',
        email='$email',
        phone='$phone',
        avatar='$avatar',
        password='$hashedPassword'
        WHERE user_code='$userCode'";
    }
    else
    {
        $update =
        "UPDATE users
        SET username='$username',
        email='$email',
        phone='$phone',
        avatar='$avatar'
        WHERE user_code='$userCode'";
    }

    if($conn->query($update))
    {
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $message = "Profile updated successfully!";
    }
}

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

$badge1 = ($points >= 50);
$badge2 = ($points >= 100);
$badge3 = ($points >= 200);
$badge4 = ($points >= 500);

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

<title>My Space Profile</title>

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
    content: "👩‍🚀";
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

/* MESSAGE */

.message
{
    background: #e8ffe8;
    color: #15803d;
    padding: 15px;
    border-radius: 25px;
    margin-bottom: 20px;
    font-weight: bold;
    border: 2px solid #86efac;
}

/* LAYOUT */

.grid
{
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 25px;
}

.card,
.achievementCard
{
    background: white;
    padding: 30px;
    border-radius: 35px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
}

.card h2,
.achievementCard h2
{
    color: #3b0764;
    margin-bottom: 20px;
}

/* PROFILE SUMMARY */

.avatarPreview
{
    text-align: center;
}

.avatarPreview img
{
    width: 175px;
    height: 175px;
    border-radius: 50%;
    object-fit: cover;
    background: #eefcff;
    padding: 8px;
    margin-bottom: 20px;
    border: 4px solid #52e5d8;
    box-shadow: 0 8px 18px rgba(82,229,216,0.25);
}

.infoRow
{
    padding: 13px 0;
    border-bottom: 1px solid #e0f7ff;
}

.infoRow span
{
    color: #666;
}

.infoRow b
{
    color: #3b0764;
    float: right;
}

/* STATS */

.stats
{
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 15px;
    margin-top: 20px;
}

.statBox
{
    padding: 18px;
    border-radius: 25px;
    text-align: center;
    border: 2px solid #d8f5ff;
}

.stats .statBox:nth-child(1)
{
    background: #fff7cc;
}

.stats .statBox:nth-child(2)
{
    background: #ffeaf7;
}

.stats .statBox:nth-child(3)
{
    background: #e0f7ff;
}

.stats .statBox:nth-child(4)
{
    background: #e8ffe8;
}

.statBox h1
{
    color: #6c63ff;
}

.statBox p
{
    color: #555;
    font-size: 14px;
}

/* ACHIEVEMENTS */

.achievementCard
{
    margin-top: 25px;
}

.badges
{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.badgeItem
{
    padding: 18px;
    border-radius: 25px;
    text-align: center;
    font-weight: bold;
    font-size: 15px;
}

.unlocked
{
    background: linear-gradient(135deg, #fff7cc, #ffd93d);
    color: #6b5200;
    box-shadow: 0 5px 12px rgba(255,217,61,0.35);
    border: 2px solid #ffd93d;
}

.locked
{
    background: #f1f1f1;
    color: #999;
    filter: grayscale(100%);
    border: 2px solid #ddd;
}

/* FORM */

.formGroup
{
    margin-bottom: 18px;
}

.formGroup label
{
    font-weight: bold;
    color: #3b0764;
}

.formGroup input
{
    width: 100%;
    padding: 14px;
    margin-top: 7px;
    border: 2px solid #d8f5ff;
    border-radius: 18px;
    outline: none;
    font-size: 15px;
}

.formGroup input:focus
{
    border-color: #6c63ff;
    box-shadow: 0 0 10px rgba(108,99,255,0.20);
}

.avatarGrid
{
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 12px;
}

.avatarOption input
{
    display: none;
}

.avatarOption img
{
    width: 72px;
    height: 72px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
    border: 4px solid transparent;
    transition: 0.3s;
    background: #eefcff;
}

.avatarOption img:hover
{
    transform: scale(1.1) rotate(-3deg);
}

.avatarOption input:checked + img
{
    border: 4px solid #6c63ff;
    box-shadow: 0 0 15px rgba(108,99,255,0.55);
}

.saveBtn
{
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
    color: white;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 15px;
    box-shadow: 0 8px 18px rgba(108,99,255,0.25);
}

.saveBtn:hover
{
    transform: translateY(-3px);
}

@media(max-width:900px)
{
    .grid
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
        <a href="progress.php">📊 Progress</a>
        <?php if($canAccessLeaderboard) { ?>
    <a href="leaderboard.php">🏆 Leaderboard</a>
<?php } else { ?>
    <a href="#" onclick="alert('Leaderboard is only available for Premium Plan'); return false;">🔒 Leaderboard</a>
<?php } ?>
        <a href="profile.php" class="active">👤 Profile</a>
        <?php if($canAccessCertificate) { ?>
    <a href="certificate.php">📜 Certificate</a>
<?php } else { ?>
    <a href="#" onclick="alert('Certificate is only available for Premium Plan'); return false;">🔒 Certificate</a>
<?php } ?>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="quoteBox">
        👩‍🚀<br><br>
        Choose your avatar,<br>
        collect stars,<br>
        and explore more! ✨
    </div>

</div>

<div class="main">

    <div class="topbar">

        <form method="GET" action="profile.php">
    <input
    type="text"
    name="search"
    class="searchBox"
    placeholder="🔍 Search profile, progress, achievements..."
    value="<?php echo $search; ?>">
</form>

        <div class="profileMini">
            <img src="<?php echo $avatar; ?>">

            <div>
                <b><?php echo $user['username']; ?> ⭐</b><br>
                <small><?php echo $user['standard']; ?> Explorer</small>
            </div>
        </div>

    </div>
	<?php
if($search != "")
{
    echo "<div class='card' style='margin-bottom:25px;'>";

    echo "<h2>🔍 Search Result</h2>";

    if(strpos($search, "username") !== false || strpos($search, "name") !== false)
    {
        echo "<p>Your username is <b>".$user['username']."</b>.</p>";
    }
    else if(strpos($search, "email") !== false)
    {
        echo "<p>Your email is <b>".$user['email']."</b>.</p>";
    }
    else if(strpos($search, "phone") !== false)
    {
        echo "<p>Your phone number is <b>".$user['phone']."</b>.</p>";
    }
    else if(strpos($search, "standard") !== false)
    {
        echo "<p>Your standard is <b>".$user['standard']."</b>.</p>";
    }
    else if(strpos($search, "star") !== false || strpos($search, "point") !== false)
    {
        echo "<p>Your total stars collected is <b>".$points."</b>.</p>";
    }
    else if(strpos($search, "accuracy") !== false)
    {
        echo "<p>Your current accuracy is <b>".$accuracy."%</b>.</p>";
    }
    else if(strpos($search, "achievement") !== false || strpos($search, "badge") !== false)
    {
        echo "<p>Your achievements are shown in the Space Achievements section below.</p>";
    }
    else
    {
        echo "<p>No matching profile result found. Try: username, email, phone, standard, stars, accuracy, or achievement.</p>";
    }

    echo "</div>";
}
?>

    <div class="hero">
        <h1>👩‍🚀 My Space Profile</h1>
        <p>Edit your username, email, phone number, password and choose your favourite astronaut avatar.</p>
    </div>

    <?php if($message != "") { ?>
        <div class="message">
            ✅ <?php echo $message; ?>
        </div>
    <?php } ?>

    <div class="grid">

        <div>

            <div class="card avatarPreview">

                <h2>⭐ Explorer Summary</h2>

                <img src="<?php echo $avatar; ?>">

                <div class="infoRow">
                    <span>Student ID</span>
                    <b><?php echo $user['user_code']; ?></b>
                </div>

                <div class="infoRow">
                    <span>Username</span>
                    <b><?php echo $user['username']; ?></b>
                </div>

                <div class="infoRow">
                    <span>Email</span>
                    <b><?php echo $user['email']; ?></b>
                </div>

                <div class="infoRow">
                    <span>Standard</span>
                    <b><?php echo $user['standard']; ?></b>
                </div>

                <div class="stats">

                    <div class="statBox">
                        <h1><?php echo $points; ?></h1>
                        <p>Stars</p>
                    </div>

                    <div class="statBox">
                        <h1><?php echo $accuracy; ?>%</h1>
                        <p>Accuracy</p>
                    </div>

                    <div class="statBox">
                        <h1><?php echo $attempt; ?></h1>
                        <p>Missions</p>
                    </div>

                    <div class="statBox">
                        <h1><?php echo $correct; ?></h1>
                        <p>Correct</p>
                    </div>

                </div>

            </div>

            <div class="achievementCard">

                <h2>🏅 Space Achievements</h2>

                <div class="badges">

                    <div class="badgeItem <?php echo $badge1 ? 'unlocked' : 'locked'; ?>">
                        🌱 Beginner Explorer<br>
                        <small>50 Stars</small>
                    </div>

                    <div class="badgeItem <?php echo $badge2 ? 'unlocked' : 'locked'; ?>">
                        🚀 Space Cadet<br>
                        <small>100 Stars</small>
                    </div>

                    <div class="badgeItem <?php echo $badge3 ? 'unlocked' : 'locked'; ?>">
                        🌌 Galaxy Master<br>
                        <small>200 Stars</small>
                    </div>

                    <div class="badgeItem <?php echo $badge4 ? 'unlocked' : 'locked'; ?>">
                        👑 Quiz Champion<br>
                        <small>500 Stars</small>
                    </div>

                </div>

            </div>

        </div>

        <div class="card">

            <h2>✏️ Edit Explorer Profile</h2>

            <form method="POST">

                <div class="formGroup">
                    <label>Username</label>
                    <input
                    type="text"
                    name="username"
                    value="<?php echo $user['username']; ?>"
                    required>
                </div>

                <div class="formGroup">
                    <label>Email</label>
                    <input
                    type="email"
                    name="email"
                    value="<?php echo $user['email']; ?>"
                    required>
                </div>

                <div class="formGroup">
                    <label>Phone Number</label>
                    <input
                    type="text"
                    name="phone"
                    value="<?php echo $user['phone']; ?>">
                </div>

                <div class="formGroup">
                    <label>New Password</label>
                    <input
                    type="password"
                    name="password"
                    placeholder="Leave blank if no change">
                </div>

                <div class="formGroup">
                    <label>Choose Avatar</label>

                    <div class="avatarGrid">

                        <?php
                        for($i = 1; $i <= 7; $i++)
                        {
                            $file = "avatar" . $i . ".jpg";
                        ?>

                        <label class="avatarOption">
                            <input
                            type="radio"
                            name="avatar"
                            value="<?php echo $file; ?>"
                            <?php if($avatar == $file) echo "checked"; ?>>

                            <img src="<?php echo $file; ?>">
                        </label>

                        <?php
                        }
                        ?>

                        <label class="avatarOption">
                            <input
                            type="radio"
                            name="avatar"
                            value="avatar.jpg"
                            <?php if($avatar == "avatar.jpg") echo "checked"; ?>>

                            <img src="avatar.jpg">
                        </label>

                    </div>

                </div>

                <button
                type="submit"
                name="updateProfile"
                class="saveBtn">

                Save Changes ✨

                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>