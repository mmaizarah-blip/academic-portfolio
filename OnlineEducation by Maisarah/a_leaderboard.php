<?php

session_start();

include "db.php";

$search = "";
$standard = "";

if(isset($_GET['search']))
{
    $search = $_GET['search'];
}

if(isset($_GET['standard']))
{
    $standard = $_GET['standard'];
}

$sql =
"SELECT 
    users.user_code,
    users.username,
    users.standard,
    SUM(COALESCE(student_progress.total_points,0)) AS total_points

FROM users

LEFT JOIN student_progress
ON users.user_code = student_progress.user_code

WHERE users.role='Student'
AND
(
    users.username LIKE '%$search%'
    OR users.user_code LIKE '%$search%'
)";

if($standard != "" && $standard != "All Standards")
{
    $sql .= " AND users.standard='$standard'";
}

$sql .= "
GROUP BY users.user_code, users.username, users.standard
ORDER BY total_points DESC, users.username ASC";

$result = $conn->query($sql);



$topSql =
"SELECT 
    users.user_code,
    users.username,
    users.standard,
    SUM(COALESCE(student_progress.total_points,0)) AS total_points

FROM users

LEFT JOIN student_progress
ON users.user_code = student_progress.user_code

WHERE users.role='Student'

GROUP BY users.user_code, users.username, users.standard

ORDER BY total_points DESC, users.username ASC
LIMIT 1";

$topResult = $conn->query($topSql);
$top = $topResult->fetch_assoc();

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Student Leaderboard</title>

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

.container
{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    margin-bottom:25px;
}

h1
{
    color:purple;
    margin-bottom:10px;
}

.subtitle
{
    color:gray;
    margin-bottom:20px;
}

/* SEARCH */

.search-form
{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.search-box,
.filter-box
{
    padding:12px;
    border:1px solid lightgray;
    border-radius:10px;
    font-size:15px;
}

.search-box
{
    width:350px;
}

.filter-box
{
    width:220px;
}

.searchBtn
{
    padding:12px 18px;
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    border:none;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
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

/* TOP CARD */

.top-card
{
    background:linear-gradient(135deg,#6a0dad,#b145d9,#d29cff);
    color:white;
    padding:35px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 8px 20px rgba(106,13,173,0.25);
}

.top-card h2
{
    font-size:30px;
    margin-bottom:18px;
}

.top-card p
{
    margin:8px 0;
    font-size:18px;
}

.points
{
    font-size:42px;
    font-weight:bold;
    margin-top:15px;
}

/* TABLE */

table
{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
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
    border-bottom:1px solid #ddd;
    text-align:center;
}

tr:hover
{
    background:#faf5ff;
}

.rank
{
    font-weight:bold;
    font-size:18px;
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

            <a href="a_leaderboard.php" class="active">
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

    <div class="container">

        <h1>🏆 Student Leaderboard</h1>

        <p class="subtitle">
            View student ranking based on total points.
        </p>

        <form method="GET" class="search-form">

            <input
            type="text"
            name="search"
            class="search-box"
            placeholder="Search student name or ID..."
            value="<?php echo $search; ?>">

            <select name="standard" class="filter-box">

                <option
                <?php if($standard == "" || $standard == "All Standards") echo "selected"; ?>>
                    All Standards
                </option>

                <option
                <?php if($standard == "Standard 1") echo "selected"; ?>>
                    Standard 1
                </option>

                <option
                <?php if($standard == "Standard 2") echo "selected"; ?>>
                    Standard 2
                </option>

                <option
                <?php if($standard == "Standard 3") echo "selected"; ?>>
                    Standard 3
                </option>

                <option
                <?php if($standard == "Standard 4") echo "selected"; ?>>
                    Standard 4
                </option>

                <option
                <?php if($standard == "Standard 5") echo "selected"; ?>>
                    Standard 5
                </option>

                <option
                <?php if($standard == "Standard 6") echo "selected"; ?>>
                    Standard 6
                </option>

            </select>

            <button type="submit" class="searchBtn">
                Search
            </button>

            <a href="a_leaderboard.php" class="resetBtn">
                Reset
            </a>

        </form>

    </div>

    <div class="container">

        <div class="top-card">

            <h2>🏆 TOP ACHIEVER</h2>

            <?php

            if($top)
            {

            ?>

            <p>
                Student ID : <?php echo $top['user_code']; ?>
            </p>

            <p>
                Username : <?php echo $top['username']; ?>
            </p>

            <p>
                Standard : <?php echo $top['standard']; ?>
            </p>

            <div class="points">
                <?php echo $top['total_points']; ?> ⭐
            </div>

            <p>
                Current Champion
            </p>

            <?php

            }
            else
            {
                echo "<p>No student found</p>";
            }

            ?>

        </div>

    </div>

    <div class="container">

        <h1>Ranking List</h1>

        <table>

            <tr>
                <th>Rank</th>
                <th>Student ID</th>
                <th>Username</th>
                <th>Standard</th>
                <th>Total Points</th>
            </tr>

            <?php

            $rank = 1;

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {
                    if($rank == 1)
                    {
                        $rankDisplay = "🥇 1";
                    }
                    else if($rank == 2)
                    {
                        $rankDisplay = "🥈 2";
                    }
                    else if($rank == 3)
                    {
                        $rankDisplay = "🥉 3";
                    }
                    else
                    {
                        $rankDisplay = $rank;
                    }

            ?>

            <tr>

                <td class="rank">
                    <?php echo $rankDisplay; ?>
                </td>

                <td>
                    <?php echo $row['user_code']; ?>
                </td>

                <td>
                    <?php echo $row['username']; ?>
                </td>

                <td>
                    <?php echo $row['standard']; ?>
                </td>

                <td>
                    <?php echo $row['total_points']; ?>
                </td>

            </tr>

            <?php

                    $rank++;
                }
            }
            else
            {
                echo "
                <tr>
                    <td colspan='5'>
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

</script>

</body>
</html>