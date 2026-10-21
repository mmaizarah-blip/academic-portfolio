<?php
session_start();
include "db.php";

$error = "";

if(isset($_POST['login']))
{
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0)
    {
        $user = mysqli_fetch_assoc($result);

        $password_correct = false;

        if(substr($user['password'], 0, 4) == '$2y$')
        {
            if(password_verify($password, $user['password']))
            {
                $password_correct = true;
            }
        }
        else
        {
            if($password == $user['password'])
            {
                $password_correct = true;
            }
        }

        if($password_correct)
        {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_code'] = $user['user_code'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['standard'] = $user['standard'];

            $role = strtolower(trim($user['role']));

            if($role == 'admin')
            {
                header("Location: A_home.php");
                exit();
            }
            else if($role == 'instructor')
            {
                header("Location: I_home.php");
                exit();
            }
            else if($role == 'student')
{
    $userCode = $user['user_code'];

    $check =
    "SELECT *
    FROM subscriptions
    WHERE user_code='$userCode'
    AND payment_status='Paid'
    LIMIT 1";

    $checkResult = mysqli_query($conn,$check);

    if(mysqli_num_rows($checkResult)>0)
    {
        header("Location: home.php");
    }
    else
    {
        header("Location: subscription.php");
    }

    exit();
}
            else
            {
                echo "Role tidak dikenali: " . $user['role'];
            }
        }
        else
        {
            $error = "Password salah! / Wrong password!";
        }
    }
    else
    {
        $error = "Email tidak dijumpai! / Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Log Masuk | One Step Ahead</title>

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
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;

    background:
    radial-gradient(circle at 15% 20%, rgba(255,255,255,0.18) 1px, transparent 3px),
    radial-gradient(circle at 80% 25%, rgba(255,255,255,0.15) 1px, transparent 3px),
    radial-gradient(circle at 45% 70%, rgba(255,255,255,0.18) 1px, transparent 3px),
    radial-gradient(circle at 70% 85%, rgba(255,255,255,0.12) 1px, transparent 3px),
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
    z-index: 0;
}

body::after
{
    content: "";
    position: absolute;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(96,165,250,0.35), transparent 65%);
    top: -140px;
    right: -120px;
    z-index: 0;
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

.login-container
{
    width: 100%;
    max-width: 460px;
    background: rgba(255,255,255,0.96);
    border-radius: 38px;
    overflow: hidden;
    position: relative;
    z-index: 10;
    box-shadow:
    0 25px 60px rgba(0,0,0,0.35),
    0 0 40px rgba(59,130,246,0.25);
    animation: slideUp 0.6s ease-out;
    border: 2px solid rgba(255,255,255,0.55);
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

.login-header
{
    background:
    linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #3b82f6 100%);
    padding: 34px 25px 28px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.login-header::before
{
    content: "🪐";
    position: absolute;
    top: 18px;
    left: 25px;
    font-size: 34px;
    opacity: 0.9;
}

.login-header::after
{
    content: "🚀";
    position: absolute;
    right: 28px;
    bottom: 18px;
    font-size: 36px;
    opacity: 0.9;
}

.logo-wrapper
{
    margin-bottom: 12px;
}

.login-logo
{
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    background: white;
    padding: 8px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    border: 3px solid rgba(255,255,255,0.8);
}

.login-header h1
{
    font-family: 'Fredoka One', cursive;
    font-size: 1.8rem;
    letter-spacing: 1px;
    margin-bottom: 6px;
    color: white;
    text-shadow: 2px 2px 5px rgba(0,0,0,0.25);
}

.login-header p
{
    font-size: 0.9rem;
    color: #dbeafe;
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
    backdrop-filter: blur(8px);
}

.login-body
{
    padding: 35px 32px 38px;
    background:
    linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.form-group
{
    margin-bottom: 22px;
}

.form-group label
{
    display: block;
    margin-bottom: 8px;
    color: #1e3a8a;
    font-weight: 800;
    font-size: 1rem;
}

.form-group label i
{
    margin-right: 10px;
    color: #2563eb;
}

.input-group
{
    position: relative;
    display: flex;
    align-items: center;
}

.input-group input
{
    width: 100%;
    padding: 14px 50px 14px 20px;
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

.input-group input:focus
{
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 5px rgba(37,99,235,0.15);
}

.input-group input::placeholder
{
    color: #94a3b8;
    font-weight: 500;
}

.forgot-link
{
    text-align: right;
    margin-top: 10px;
}

.forgot-link a
{
    font-size: 0.83rem;
    color: #2563eb;
    text-decoration: none;
    font-weight: 800;
}

.forgot-link a:hover
{
    color: #1e3a8a;
    text-decoration: underline;
}

.login-btn
{
    width: 100%;
    padding: 15px;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: white;
    border: none;
    border-radius: 60px;
    font-size: 1.18rem;
    font-weight: bold;
    font-family: 'Fredoka One', cursive;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 10px;
    box-shadow: 0 7px 0 #1e40af;
}

.login-btn small
{
    font-size: 0.72rem;
    font-family: 'Comic Neue', cursive;
    font-weight: 800;
}

.login-btn:hover
{
    transform: translateY(-3px);
    box-shadow: 0 10px 0 #1e40af;
}

.login-btn:active
{
    transform: translateY(3px);
    box-shadow: 0 4px 0 #1e40af;
}

.error-message
{
    background: #fef2f2;
    color: #dc2626;
    padding: 13px 18px;
    border-radius: 25px;
    margin-bottom: 25px;
    font-size: 0.88rem;
    text-align: center;
    border-left: 5px solid #f87171;
    font-weight: 800;
    animation: shake 0.4s ease;
}

@keyframes shake
{
    0%, 100%
    {
        transform: translateX(0);
    }

    25%
    {
        transform: translateX(-5px);
    }

    75%
    {
        transform: translateX(5px);
    }
}

.register-link
{
    text-align: center;
    margin-top: 28px;
    padding-top: 20px;
    border-top: 2px solid #dbeafe;
    font-size: 0.92rem;
    color: #1e3a8a;
    font-weight: 800;
}

.register-link a
{
    color: #2563eb;
    text-decoration: none;
    font-weight: 900;
}

.register-link a:hover
{
    color: #1e40af;
    text-decoration: underline;
}

.login-footer
{
    text-align: center;
    padding: 15px;
    background: #eff6ff;
    font-size: 0.72rem;
    color: #1e3a8a;
    font-weight: 700;
}

@media(max-width: 480px)
{
    .login-container
    {
        max-width: 95%;
        border-radius: 30px;
    }

    .login-body
    {
        padding: 28px 24px 34px;
    }

    .login-logo
    {
        width: 75px;
        height: 75px;
    }

    .login-header h1
    {
        font-size: 1.4rem;
    }

    .login-btn
    {
        font-size: 1rem;
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

<div class="login-container">

    <div class="login-header">

        <div class="logo-wrapper">
            <img src="logo.png" alt="Logo" class="login-logo" onerror="this.src='https://via.placeholder.com/85?text=OSA'">
        </div>

        <h1>🚀 One Step Ahead 🚀</h1>

        <p>
            Explore Knowledge Beyond The Stars<br>
            <small>Learn Math & Science Through Fun Missions</small>
        </p>

        <div class="space-badge">
            🌌 Start Your Learning Mission
        </div>

    </div>

    <div class="login-body">

        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>
                    <i class="fas fa-envelope"></i>
                    Email
                </label>

                <div class="input-group">
                    <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    required
                    autofocus>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-lock"></i>
                    Password
                </label>

               <div class="input-group">
    <input
    type="password"
    id="password"
    name="password"
    placeholder="Enter your password"
    required>

    <i
    class="fas fa-eye"
    id="togglePassword"
    style="
        position:absolute;
        right:18px;
        cursor:pointer;
        color:#2563eb;
        font-size:16px;
    ">
    </i>
</div>

                <div class="forgot-link">
                    <a href="forgot_password.php">
                        <i class="fas fa-key"></i>
                        Forgot password?
                    </a>
                </div>
            </div>

            <button type="submit" name="login" class="login-btn">
                🚀 LOG IN 🚀<br>
                <small>START MISSION</small>
            </button>

        </form>

        <div class="register-link">
            New explorer?
            <a href="signup.php">
                SIGN UP NOW 🌟
            </a>
        </div>

    </div>

    <div class="login-footer">
        <small>© 2024 One Step Ahead | Space Learning Platform</small>
    </div>

</div>
<script>
document.getElementById("togglePassword").onclick = function()
{
    var passwordInput = document.getElementById("password");

    if(passwordInput.type === "password")
    {
        passwordInput.type = "text";
        this.classList.remove("fa-eye");
        this.classList.add("fa-eye-slash");
    }
    else
    {
        passwordInput.type = "password";
        this.classList.remove("fa-eye-slash");
        this.classList.add("fa-eye");
    }
}
</script>

</body>
</html>