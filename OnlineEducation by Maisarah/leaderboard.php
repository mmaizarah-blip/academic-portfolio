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

$userSql =
"SELECT *
FROM users
WHERE user_code='$userCode'";

$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

$avatar = "avatar.jpg";

if(isset($user['avatar']) && $user['avatar'] != "")
{
    $avatar = $user['avatar'];
}

$sql =
"SELECT 
users.user_code,
users.username,
users.standard,
users.avatar,
student_progress.total_points,
student_progress.total_attempt,
student_progress.total_correct

FROM users

INNER JOIN student_progress
ON users.user_code = student_progress.user_code

WHERE users.role='Student'";

if($search != "")
{
    $sql .= "
    AND
    (
        users.username LIKE '%$search%'
        OR users.user_code LIKE '%$search%'
        OR users.standard LIKE '%$search%'
    )";
}

$sql .= "
ORDER BY student_progress.total_points DESC";

$result = $conn->query($sql);

$students = [];

while($row = $result->fetch_assoc())
{
    $students[] = $row;
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Leaderboard</title>

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
    content: "⭐";
    position: absolute;
    top: 95px;
    right: 18px;
    font-size: 18px;
}

.sidebar::after
{
    content: "☁️";
    position: absolute;
    bottom: 20px;
    left: 25px;
    font-size: 45px;
}

.logoBox
{
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 35px;
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
    color: #ffe8f7;
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
    background: rgba(255,255,255,0.18);
    padding: 20px;
    border-radius: 25px;
    text-align: center;
    line-height: 1.6;
    border: 2px dashed rgba(255,255,255,0.45);
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
    border: 2px solid #eadcff;
    font-size: 15px;
    outline: none;
    box-shadow: 0 8px 20px rgba(124,58,237,0.08);
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
    border: 2px solid #fbcfe8;
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
    padding: 38px;
    border-radius: 35px;
    margin-bottom: 25px;
    box-shadow: 0 12px 25px rgba(124,58,237,0.12);
    border: 3px dashed #ffd166;
    position: relative;
    overflow: hidden;
}

.hero::before
{

    content: "🚀";
    position: absolute;
    top: 25px;
    right: 40px;
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
}

/* TOP THREE */

.topThree
{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.winnerCard
{
    background: white;
    padding: 30px;
    border-radius: 35px;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
    border: 2px solid #fbcfe8;
    transition: 0.3s;
}

.winnerCard:hover
{
    transform: translateY(-10px) scale(1.03);
}

.winnerCard::after
{
    content: "✨";
    position: absolute;
    top: 20px;
    right: 25px;
    font-size: 25px;
}

.winnerCard.first
{
  background: linear-gradient(135deg, #fff7cc, #fffbe8);
    border-top: 10px solid gold;
}

.winnerCard.second
{
    background: linear-gradient(135deg, #dffaff, #f5fdff);
    border-top: 10px solid silver;
}

.winnerCard.third
{
     background: linear-gradient(135deg, #ffeaf7, #fff7fc);
    border-top: 10px solid #cd7f32;
}

.medal
{
    font-size: 50px;
    margin-bottom: 10px;
}

.winnerCard img
{
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 12px;
    background: white;
    padding: 6px;
    border: 3px solid #fbcfe8;
}

.winnerCard h2
{
    color: #3b0764;
    margin-bottom: 5px;
}

.winnerCard p
{
    color: #666;
    margin-bottom: 8px;
}

.points
{
    font-size: 28px;
    font-weight: bold;
    color: #ec4899;
}

/* TABLE */

.tableCard
{
    background: white;
    padding: 30px;
    border-radius: 35px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #fbcfe8;
    overflow-x: auto;
}

.tableCard h2
{
    color: #3b0764;
    margin-bottom: 20px;
}

table
{
    width: 100%;
    border-collapse: collapse;
}

th
{
    background: linear-gradient(90deg, #6c63ff, #ff8ed4);
    color: white;
    padding: 15px;
}

th:first-child
{
    border-radius: 20px 0 0 20px;
}

th:last-child
{
    border-radius: 0 20px 20px 0;
}

td
{
    padding: 15px;
    border-bottom: 1px solid #fce7f3;
    text-align: center;
}

tr:hover
{
    background: #fff8fd;
}

.studentInfo
{
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.studentInfo img
{
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fbcfe8;
}

.rank
{
    font-size: 24px;
    font-weight: bold;
	 color: #6c63ff;
}

.badge
{
    display: inline-block;
     background: #eefcff;
    color: #6c63ff;
    padding: 8px 15px;
    border-radius: 25px;
    font-weight: bold;
    border: 2px solid #fbcfe8;
}

.empty
{
    text-align: center;
    padding: 40px;
    color: #777;
}

@media(max-width:900px)
{
    .topThree
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
        <a href="video.php">🎥 Video Lessons</a>
        <a href="progress.php">📊 Progress</a>
        <a href="leaderboard.php" class="active">🏆 Leaderboard</a>
        <a href="profile.php">👤 Profile</a>
        <a href="certificate.php">📜 Certificate</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="quoteBox">
🚀<br><br>
Collect stars,<br>
complete missions,<br>
and reach the galaxy! ✨
</div>

</div>

<div class="main">

    <div class="topbar">

        <form method="GET" action="leaderboard.php">
    <input
    type="text"
    name="search"
    class="searchBox"
    placeholder="🔍 Search leaderboard, students, ranking..."
    value="<?php echo $search; ?>">
</form>

        <a href="profile.php" style="text-decoration:none;color:inherit;">
            <div class="profileMini">
                <img src="<?php echo $avatar; ?>">

                <div>
                    <b><?php echo $user['username']; ?> 🌟</b><br>
                    <small><?php echo $user['standard']; ?> Explorer</small>
                </div>
            </div>
        </a>

    </div>
	<?php if($search != "") { ?>

<div class="tableCard" style="margin-bottom:25px;">
    <h2>🔍 Search Result for "<?php echo $search; ?>"</h2>
    <p>Showing leaderboard results that match the student name, student ID, or standard.</p>
</div>

<?php } ?>

    <div class="hero">
        <h1>🚀 Galaxy Ranking Board</h1>
        <p>See the top space explorers, collect stars and climb your way to the top of the galaxy!</p>
    </div>

    <?php if(count($students) > 0) { ?>

    <div class="topThree">

        <?php

        for($i = 0; $i < 3; $i++)
        {
            if(isset($students[$i]))
            {
                $s = $students[$i];

                $studentAvatar = "avatar.jpg";

                if(isset($s['avatar']) && $s['avatar'] != "")
                {
                    $studentAvatar = $s['avatar'];
                }

                if($i == 0)
                {
                    $class = "first";
                    $medal = "🥇";
                    $title = "Super Star";
                }
                else if($i == 1)
                {
                    $class = "second";
                    $medal = "🥈";
                    $title = "Bright Learner";
                }
                else
                {
                    $class = "third";
                    $medal = "🥉";
                    $title = "Rising Star";
                }

        ?>

        <div class="winnerCard <?php echo $class; ?>">

            <div class="medal">
                <?php echo $medal; ?>
            </div>

            <img src="<?php echo $studentAvatar; ?>">

            <h2><?php echo $s['username']; ?></h2>

            <p><?php echo $s['standard']; ?></p>

            <div class="points">
                <?php echo $s['total_points']; ?> stars ⭐
            </div>

            <br>

            <span class="badge">
                <?php echo $title; ?>
            </span>

        </div>

        <?php
            }
        }
        ?>

    </div>

    <div class="tableCard">

        <h2>🚀 Galaxy Explorer Rankings</h2>

        <table>

            <tr>
                <th>Rank</th>
                <th>Student</th>
                <th>Student ID</th>
                <th>Standard</th>
                <th>Attempted</th>
                <th>Correct</th>
                <th>Total Stars</th>
            </tr>

            <?php

            $rank = 1;

            foreach($students as $row)
            {
                $studentAvatar = "avatar.jpg";

                if(isset($row['avatar']) && $row['avatar'] != "")
                {
                    $studentAvatar = $row['avatar'];
                }

                if($rank == 1)
                {
                    $rankText = "🥇";
                }
                else if($rank == 2)
                {
                    $rankText = "🥈";
                }
                else if($rank == 3)
                {
                    $rankText = "🥉";
                }
                else
                {
                    $rankText = $rank;
                }

            ?>

            <tr>

                <td class="rank">
                    <?php echo $rankText; ?>
                </td>

                <td>
                    <div class="studentInfo">
                        <img src="<?php echo $studentAvatar; ?>">
                        <b><?php echo $row['username']; ?></b>
                    </div>
                </td>

                <td>
                    <?php echo $row['user_code']; ?>
                </td>

                <td>
                    <?php echo $row['standard']; ?>
                </td>

                <td>
                    <?php echo $row['total_attempt']; ?>
                </td>

                <td>
                    <?php echo $row['total_correct']; ?>
                </td>

                <td>
                    <span class="badge">
                        <?php echo $row['total_points']; ?> stars ⭐
                    </span>
                </td>

            </tr>

            <?php

                $rank++;
            }

            ?>

        </table>

    </div>

    <?php } else { ?>

    <div class="tableCard empty">
        <h2>No leaderboard data yet.</h2>
        <p>Complete a quiz first to appear on the leaderboard 🌟</p>
    </div>

    <?php } ?>

</div>

</body>
</html>