<?php

session_start();

include "db.php";

if(!isset($_SESSION['user_code']))
{
    echo
    "<script>
        alert('Please login first');
        window.location.href='login.php';
    </script>";
}

if($_SESSION['role'] != "Admin")
{
    echo
    "<script>
        alert('Access denied');
        window.location.href='login.php';
    </script>";
}

$adminName = $_SESSION['username'];

$studentSql =
"SELECT COUNT(*) AS total_students
FROM users
WHERE role='Student'";

$studentResult =
$conn->query($studentSql);

$studentRow =
$studentResult->fetch_assoc();

$totalStudents =
$studentRow['total_students'];



$instructorSql =
"SELECT COUNT(*) AS total_instructors
FROM users
WHERE role='Instructor'";

$instructorResult =
$conn->query($instructorSql);

$instructorRow =
$instructorResult->fetch_assoc();

$totalInstructors =
$instructorRow['total_instructors'];



$questionSql =
"SELECT COUNT(*) AS total_questions
FROM worksheet_questions";

$questionResult =
$conn->query($questionSql);

$questionRow =
$questionResult->fetch_assoc();

$totalQuestions =
$questionRow['total_questions'];



$pointSql =
"SELECT SUM(total_points) AS total_points
FROM student_progress";

$pointResult =
$conn->query($pointSql);

$pointRow =
$pointResult->fetch_assoc();

$totalPoints =
$pointRow['total_points'];

$subscriptionSql =
"SELECT COUNT(*) AS total_subscription
FROM subscriptions
WHERE payment_status='Paid'";

$subscriptionResult =
$conn->query($subscriptionSql);

$subscriptionRow =
$subscriptionResult->fetch_assoc();

$totalSubscription =
$subscriptionRow['total_subscription'];

if($totalPoints == NULL)
{
    $totalPoints = 0;
}



$topSql =
"SELECT users.username,
        COALESCE(student_progress.total_points, 0) AS total_points
FROM users
LEFT JOIN student_progress
ON users.user_code = student_progress.user_code
WHERE users.role='Student'
ORDER BY total_points DESC
LIMIT 5";

$topResult =
$conn->query($topSql);



$activitySql =
"SELECT username AS title,
        role AS type,
        created_at
FROM users

UNION ALL

SELECT question_text AS title,
        'Question' AS type,
        created_at
FROM worksheet_questions

ORDER BY created_at DESC
LIMIT 5";

$activityResult =
$conn->query($activitySql);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>

<style>

*
{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body
{
    font-family:Arial, sans-serif;
    background:#f3f0f8;
    display:flex;
}

/* SIDEBAR */

.sidebar
{
    width:260px;
    height:100vh;

    background:
    linear-gradient(
    180deg,
    #3b0057,
    #6a0dad,
    #4b007d);

    position:fixed;
    left:0;
    top:0;

    display:flex;
    flex-direction:column;

    box-shadow:
    4px 0 15px rgba(0,0,0,0.2);
}

.logo-box
{
    color:white;
    text-align:center;
    padding:28px 20px;
}

.logo-box h2
{
    font-size:26px;
}

.logo-box p
{
    font-size:13px;
    opacity:0.8;
    margin-top:5px;
}

.sidebar a,
.dropbtn
{
    width:100%;
    display:block;

    padding:15px 22px;

    color:white;
    text-decoration:none;

    background:none;
    border:none;

    text-align:left;
    font-size:16px;

    cursor:pointer;
    transition:0.3s;
}

.sidebar a:hover,
.dropbtn:hover,
.active
{
    background:
    rgba(255,255,255,0.18);
}

.dropdown-content
{
    display:none;

    background:
    rgba(0,0,0,0.15);
}

.dropdown-content a
{
    padding-left:45px;
    font-size:15px;
}

.logout
{
    margin-top:auto;
    background:
    rgba(255,255,255,0.1);
}

/* MAIN */

.main-content
{
    margin-left:260px;
    width:calc(100% - 260px);
    padding:30px;
}

.hero
{
    background:linear-gradient(135deg,#7b1fa2,#b145d9,#e0b3ff);
    color:white;
    padding:35px;
    border-radius:25px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(123,31,162,0.3);
    margin-bottom:25px;
}

.hero h1
{
    font-size:34px;
    margin-bottom:8px;
}

.hero p
{
    font-size:17px;
    opacity:0.95;
}

.admin-badge
{
    background:white;
    color:purple;
    padding:15px 20px;
    border-radius:15px;
    font-weight:bold;
}

/* STATS */

.stats
{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:20px;
    margin-bottom:25px;
}

.stat-card
{
    background:white;
    padding:25px;
    border-radius:22px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    transition:0.3s;
}

.stat-card:hover
{
    transform:translateY(-5px);
}

.icon
{
    width:55px;
    height:55px;
    background:#f1e4ff;
    color:purple;
    border-radius:18px;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:28px;
    margin-bottom:15px;
}

.stat-card h3
{
    color:#666;
    font-size:15px;
}

.number
{
    font-size:36px;
    color:purple;
    font-weight:bold;
    margin-top:8px;
}

/* GRID AREA */

.dashboard-grid
{
    display:grid;
    grid-template-columns:1.2fr 0.8fr;
    gap:25px;
}

.box
{
    background:white;
    padding:25px;
    border-radius:22px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    margin-bottom:25px;
}

.box h2
{
    color:purple;
    margin-bottom:18px;
}

/* QUICK ACCESS */

.quick-buttons
{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px;
}

.quick-buttons a:last-child
{
    grid-column:1 / 3;
    text-align:center;
}

.quick-buttons a
{
    text-decoration:none;
    color:white;
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    padding:18px;
    border-radius:16px;
    font-weight:bold;
    transition:0.3s;
}

.quick-buttons a:hover
{
    transform:scale(1.03);
}

/* TABLE */

table
{
    width:100%;
    border-collapse:collapse;
}

th
{
    background:#6a0dad;
    color:white;
    padding:13px;
}

td
{
    padding:13px;
    text-align:center;
    border-bottom:1px solid #eee;
}

tr:hover
{
    background:#faf5ff;
}

/* RECENT */

.activity
{
    display:flex;
    align-items:center;
    gap:12px;
    padding:13px 0;
    border-bottom:1px solid #eee;
}

.activity-icon
{
    width:42px;
    height:42px;
    background:#f1e4ff;
    border-radius:50%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:20px;
}

.activity-text b
{
    color:#333;
}

.activity-text p
{
    color:gray;
    font-size:13px;
    margin-top:3px;
}

</style>
</head>

<body>

<div class="sidebar">

    <div class="logo-box">
        <h2>Admin</h2>
        <p>OneStepAhead System</p>
    </div>

    <a href="A_home.php" class="active">🏠 Dashboard</a>

    <div class="dropdown">
        <button class="dropbtn" onclick="toggleStudent()">
            👨‍🎓 Student <span id="studentArrow">▼</span>
        </button>

        <div class="dropdown-content" id="studentMenu">
            <a href="s_profile.php">Profile</a>
            <a href="s_progress.php">Progress</a>
            <a href="a_leaderboard.php">Leaderboard</a>
        </div>
    </div>

    <div class="dropdown">
        <button class="dropbtn" onclick="toggleInstructor()">
            👨‍🏫 Instructor <span id="instructorArrow">▼</span>
        </button>

        <div class="dropdown-content" id="instructorMenu">
            <a href="i_profile.php">Profile</a>
        </div>
    </div>

	<a href="report.php">📊 Report</a>
	
	<a href="subscription_report.php">💳 Subscription Report</a>
	
    <a href="a_profile.php">👤 Profile</a>

    <a href="login.php" class="logout">🚪 Logout</a>

</div>

<div class="main-content">

    <div class="hero">

        <div>
            <h1>Welcome Back, <?php echo $adminName; ?> 👋</h1>
            <p>Manage students, instructors, progress and worksheet records in one dashboard.</p>
        </div>

        <div class="admin-badge">
            Administrator
        </div>

    </div>

    <div class="stats">

        <div class="stat-card">
            <div class="icon">👨‍🎓</div>
            <h3>Total Students</h3>
            <div class="number"><?php echo $totalStudents; ?></div>
        </div>

        <div class="stat-card">
            <div class="icon">👨‍🏫</div>
            <h3>Total Instructors</h3>
            <div class="number"><?php echo $totalInstructors; ?></div>
        </div>

        <div class="stat-card">
            <div class="icon">📚</div>
            <h3>Total Questions</h3>
            <div class="number"><?php echo $totalQuestions; ?></div>
        </div>

        <div class="stat-card">
            <div class="icon">🏆</div>
            <h3>Total Points</h3>
            <div class="number"><?php echo $totalPoints; ?></div>
        </div>
		
		<div class="stat-card">
    <div class="icon">💳</div>
    <h3>Paid Subscription</h3>
    <div class="number">
        <?php echo $totalSubscription; ?>
    </div>
</div>

    </div>

    <div class="dashboard-grid">

        <div>

            <div class="box">

                <h2>Quick Access</h2>

                <div class="quick-buttons">

                    <a href="s_profile.php">👨‍🎓 Student Profile</a>

                    <a href="s_progress.php">📈 Student Progress</a>

                    <a href="a_leaderboard.php">🏆 Leaderboard</a>

                    <a href="i_profile.php">👨‍🏫 Instructor Profile</a>
					
					<a href="report.php">📊 System Report</a>
					<a href="subscription_report.php">
💳 Subscription Report
</a>
					

                </div>

            </div>

            <div class="box">

                <h2>Top 5 Students</h2>

                <table>

                    <tr>
                        <th>Rank</th>
                        <th>Student Name</th>
                        <th>Total Points</th>
                    </tr>

                    <?php

                    $rank = 1;

                    if($topResult->num_rows > 0)
                    {
                        while($row = $topResult->fetch_assoc())
                        {
                            echo "
                            <tr>
                                <td>$rank</td>
                                <td>{$row['username']}</td>
                                <td>{$row['total_points']}</td>
                            </tr>
                            ";

                            $rank++;
                        }
                    }
                    else
                    {
                        echo "
                        <tr>
                            <td colspan='3'>No student progress found</td>
                        </tr>
                        ";
                    }

                    ?>

                </table>

            </div>

        </div>

        <div>

            <div class="box">

                <h2>Recent Activities</h2>

                <?php

                if($activityResult->num_rows > 0)
                {
                    while($activity = $activityResult->fetch_assoc())
                    {
                        $icon = "👤";
                        $text = "registered";

                        if($activity['type'] == "Student")
                        {
                            $icon = "👨‍🎓";
                            $text = "student registered";
                        }
                        else if($activity['type'] == "Instructor")
                        {
                            $icon = "👨‍🏫";
                            $text = "instructor registered";
                        }
                        else if($activity['type'] == "Admin")
                        {
                            $icon = "🛡️";
                            $text = "admin account created";
                        }
                        else if($activity['type'] == "Question")
                        {
                            $icon = "📚";
                            $text = "worksheet question added";
                        }

                        echo "
                        <div class='activity'>

                            <div class='activity-icon'>
                                $icon
                            </div>

                            <div class='activity-text'>
                                <b>{$activity['title']}</b>
                                <p>$text • {$activity['created_at']}</p>
                            </div>

                        </div>
                        ";
                    }
                }
                else
                {
                    echo "<p>No recent activities found</p>";
                }

                ?>

            </div>

            <div class="box">

                <h2>Dashboard Overview</h2>

                <p>
                    There are currently
                    <b><?php echo $totalStudents; ?></b>
                    students and
                    <b><?php echo $totalInstructors; ?></b>
                    instructors registered in the system.
                </p>

                <br>

                <p>
                    The system contains
                    <b><?php echo $totalQuestions; ?></b>
                    worksheet questions and students have collected
                    <b><?php echo $totalPoints; ?></b>
                    total points.
                </p>

            </div>
			


        </div>

    </div>

</div>

<script>

function openDropdown(which)
{
    var studentMenu =
    document.getElementById("studentMenu");

    var instructorMenu =
    document.getElementById("instructorMenu");

    var studentArrow =
    document.getElementById("studentArrow");

    var instructorArrow =
    document.getElementById("instructorArrow");

    if(which === "student")
    {
        studentMenu.style.display = "block";
        instructorMenu.style.display = "none";

        studentArrow.innerHTML = "▲";
        instructorArrow.innerHTML = "▼";

        localStorage.setItem("openDropdown", "student");
    }
    else if(which === "instructor")
    {
        instructorMenu.style.display = "block";
        studentMenu.style.display = "none";

        instructorArrow.innerHTML = "▲";
        studentArrow.innerHTML = "▼";

        localStorage.setItem("openDropdown", "instructor");
    }
    else
    {
        studentMenu.style.display = "none";
        instructorMenu.style.display = "none";

        studentArrow.innerHTML = "▼";
        instructorArrow.innerHTML = "▼";

        localStorage.setItem("openDropdown", "none");
    }
}

function toggleStudent()
{
    var menu =
    document.getElementById("studentMenu");

    if(menu.style.display === "block")
    {
        openDropdown("none");
    }
    else
    {
        openDropdown("student");
    }
}

function toggleInstructor()
{
    var menu =
    document.getElementById("instructorMenu");

    if(menu.style.display === "block")
    {
        openDropdown("none");
    }
    else
    {
        openDropdown("instructor");
    }
}

window.onload = function()
{
    var saved =
    localStorage.getItem("openDropdown");

    if(saved === "student")
    {
        openDropdown("student");
    }
    else if(saved === "instructor")
    {
        openDropdown("instructor");
    }
    else
    {
        openDropdown("none");
    }
}

</script>

</body>
</html>