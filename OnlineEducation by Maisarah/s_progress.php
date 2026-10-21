<?php

session_start();

include "db.php";

$search = "";

if(isset($_GET['search']))
{
    $search = $_GET['search'];
}

$sql =
"SELECT 
    users.user_code,
    users.username,
    users.standard,
    SUM(COALESCE(student_progress.total_points,0)) AS total_points,
    SUM(COALESCE(student_progress.total_attempt,0)) AS total_attempt,
    SUM(COALESCE(student_progress.total_correct,0)) AS total_correct

FROM users

LEFT JOIN student_progress
ON users.user_code = student_progress.user_code

WHERE users.role='Student'
AND
(
    users.username LIKE '%$search%'
    OR users.standard LIKE '%$search%'
    OR users.user_code LIKE '%$search%'
)

GROUP BY users.user_code, users.username, users.standard

ORDER BY total_points DESC";
$result = $conn->query($sql);



$chartSql =
"SELECT 
    users.username,
    SUM(COALESCE(student_progress.total_points,0)) AS total_points,
    SUM(COALESCE(student_progress.total_attempt,0)) AS total_attempt,
    SUM(COALESCE(student_progress.total_correct,0)) AS total_correct

FROM users

LEFT JOIN student_progress
ON users.user_code = student_progress.user_code

WHERE users.role='Student'
AND
(
    users.username LIKE '%$search%'
    OR users.standard LIKE '%$search%'
    OR users.user_code LIKE '%$search%'
)

GROUP BY users.user_code, users.username

ORDER BY total_points DESC";

$chartResult =
$conn->query($chartSql);

$studentNames = [];
$studentPoints = [];
$correctAnswers = [];
$wrongAnswers = [];

while($chartRow = $chartResult->fetch_assoc())
{
    $studentNames[] =
    $chartRow['username'];

    $studentPoints[] =
    $chartRow['total_points'];

    $correctAnswers[] =
    $chartRow['total_correct'];

    $wrong =
    $chartRow['total_attempt'] -
    $chartRow['total_correct'];

    if($wrong < 0)
    {
        $wrong = 0;
    }

    $wrongAnswers[] =
    $wrong;
}

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Student Progress</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    background:linear-gradient(180deg,#3b0057,#6a0dad,#4b007d);
    position:fixed;
    left:0;
    top:0;
    display:flex;
    flex-direction:column;
    box-shadow:4px 0 15px rgba(0,0,0,0.2);
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
    background:rgba(255,255,255,0.18);
}

.dropdown-content
{
    display:none;
    background:rgba(0,0,0,0.15);
}

.dropdown-content a
{
    padding-left:45px;
    font-size:15px;
}

.logout
{
    margin-top:auto;
    background:rgba(255,255,255,0.1);
}

/* MAIN CONTENT */

.main-content
{
    margin-left:260px;
    width:calc(100% - 260px);
    padding:40px;
}

.title-box
{
    background:white;
    padding:30px;
    border-radius:20px;
    margin-bottom:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.title-box h1
{
    color:purple;
    margin-bottom:8px;
}

.title-box p
{
    color:gray;
}

/* SEARCH */

.search-box
{
    background:white;
    padding:20px;
    border-radius:20px;
    margin-bottom:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.search-box form
{
    display:flex;
    gap:10px;
}

.search-box input
{
    width:350px;
    padding:12px;
    border:1px solid lightgray;
    border-radius:10px;
    font-size:15px;
}

.search-box button
{
    padding:12px 18px;
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
}

.resetBtn
{
    padding:12px 18px;
    background:#eeeeee;
    color:#333;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
}

/* GRAPH */

.graph-section
{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:25px;
    margin-bottom:25px;
}

.graph-box
{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.graph-box h2
{
    color:purple;
    margin-bottom:15px;
}

.graph-box canvas
{
    width:100% !important;
    height:300px !important;
}

/* TABLE */

.table-box
{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

table
{
    width:100%;
    border-collapse:collapse;
}

th
{
    background:purple;
    color:white;
    padding:14px;
}

td
{
    padding:13px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

tr:hover
{
    background:#faf5ff;
}

.accuracy-bar
{
    width:130px;
    background:#e0e0e0;
    border-radius:20px;
    overflow:hidden;
    margin:auto;
}

.accuracy-fill
{
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    padding:6px 0;
    font-size:13px;
    border-radius:20px;
}

</style>

</head>

<body>

<div class="sidebar">

    <div class="logo-box">
        <h2>Admin</h2>
        <p>OneStepAhead System</p>
    </div>

    <a href="A_home.php">🏠 Dashboard</a>

    <div class="dropdown">

        <button class="dropbtn" onclick="toggleStudent()">
            👨‍🎓 Student <span id="studentArrow">▼</span>
        </button>

        <div class="dropdown-content" id="studentMenu">

            <a href="s_profile.php">
                Profile
            </a>

            <a href="s_progress.php" class="active">
                Progress
            </a>

            <a href="a_leaderboard.php">
                Leaderboard
            </a>

        </div>

    </div>

    <div class="dropdown">

        <button class="dropbtn" onclick="toggleInstructor()">
            👨‍🏫 Instructor <span id="instructorArrow">▼</span>
        </button>

        <div class="dropdown-content" id="instructorMenu">

            <a href="i_profile.php">
                Profile
            </a>

        </div>

    </div>

    <a href="report.php">📊 Report</a>
	<a href="subscription_report.php">💳 Subscription Report</a>

    <a href="a_profile.php">👤 Profile</a>

    <a href="login.php" class="logout">🚪 Logout</a>

</div>

<div class="main-content">

    <div class="title-box">
        <h1>Student Progress</h1>
        <p>View student points, attempts, correct answers and accuracy.</p>
    </div>

    <div class="search-box">

        <form method="GET">

            <input
            type="text"
            name="search"
            placeholder="Search student or standard..."
            value="<?php echo $search; ?>">

            <button type="submit">
                Search
            </button>

            <a href="s_progress.php" class="resetBtn">
                Reset
            </a>

        </form>

    </div>

    <div class="graph-section">

        <div class="graph-box">

            <h2>Total Points by Student</h2>

            <canvas id="pointsChart"></canvas>

        </div>

        <div class="graph-box">

            <h2>Correct vs Wrong Answers</h2>

            <canvas id="answerChart"></canvas>

        </div>

    </div>

    <div class="table-box">

        <table>

            <tr>
                <th>Student Name</th>
                <th>Standard</th>
                <th>Total Points</th>
                <th>Questions Attempted</th>
                <th>Correct Answers</th>
                <th>Accuracy</th>
            </tr>

            <?php

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    $points =
                    $row['total_points'] ?? 0;

                    $attempt =
                    $row['total_attempt'] ?? 0;

                    $correct =
                    $row['total_correct'] ?? 0;

                    if($attempt > 0)
                    {
                        $accuracy =
                        round(($correct / $attempt) * 100);
                    }
                    else
                    {
                        $accuracy = 0;
                    }

                    echo "
                    <tr>
                        <td>{$row['username']}</td>
                        <td>{$row['standard']}</td>
                        <td>$points</td>
                        <td>$attempt</td>
                        <td>$correct</td>
                        <td>
                            <div class='accuracy-bar'>
                                <div class='accuracy-fill' style='width:{$accuracy}%'>
                                    {$accuracy}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    ";
                }
            }
            else
            {
                echo "
                <tr>
                    <td colspan='6'>
                        No student found
                    </td>
                </tr>
                ";
            }

            ?>

        </table>

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
        studentArrow.innerHTML = "▲";

        instructorMenu.style.display = "none";
        instructorArrow.innerHTML = "▼";

        localStorage.setItem("openDropdown", "student");
    }
    else if(which === "instructor")
    {
        instructorMenu.style.display = "block";
        instructorArrow.innerHTML = "▲";

        studentMenu.style.display = "none";
        studentArrow.innerHTML = "▼";

        localStorage.setItem("openDropdown", "instructor");
    }
    else
    {
        studentMenu.style.display = "none";
        studentArrow.innerHTML = "▼";

        instructorMenu.style.display = "none";
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
    openDropdown("student");
}

var studentNames =
<?php echo json_encode($studentNames); ?>;

var studentPoints =
<?php echo json_encode($studentPoints); ?>;

var correctAnswers =
<?php echo json_encode($correctAnswers); ?>;

var wrongAnswers =
<?php echo json_encode($wrongAnswers); ?>;



var pointsChart =
document.getElementById("pointsChart");

new Chart(pointsChart,
{
    type:"bar",

    data:
    {
        labels:studentNames,

        datasets:
        [
            {
                label:"Total Points",
                data:studentPoints,
                backgroundColor:"#8e24aa",
                borderRadius:8
            }
        ]
    },

    options:
    {
        responsive:true,
        maintainAspectRatio:false,

        plugins:
        {
            legend:
            {
                display:false
            }
        },

        scales:
        {
            y:
            {
                beginAtZero:true
            }
        }
    }
});



var answerChart =
document.getElementById("answerChart");

new Chart(answerChart,
{
    type:"bar",

    data:
    {
        labels:studentNames,

        datasets:
        [
            {
                label:"Correct Answers",
                data:correctAnswers,
                backgroundColor:"#2ecc71",
                borderRadius:8
            },

            {
                label:"Wrong Answers",
                data:wrongAnswers,
                backgroundColor:"#e74c3c",
                borderRadius:8
            }
        ]
    },

    options:
    {
        responsive:true,
        maintainAspectRatio:false,

        scales:
        {
            y:
            {
                beginAtZero:true
            }
        }
    }
});

</script>

</body>
</html>