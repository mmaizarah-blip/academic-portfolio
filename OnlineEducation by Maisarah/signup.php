<?php
session_start();
include "db.php";

$error = '';
$success = false;
$redirectUrl = '';

if(isset($_POST['signup']))
{
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if($password != $confirm_password)
    {
        $error = "Password tidak sama!";
    }
    else if(strlen($password) < 5)
    {
        $error = "Password perlulah sekurang-kurangnya 5 aksara!";
    }
    else
    {
        $check = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $result = $conn->query($check);

        if($result->num_rows > 0)
        {
            $error = "Username atau email sudah wujud!";
        }
        else
        {
            $lastStudent =
            "SELECT user_code
            FROM users
            WHERE role='Student'
            ORDER BY user_id DESC
            LIMIT 1";

            $resultStudent = $conn->query($lastStudent);

            if($resultStudent->num_rows > 0)
            {
                $row = $resultStudent->fetch_assoc();
                $number = (int)substr($row['user_code'], 1);
                $number++;
                $userCode = "S" . str_pad($number, 3, "0", STR_PAD_LEFT);
            }
            else
            {
                $userCode = "S001";
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql =
            "INSERT INTO users
            (
                user_code,
                username,
                email,
                phone,
                role,
                standard,
                password,
                avatar
            )
            VALUES
            (
                '$userCode',
                '$username',
                '$email',
                '',
                'Student',
                'Standard 1',
                '$hashedPassword',
                'avatar.jpg'
            )";

            if($conn->query($sql))
            {
                $_SESSION['user_code'] = $userCode;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = "Student";
                $_SESSION['standard'] = "Standard 1";

                $success = true;
                $successMessage = "Registration successful! Redirecting to subscription...";
                $redirectUrl = "subscription.php";
            }
            else
            {
                $error = "Registration failed: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Sign Up | One Step Ahead</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">

<style>
*
{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body
{
    font-family: 'Comic Neue', cursive;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
	overflow-y:auto;
    overflow-x: hidden;
    position: relative;

    background:
    radial-gradient(circle at 15% 20%, rgba(255,255,255,0.18) 1px, transparent 3px),
    radial-gradient(circle at 80% 25%, rgba(255,255,255,0.15) 1px, transparent 3px),
    radial-gradient(circle at 45% 70%, rgba(255,255,255,0.18) 1px, transparent 3px),
    linear-gradient(135deg, #020617 0%, #0f172a 35%, #1e3a8a 70%, #2563eb 100%);
}

body::before
{
    content: "";
    position: absolute;
    inset: 0;
    background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(white 1px, transparent 1px);
    background-size: 80px 80px, 130px 130px;
    background-position: 0 0, 40px 60px;
    opacity: 0.25;
}

body::after
{
    content: "";
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(96,165,250,0.35), transparent 65%);
    top: -130px;
    right: -110px;
}

.star
{
    position: absolute;
    width: 4px;
    height: 4px;
    background: white;
    border-radius: 50%;
    animation: twinkle 3s infinite ease-in-out;
    z-index: 1;
}

@keyframes twinkle
{
    0%, 100%
    {
        opacity: 0.2;
        transform: scale(1);
    }

    50%
    {
        opacity: 1;
        transform: scale(1.9);
    }
}

.floating
{
    position: absolute;
    font-size: 32px;
    opacity: 0.85;
    pointer-events: none;
    animation: floatShape 6s infinite ease-in-out;
    z-index: 1;
}

@keyframes floatShape
{
    0%, 100%
    {
        transform: translateY(0) rotate(0deg);
    }

    50%
    {
        transform: translateY(-18px) rotate(8deg);
    }
}

.signup-container
{
    width: 100%;
    max-width: 500px;
    background: rgba(255,255,255,0.96);
    border-radius: 38px;
    overflow: hidden;
    position: relative;
    z-index: 10;
    box-shadow:
    0 25px 60px rgba(0,0,0,0.35),
    0 0 40px rgba(59,130,246,0.25);
    border: 2px solid rgba(255,255,255,0.55);
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp
{
    from
    {
        opacity: 0;
        transform: translateY(35px);
    }

    to
    {
        opacity: 1;
        transform: translateY(0);
    }
}

.signup-header
{
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #3b82f6 100%);
    padding: 34px 25px 28px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.signup-header::before
{
    content: "🪐";
    position: absolute;
    top: 18px;
    left: 25px;
    font-size: 34px;
}

.signup-header::after
{
    content: "🚀";
    position: absolute;
    right: 28px;
    bottom: 18px;
    font-size: 36px;
}

.signup-logo
{
    width: 85px;
    height: 85px;
    border-radius: 50%;
    object-fit: cover;
    background: white;
    padding: 8px;
    margin-bottom: 12px;
    border: 3px solid rgba(255,255,255,0.8);
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
}

.signup-header h1
{
    font-family: 'Fredoka One', cursive;
    font-size: 1.8rem;
    margin-bottom: 6px;
    text-shadow: 2px 2px 5px rgba(0,0,0,0.25);
}

.signup-header p
{
    color: #dbeafe;
    font-size: 0.9rem;
    line-height: 1.5;
}

.space-badge
{
    display: inline-block;
    background: rgba(255,255,255,0.18);
    border: 1px solid rgba(255,255,255,0.35);
    padding: 8px 18px;
    border-radius: 40px;
    font-size: 0.82rem;
    margin-top: 14px;
    color: white;
    font-weight: 800;
}

.signup-body
{
    padding: 32px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.error-message,
.success-message
{
    padding: 13px 18px;
    border-radius: 25px;
    margin-bottom: 22px;
    font-size: 0.88rem;
    text-align: center;
    font-weight: 800;
}

.error-message
{
    background: #fef2f2;
    color: #dc2626;
    border-left: 5px solid #f87171;
}

.success-message
{
    background: #ecfdf5;
    color: #16a34a;
    border-left: 5px solid #4ade80;
}

.role-section
{
    margin-bottom: 22px;
}

.role-section label,
.form-group label
{
    display: block;
    margin-bottom: 8px;
    color: #1e3a8a;
    font-weight: 800;
    font-size: 1rem;
}

.role-section label i,
.form-group label i
{
    margin-right: 10px;
    color: #2563eb;
}

.role-card
{
    background: #eff6ff;
    border: 2px solid #bfdbfe;
    border-radius: 24px;
    padding: 18px;
    text-align: center;
    color: #1e3a8a;
    font-weight: 800;
}

.role-card i
{
    font-size: 2.3rem;
    color: #2563eb;
    margin-bottom: 8px;
}

.role-card p
{
    color: #64748b;
    font-size: 0.8rem;
}

.role-card input
{
    display: none;
}

.admin-note
{
    text-align: center;
    font-size: 0.78rem;
    color: #64748b;
    margin-top: 10px;
    font-weight: 700;
}

.form-group
{
    margin-bottom: 18px;
}

.input-wrapper
{
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper input
{
    width: 100%;
    padding: 14px 48px 14px 20px;
    border: 2px solid #bfdbfe;
    border-radius: 60px;
    font-size: 1rem;
    outline: none;
    background: #f8fbff;
    font-family: 'Comic Neue', cursive;
    font-weight: 700;
    color: #0f172a;
    transition: 0.3s;
}

.input-wrapper input:focus
{
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 5px rgba(37,99,235,0.15);
}

.input-wrapper input::placeholder
{
    color: #94a3b8;
    font-weight: 500;
}

.toggle-password
{
    position: absolute;
    right: 18px;
    color: #2563eb;
    cursor: pointer;
}

.signup-btn
{
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: white;
    border: none;
    border-radius: 60px;
    font-size: 1.08rem;
    font-weight: bold;
    font-family: 'Fredoka One', cursive;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 8px;
    box-shadow: 0 7px 0 #1e40af;
}

.signup-btn:hover
{
    transform: translateY(-3px);
    box-shadow: 0 10px 0 #1e40af;
}

.signup-btn:active
{
    transform: translateY(3px);
    box-shadow: 0 4px 0 #1e40af;
}

.login-link
{
    text-align: center;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 2px solid #dbeafe;
    font-size: 0.92rem;
    color: #1e3a8a;
    font-weight: 800;
}

.login-link a
{
    color: #2563eb;
    text-decoration: none;
    font-weight: 900;
}

.login-link a:hover
{
    color: #1e40af;
    text-decoration: underline;
}

.loading-overlay
{
    position: fixed;
    inset: 0;
    background: linear-gradient(135deg, #020617, #1e3a8a, #2563eb);
    z-index: 1000;
    display: none;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}

.loading-overlay.active
{
    display: flex;
}

.loader i
{
    font-size: 4rem;
    margin-bottom: 18px;
}

.loader h3
{
    font-family: 'Fredoka One', cursive;
    font-size: 1.6rem;
}

.loader p
{
    margin-top: 8px;
    color: #dbeafe;
}

@media(max-width: 520px)
{
    .signup-container
    {
        max-width: 95%;
        border-radius: 30px;
    }

    .signup-body
    {
        padding: 28px 24px 34px;
    }

    .signup-header h1
    {
        font-size: 1.4rem;
    }
}
</style>
</head>

<body>

<div class="star" style="top: 12%; left: 12%; animation-delay: 0s;"></div>
<div class="star" style="top: 18%; left: 80%; animation-delay: 1s;"></div>
<div class="star" style="top: 70%; left: 20%; animation-delay: 1.6s;"></div>
<div class="star" style="top: 82%; left: 75%; animation-delay: 2s;"></div>
<div class="star" style="top: 40%; left: 8%; animation-delay: 0.7s;"></div>
<div class="star" style="top: 35%; left: 92%; animation-delay: 2.4s;"></div>

<div class="floating" style="top: 10%; left: 5%; animation-delay: 0s;">🌙</div>
<div class="floating" style="top: 20%; left: 88%; animation-delay: 1s;">🪐</div>
<div class="floating" style="top: 78%; left: 8%; animation-delay: 2s;">🚀</div>
<div class="floating" style="top: 75%; left: 86%; animation-delay: 1.5s;">🛸</div>
<div class="floating" style="top: 48%; left: 4%; animation-delay: 2.5s;">✨</div>
<div class="floating" style="top: 45%; left: 92%; animation-delay: 0.5s;">⭐</div>

<div class="loading-overlay" id="loadingOverlay">
    <div class="loader">
        <i class="fas fa-spinner fa-pulse"></i>
        <h3>Redirecting...</h3>
        <p>Preparing your learning mission.</p>
    </div>
</div>

<div class="signup-container" id="signupContainer">

    <div class="signup-header">
        <img src="logo.png" alt="Logo" class="signup-logo" onerror="this.src='https://via.placeholder.com/85?text=OSA'">

        <h1>🚀 Join One Step Ahead 🚀</h1>

        <p>
            Start your learning journey beyond the stars.
        </p>

        <div class="space-badge">
            🌌 Create Student Account
        </div>
    </div>

    <div class="signup-body">

        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($success && !empty($redirectUrl)): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <?php echo $successMessage; ?>
            </div>

            <script>
                setTimeout(function()
                {
                    document.getElementById("loadingOverlay").classList.add("active");

                    setTimeout(function()
                    {
                        window.location.href = "<?php echo $redirectUrl; ?>";
                    }, 900);

                }, 1200);
            </script>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="role-section">
                <label>
                    <i class="fas fa-users"></i>
                    Account Type
                </label>

                <div class="role-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>Student</h4>
                    <p>I want to learn Math and Science</p>
                    <input type="radio" name="role" value="student" checked>
                </div>

                <div class="admin-note">
                    <i class="fas fa-shield-alt"></i>
                    Admin and instructor accounts are created by administrator only.
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-user-tag"></i>
                    Username
                </label>

                <div class="input-wrapper">
                    <input
                    type="text"
                    name="username"
                    required
                    placeholder="Choose username">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-envelope"></i>
                    Email
                </label>

                <div class="input-wrapper">
                    <input
                    type="email"
                    name="email"
                    required
                    placeholder="Enter your email">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-lock"></i>
                    Password
                </label>

                <div class="input-wrapper">
                    <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    placeholder="Minimum 5 characters">

                    <i class="fas fa-eye toggle-password" onclick="togglePassword('password', this)"></i>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-check-circle"></i>
                    Confirm Password
                </label>

                <div class="input-wrapper">
                    <input
                    type="password"
                    name="confirm_password"
                    id="confirm_password"
                    required
                    placeholder="Confirm your password">

                    <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password', this)"></i>
                </div>
            </div>

            <button type="submit" name="signup" class="signup-btn">
                🚀 SIGN UP
            </button>

        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Log in here</a>
        </div>

    </div>

</div>

<script>
function togglePassword(fieldId, icon)
{
    const field = document.getElementById(fieldId);

    if(field.type === "password")
    {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
    else
    {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>