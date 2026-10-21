<?php

session_start();

include "db.php";

$search = "";

if(isset($_GET['search']))
{
    $search = $_GET['search'];
}

$sql =
"SELECT *
FROM users
WHERE role='Student'
AND
(
    user_code LIKE '%$search%'
    OR username LIKE '%$search%'
)
ORDER BY user_code";

$result =
$conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Student Profile</title>

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

.container
{
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.header-row
{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

h1
{
    color:purple;
}

.addBtn
{
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    padding:12px 18px;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
}

.addBtn:hover
{
    opacity:0.9;
}

table
{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
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

.editBtn
{
    background:orange;
    color:white;
    padding:7px 12px;
    text-decoration:none;
    border-radius:7px;
    font-size:14px;
}

.deleteBtn
{
    background:red;
    color:white;
    padding:7px 12px;
    text-decoration:none;
    border-radius:7px;
    font-size:14px;
}

.action-box
{
    display:flex;
    justify-content:center;
    gap:8px;
}

.search-box
{
    background:white;
    padding:20px;
    border-radius:20px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    margin-bottom:25px;
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
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    border:none;
    padding:12px 18px;
    border-radius:10px;
    font-weight:bold;
    cursor:pointer;
}

.resetBtn
{
    background:#eeeeee;
    color:#333;
    text-decoration:none;
    padding:12px 18px;
    border-radius:10px;
    font-weight:bold;
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

            <a href="s_profile.php" class="active">
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

	<a href="report.php">📊 Report</a>
	<a href="subscription_report.php">💳 Subscription Report</a>
	
    <a href="a_profile.php">👤 Profile</a>

    <a href="login.php" class="logout">🚪 Logout</a>

</div>

<div class="main-content">

    <div class="search-box">

        <form method="GET">

            <input
            type="text"
            name="search"
            placeholder="Search by student ID or name..."
            value="<?php echo $search; ?>">

            <button type="submit">
                Search
            </button>

            <a href="s_profile.php" class="resetBtn">
                Reset
            </a>

        </form>

    </div>

    <div class="container">

        <div class="header-row">

            <h1>Student Profile</h1>

            <a href="add_student.php" class="addBtn">
                + Add Student
            </a>

        </div>

        <table>

            <tr>
                <th>Student ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Standard</th>
                <th>Action</th>
            </tr>

            <?php

            if($result->num_rows > 0)
            {
                while($row = $result->fetch_assoc())
                {

            ?>

            <tr>

                <td>
                    <?php echo $row['user_code']; ?>
                </td>

                <td>
                    <?php echo $row['username']; ?>
                </td>

                <td>
                    <?php echo $row['email']; ?>
                </td>

                <td>
                    <?php echo $row['phone']; ?>
                </td>

                <td>
                    <?php echo $row['standard']; ?>
                </td>

                <td>

                    <div class="action-box">

                        <a
                        class="editBtn"
                        href="edit_student.php?id=<?php echo $row['user_code']; ?>">
                            Edit
                        </a>

                        <a
                        class="deleteBtn"
                        href="delete_student.php?id=<?php echo $row['user_code']; ?>"
                        onclick="return confirm('Delete this student?')">
                            Delete
                        </a>

                    </div>

                </td>

            </tr>

            <?php

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

</script>

</body>
</html>