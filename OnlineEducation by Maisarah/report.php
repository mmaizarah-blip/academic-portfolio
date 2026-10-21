<?php

session_start();

include "db.php";

$adminName = "Admin";

if(isset($_SESSION['username']))
{
    $adminName = $_SESSION['username'];
}

$totalStudentSql =
"SELECT COUNT(*) AS total
FROM users
WHERE role='Student'";

$totalStudentResult =
$conn->query($totalStudentSql);

$totalStudentRow =
$totalStudentResult->fetch_assoc();

$totalStudents =
$totalStudentRow['total'];



$totalInstructorSql =
"SELECT COUNT(*) AS total
FROM users
WHERE role='Instructor'";

$totalInstructorResult =
$conn->query($totalInstructorSql);

$totalInstructorRow =
$totalInstructorResult->fetch_assoc();

$totalInstructors =
$totalInstructorRow['total'];



$totalQuestionSql =
"SELECT COUNT(*) AS total
FROM worksheet_questions";

$totalQuestionResult =
$conn->query($totalQuestionSql);

$totalQuestionRow =
$totalQuestionResult->fetch_assoc();

$totalQuestions =
$totalQuestionRow['total'];



$progressSql =
"SELECT
SUM(total_points) AS points,
SUM(total_attempt) AS attempts,
SUM(total_correct) AS correct
FROM
(
    SELECT
        user_code,
        SUM(total_points) AS total_points,
        SUM(total_attempt) AS total_attempt,
        SUM(total_correct) AS total_correct
    FROM student_progress
    GROUP BY user_code
) AS progress";

$progressResult =
$conn->query($progressSql);

$progressRow =
$progressResult->fetch_assoc();

$totalPoints =
$progressRow['points'];

$totalAttempts =
$progressRow['attempts'];

$totalCorrect =
$progressRow['correct'];

if($totalPoints == NULL)
{
    $totalPoints = 0;
}

if($totalAttempts == NULL)
{
    $totalAttempts = 0;
}

if($totalCorrect == NULL)
{
    $totalCorrect = 0;
}

if($totalAttempts > 0)
{
    $accuracy =
    round(($totalCorrect / $totalAttempts) * 100);
}
else
{
    $accuracy = 0;
}



$studentReportSql =
"SELECT
    users.user_code,
    users.username,
    users.email,

    SUM(COALESCE(student_progress.total_points,0)) AS total_points,
    SUM(COALESCE(student_progress.total_attempt,0)) AS total_attempt,
    SUM(COALESCE(student_progress.total_correct,0)) AS total_correct

FROM users

LEFT JOIN student_progress
ON users.user_code = student_progress.user_code

WHERE users.role='Student'

GROUP BY
    users.user_code,
    users.username,
    users.email

ORDER BY total_points DESC,
users.username ASC";

$studentReportResult =
$conn->query($studentReportSql);

$studentPrintResult =
$conn->query($studentReportSql);

$chartResult =
$conn->query($studentReportSql);

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
<title>Report</title>

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

/* MAIN */

.main-content
{
    margin-left:260px;
    width:calc(100% - 260px);
    padding:40px;
}

.title-box
{
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    padding:30px;
    border-radius:22px;
    margin-bottom:25px;
    box-shadow:0 8px 20px rgba(106,13,173,0.25);
}

.title-box h1
{
    margin-bottom:8px;
}

.stats
{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:20px;
    margin-bottom:25px;
}

.card
{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.icon
{
    font-size:30px;
    margin-bottom:10px;
}

.card h3
{
    color:#666;
    font-size:15px;
}

.number
{
    color:purple;
    font-size:34px;
    font-weight:bold;
    margin-top:8px;
}

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

.container
{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    margin-bottom:25px;
}

.table-header
{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.table-header h2
{
    color:purple;
    margin:0;
}

.printBtn
{
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:10px;
    cursor:pointer;
    font-size:14px;
    font-weight:bold;
    transition:0.3s;
}

.printBtn:hover
{
    transform:translateY(-2px);
    box-shadow:0 5px 15px rgba(106,13,173,0.3);
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

/* PRINT REPORT HIDDEN ON WEBSITE */

.print-report
{
    display:none;
}

/* PRINT DESIGN */

@media print
{
    @page
    {
        size:A4;
        margin:18mm;
    }

    body
    {
        background:white;
        display:block;
        font-family:Arial, sans-serif;
        color:#000;
    }

    .sidebar,
    .main-content,
    .graph-section
    {
        display:none;
    }

    .print-report
    {
        display:block;
        width:100%;
        position:relative;
        min-height:1000px;
    }

    .print-header
    {
        text-align:center;
        border:2px solid #000;
        padding:14px;
        margin-bottom:18px;
    }

    .print-header h1
    {
        font-size:18px;
        letter-spacing:1px;
        margin-bottom:6px;
    }

    .print-header h2
    {
        font-size:15px;
        margin-top:4px;
    }

    .print-info
    {
        width:100%;
        border-collapse:collapse;
        margin-bottom:20px;
        font-size:12px;
    }

    .print-info td
    {
        border:1px solid #000;
        padding:7px;
        text-align:left;
    }

    .section-title
    {
        font-size:14px;
        font-weight:bold;
        margin-top:16px;
        margin-bottom:8px;
        border-bottom:2px solid #000;
        padding-bottom:5px;
    }

    .summary-table
    {
        width:100%;
        border-collapse:collapse;
        margin-bottom:20px;
        font-size:12px;
    }

    .summary-table td
    {
        border:1px solid #000;
        padding:8px;
    }

    .summary-label
    {
        font-weight:bold;
        background:#eeeeee;
        width:25%;
    }

    .print-table
    {
        width:100%;
        border-collapse:collapse;
        font-size:11px;
        margin-top:10px;
    }

    .print-table th
    {
        background:#e6e6e6;
        color:#000;
        border:1px solid #000;
        padding:7px;
        text-align:center;
    }

    .print-table td
    {
        border:1px solid #000;
        padding:6px;
        text-align:center;
    }

    .print-table tr:nth-child(even)
    {
        background:#f7f7f7;
    }

    .print-footer
    {
        position:absolute;
        bottom:0;
        left:0;
        width:100%;
        font-size:12px;
    }

    .signature
    {
        margin-top:45px;
        width:230px;
        border-top:1px solid #000;
        padding-top:6px;
        text-align:center;
    }
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

            <a href="s_progress.php">
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

    <a href="report.php" class="active">📊 Report</a>
	<a href="subscription_report.php">💳 Subscription Report</a>

    <a href="a_profile.php">👤 Profile</a>

    <a href="login.php" class="logout">🚪 Logout</a>

</div>

<div class="main-content">

    <div class="title-box">

        <h1>📊 System Report</h1>

        <p>
            Summary report for users, worksheet questions and student performance.
        </p>

    </div>

    <div class="stats">

        <div class="card">
            <div class="icon">👨‍🎓</div>
            <h3>Total Students</h3>
            <div class="number"><?php echo $totalStudents; ?></div>
        </div>

        <div class="card">
            <div class="icon">👨‍🏫</div>
            <h3>Total Instructors</h3>
            <div class="number"><?php echo $totalInstructors; ?></div>
        </div>

        <div class="card">
            <div class="icon">📚</div>
            <h3>Total Questions</h3>
            <div class="number"><?php echo $totalQuestions; ?></div>
        </div>

        <div class="card">
            <div class="icon">🏆</div>
            <h3>Total Points</h3>
            <div class="number"><?php echo $totalPoints; ?></div>
        </div>

    </div>

    <div class="stats">

        <div class="card">
            <div class="icon">📝</div>
            <h3>Total Attempts</h3>
            <div class="number"><?php echo $totalAttempts; ?></div>
        </div>

        <div class="card">
            <div class="icon">✅</div>
            <h3>Correct Answers</h3>
            <div class="number"><?php echo $totalCorrect; ?></div>
        </div>

        <div class="card">
            <div class="icon">🎯</div>
            <h3>Average Accuracy</h3>
            <div class="number"><?php echo $accuracy; ?>%</div>
        </div>

        <div class="card">
            <div class="icon">⭐</div>
            <h3>Average Points</h3>
            <div class="number">
                <?php

                if($totalStudents > 0)
                {
                    echo round($totalPoints / $totalStudents);
                }
                else
                {
                    echo 0;
                }

                ?>
            </div>
        </div>

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

    <div class="container">

        <div class="table-header">

            <h2>Student Performance Report</h2>

            <button class="printBtn" onclick="window.print()">
                🖨 Print Report
            </button>

        </div>

        <table>

            <tr>
                <th>No.</th>
                <th>Student ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Total Points</th>
                <th>Questions Attempted</th>
                <th>Correct Answers</th>
                <th>Accuracy</th>
            </tr>

            <?php

            $no = 1;

            if($studentReportResult->num_rows > 0)
            {
                while($row = $studentReportResult->fetch_assoc())
                {
                    if($row['total_attempt'] > 0)
                    {
                        $studentAccuracy =
                        round(($row['total_correct'] / $row['total_attempt']) * 100);
                    }
                    else
                    {
                        $studentAccuracy = 0;
                    }

                    echo "
                    <tr>
                        <td>$no</td>
                        <td>{$row['user_code']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['total_points']}</td>
                        <td>{$row['total_attempt']}</td>
                        <td>{$row['total_correct']}</td>
                        <td>{$studentAccuracy}%</td>
                    </tr>
                    ";

                    $no++;
                }
            }
            else
            {
                echo "
                <tr>
                    <td colspan='8'>
                        No report data found
                    </td>
                </tr>
                ";
            }

            ?>

        </table>

    </div>

</div>

<div class="print-report">

    <div class="print-header">

        <h1>OneStepAhead Education System</h1>

        <h2>STUDENT PERFORMANCE REPORT</h2>

    </div>

    <table class="print-info">

        <tr>
            <td><b>Generated Date</b></td>
            <td><?php echo date("d/m/Y"); ?></td>
            <td><b>Generated Time</b></td>
            <td><?php echo date("h:i A"); ?></td>
        </tr>

    </table>

    <div class="section-title">
        SUMMARY
    </div>

    <table class="summary-table">

        <tr>
            <td class="summary-label">Total Students</td>
            <td><?php echo $totalStudents; ?></td>
            <td class="summary-label">Total Points</td>
            <td><?php echo $totalPoints; ?></td>
        </tr>

        <tr>
            <td class="summary-label">Total Attempts</td>
            <td><?php echo $totalAttempts; ?></td>
            <td class="summary-label">Correct Answers</td>
            <td><?php echo $totalCorrect; ?></td>
        </tr>

        <tr>
            <td class="summary-label">Average Accuracy</td>
            <td><?php echo $accuracy; ?>%</td>
            <td class="summary-label">Average Points</td>
            <td>
                <?php

                if($totalStudents > 0)
                {
                    echo round($totalPoints / $totalStudents);
                }
                else
                {
                    echo 0;
                }

                ?>
            </td>
        </tr>

    </table>

    <div class="section-title">
        STUDENT PERFORMANCE DETAILS
    </div>

    <table class="print-table">

        <tr>
            <th>No.</th>
            <th>Student ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Total Points</th>
            <th>Questions Attempted</th>
            <th>Correct Answers</th>
            <th>Accuracy</th>
        </tr>

        <?php

        $printNo = 1;

        if($studentPrintResult->num_rows > 0)
        {
            while($printRow = $studentPrintResult->fetch_assoc())
            {
                if($printRow['total_attempt'] > 0)
                {
                    $printAccuracy =
                    round(($printRow['total_correct'] / $printRow['total_attempt']) * 100);
                }
                else
                {
                    $printAccuracy = 0;
                }

                echo "
                <tr>
                    <td>$printNo</td>
                    <td>{$printRow['user_code']}</td>
                    <td>{$printRow['username']}</td>
                    <td>{$printRow['email']}</td>
                    <td>{$printRow['total_points']}</td>
                    <td>{$printRow['total_attempt']}</td>
                    <td>{$printRow['total_correct']}</td>
                    <td>{$printAccuracy}%</td>
                </tr>
                ";

                $printNo++;
            }
        }
        else
        {
            echo "
            <tr>
                <td colspan='8'>
                    No report data found
                </td>
            </tr>
            ";
        }

        ?>

    </table>

    <div class="print-footer">

        <p>
            Generated By:
            <b><?php echo $adminName; ?></b>
        </p>

        <div class="signature">
            Administrator Signature
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
    openDropdown("none");
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