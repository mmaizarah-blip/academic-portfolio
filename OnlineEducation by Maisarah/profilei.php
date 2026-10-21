<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "db.php";

if(!isset($_SESSION['user_code']) || strtolower($_SESSION['role']) != 'instructor') {
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];

$message = "";

$userSql = "SELECT * FROM users WHERE user_code='$userCode'";
$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

if(isset($_POST['update_profile']))
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $avatar = $_POST['avatar'];

    $sql = "UPDATE users 
            SET username='$username',
                email='$email',
                phone='$phone',
                avatar='$avatar'
            WHERE user_code='$userCode'";

    if($conn->query($sql)) {
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $message = "Profile updated successfully!";
    } else {
        $message = "Failed to update profile.";
    }
}

if(isset($_POST['change_password']))
{
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    $userSql = "SELECT * FROM users WHERE user_code='$userCode'";
    $userResult = $conn->query($userSql);
    $user = $userResult->fetch_assoc();

    $passwordCorrect = false;

    if(substr($user['password'], 0, 4) == '$2y$') {
        if(password_verify($oldPassword, $user['password'])) {
            $passwordCorrect = true;
        }
    } else {
        if($oldPassword == $user['password']) {
            $passwordCorrect = true;
        }
    }

    if(!$passwordCorrect) {
        $message = "Old password is incorrect.";
    }
    else if($newPassword != $confirmPassword) {
        $message = "New password and confirm password do not match.";
    }
    else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users 
                SET password='$hashedPassword'
                WHERE user_code='$userCode'";

        if($conn->query($sql)) {
            $message = "Password changed successfully!";
        } else {
            $message = "Failed to change password.";
        }
    }
}

$userSql = "SELECT * FROM users WHERE user_code='$userCode'";
$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

$avatar = !empty($user['avatar']) ? $user['avatar'] : "avatar1.jpg";
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Instructor Profile</title>

<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="sidebar.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Comic Neue', cursive;
    background: linear-gradient(135deg, #E8F0F8 0%, #D1E2F0 100%);
    min-height: 100vh;
    display: flex;
}


.main {
    margin-left: 270px;
    width: calc(100% - 270px);
    padding: 30px 35px;
}

.header {
    background: white;
    border-radius: 30px;
    padding: 22px 30px;
    margin-bottom: 25px;
    box-shadow: 0 8px 20px rgba(30,58,95,0.08);
}

.header h1 {
    font-family: 'Fredoka One', cursive;
    color: #1E3A5F;
}

.header p {
    color: #64748B;
    margin-top: 6px;
    font-weight: bold;
}

.message {
    background: #E8F0F8;
    color: #1E3A5F;
    padding: 13px 20px;
    border-radius: 20px;
    margin-bottom: 20px;
    font-weight: 800;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.card {
    background: white;
    border-radius: 30px;
    padding: 28px;
    box-shadow: 0 8px 20px rgba(30,58,95,0.08);
}

.card h2 {
    color: #1E3A5F;
    margin-bottom: 20px;
    font-family: 'Fredoka One', cursive;
}

.current-avatar {
    text-align: center;
    margin-bottom: 20px;
}

.current-avatar img {
    width: 110px;
    height: 110px;
    border-radius: 50%;
    background: #E8F0F8;
    padding: 5px;
    object-fit: cover;
    border: 4px solid #4A90E2;
}

label {
    display: block;
    color: #1E3A5F;
    font-weight: 800;
    margin-bottom: 7px;
}

input {
    width: 100%;
    padding: 13px 15px;
    margin-bottom: 18px;
    border: 2px solid #D1E2F0;
    border-radius: 18px;
    outline: none;
    font-family: 'Comic Neue', cursive;
    font-size: 1rem;
    background: #F8FBFF;
}

input:focus {
    border-color: #4A90E2;
    background: white;
}

.avatar-options {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.avatar-options label {
    cursor: pointer;
}

.avatar-options input {
    display: none;
}

.avatar-options img {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    background: #E8F0F8;
    padding: 4px;
    border: 3px solid transparent;
    object-fit: cover;
}

.avatar-options input:checked + img {
    border-color: #4A90E2;
}

button {
    background: linear-gradient(135deg, #4A90E2, #2B5B84);
    color: white;
    border: none;
    padding: 12px 22px;
    border-radius: 30px;
    font-family: 'Comic Neue', cursive;
    font-weight: 800;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    transform: scale(1.04);
}

@media(max-width: 900px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
}

@media(max-width: 800px) {
    .sidebar {
        display: none;
    }

    .main {
        margin-left: 0;
        width: 100%;
    }
}
</style>
</head>

<body>

<div class="sidebar">
    <div class="logoBox">
        <img src="logo.png">
        <div>
            <h2>OneStepAhead</h2>
            <p>Instructor Panel</p>
        </div>
    </div>

    <div class="nav">
        <a href="I_home.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="manage_questions.php"><i class="fas fa-question-circle"></i> Manage Questions</a>
        <a href="add_question.php"><i class="fas fa-plus-circle"></i> Add Question</a>
			<a href="manage_videos.php"><i class="fas fa-video"></i> Manage Videos</a>
		<a href="addVideo.php"><i class="fas fa-video"></i>  Add Video Lessons</a>
        <a href="view_students.php"><i class="fas fa-users"></i> View Students</a>
        <a href="progressI.php"><i class="fas fa-chart-line"></i> Progress</a>
        <a href="I_profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main">

    <div class="header">
        <h1>Instructor Profile 👤</h1>
        <p>Update your information, avatar and password.</p>
    </div>

    <?php if($message != ""): ?>
        <div class="message">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="profile-grid">

        <div class="card">
            <h2>Profile Details</h2>

            <div class="current-avatar">
                <img src="<?php echo htmlspecialchars($avatar); ?>">
                <p><b><?php echo htmlspecialchars($user['username']); ?></b></p>
                <small><?php echo htmlspecialchars($user['role']); ?></small>
            </div>

            <form method="POST">

                <label>Choose Avatar</label>
                <div class="avatar-options">
                    <label>
                        <input type="radio" name="avatar" value="avatar1.jpg" <?php if($avatar=="avatar1.jpg") echo "checked"; ?>>
                        <img src="avatar1.jpg">
                    </label>

                    <label>
                        <input type="radio" name="avatar" value="avatar2.jpg" <?php if($avatar=="avatar2.jpg") echo "checked"; ?>>
                        <img src="avatar2.jpg">
                    </label>

                    <label>
                        <input type="radio" name="avatar" value="avatar3.jpg" <?php if($avatar=="avatar3.jpg") echo "checked"; ?>>
                        <img src="avatar3.jpg">
                    </label>

                    <label>
                        <input type="radio" name="avatar" value="avatar4.jpg" <?php if($avatar=="avatar4.jpg") echo "checked"; ?>>
                        <img src="avatar4.jpg">
                    </label>
                </div>

                <label>Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label>Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">

                <label>Subject Taught</label>
                <input type="text" value="<?php echo htmlspecialchars($user['subject_taught']); ?>" readonly>

                <button type="submit" name="update_profile">
                    <i class="fas fa-save"></i> Update Profile
                </button>
            </form>
        </div>

        <div class="card">
            <h2>Change Password</h2>

            <form method="POST">
                <label>Old Password</label>
                <input type="password" name="old_password" required>

                <label>New Password</label>
                <input type="password" name="new_password" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>

                <button type="submit" name="change_password">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </form>
        </div>

    </div>

</div>

</body>
</html>