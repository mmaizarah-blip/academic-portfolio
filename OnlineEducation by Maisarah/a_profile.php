<?php

session_start();

include "db.php";

$currentAdminCode =
$_SESSION['user_code'];

$sql =
"SELECT *
FROM users
WHERE user_code='$currentAdminCode'";

$result =
$conn->query($sql);

$admin =
$result->fetch_assoc();

$allAdmins =
"SELECT *
FROM users
WHERE role='Admin'
ORDER BY user_code";

$adminResult =
$conn->query($allAdmins);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">
<title>Admin Profile</title>

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
    margin-bottom:25px;
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

.profile-card
{
    background:#faf5ff;
    padding:25px;
    border-radius:18px;
    margin-bottom:20px;
}

.profile-card p
{
    font-size:17px;
    margin-bottom:14px;
}

.profile-card b
{
    color:purple;
}

.btn
{
    background:linear-gradient(135deg,#6a0dad,#b145d9);
    color:white;
    padding:12px 18px;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
    display:inline-block;
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

	<a href="report.php">📊 Report</a>
	<a href="subscription_report.php">💳 Subscription Report</a>
	
    <a href="a_profile.php" class="active">👤 Profile</a>

    <a href="login.php" class="logout">🚪 Logout</a>

</div>

<div class="main-content">

    <div class="container">

        <div class="header-row">

            <h1>My Profile</h1>

            <a class="btn" href="edit_admin.php">
                Edit My Profile
            </a>

        </div>

        <div class="profile-card">

            <p>
                <b>Admin ID:</b>
                <?php echo $admin['user_code']; ?>
            </p>

            <p>
                <b>Username:</b>
                <?php echo $admin['username']; ?>
            </p>

            <p>
                <b>Email:</b>
                <?php echo $admin['email']; ?>
            </p>

            <p>
                <b>Phone:</b>
                <?php echo $admin['phone']; ?>
            </p>

        </div>

    </div>

    <div class="container">

        <div class="header-row">

            <h1>Other Admins</h1>

            <a class="btn" href="add_admin.php">
                + Add Admin
            </a>

        </div>

        <table>

            <tr>
                <th>Admin ID</th>
                <th>Username</th>
                <th>Email</th>
            </tr>

            <?php

            if($adminResult->num_rows > 0)
            {
                while($row = $adminResult->fetch_assoc())
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

            </tr>

            <?php

                }
            }
            else
            {
                echo "
                <tr>
                    <td colspan='3'>
                        No admin found
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
    openDropdown("none");
}

</script>

</body>
</html>