<?php
session_start();
include "db.php";

$error = '';

if(isset($_POST['reset_password']))
{
    $email = trim($_POST['email']);

    $sql = "SELECT user_id, username, email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0)
    {
        $user_data = $result->fetch_assoc();

        $_SESSION['reset_user_id'] = $user_data['user_id'];
        $_SESSION['reset_email'] = $user_data['email'];
        $_SESSION['reset_username'] = $user_data['username'];

        header("Location: reset_password.php");
        exit();
    }
    else
    {
        $error = "Email tidak ditemui dalam sistem kami! / Email not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Forgot Password | One Step Ahead</title>

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

.container
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

.header
{
    background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #3b82f6 100%);
    padding: 36px 25px 30px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.header::before
{
    content: "🪐";
    position: absolute;
    top: 18px;
    left: 25px;
    font-size: 34px;
}

.header::after
{
    content: "🚀";
    position: absolute;
    right: 28px;
    bottom: 18px;
    font-size: 36px;
}

.header i
{
    font-size: 3rem;
    margin-bottom: 12px;
    color: #dbeafe;
}

.header h1
{
    font-family: 'Fredoka One', cursive;
    font-size: 1.55rem;
    margin-bottom: 8px;
    text-shadow: 2px 2px 5px rgba(0,0,0,0.25);
}

.header p
{
    font-size: 0.9rem;
    color: #dbeafe;
    line-height: 1.5;
}

.body
{
    padding: 35px 32px 38px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
}

.error
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

.form-group input
{
    width: 100%;
    padding: 14px 20px;
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

.form-group input:focus
{
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 5px rgba(37,99,235,0.15);
}

.form-group input::placeholder
{
    color: #94a3b8;
    font-weight: 500;
}

.btn
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
    box-shadow: 0 7px 0 #1e40af;
}

.btn:hover
{
    transform: translateY(-3px);
    box-shadow: 0 10px 0 #1e40af;
}

.btn:active
{
    transform: translateY(3px);
    box-shadow: 0 4px 0 #1e40af;
}

.info-note
{
    background: #eff6ff;
    color: #1e3a8a;
    padding: 14px 16px;
    border-radius: 22px;
    margin-top: 22px;
    font-size: 0.82rem;
    text-align: center;
    font-weight: 800;
    border: 2px solid #dbeafe;
}

.back-link
{
    text-align: center;
    margin-top: 24px;
    padding-top: 18px;
    border-top: 2px solid #dbeafe;
}

.back-link a
{
    color: #2563eb;
    text-decoration: none;
    font-weight: 900;
}

.back-link a:hover
{
    color: #1e40af;
    text-decoration: underline;
}

.footer
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
    .container
    {
        max-width: 95%;
        border-radius: 30px;
    }

    .body
    {
        padding: 28px 24px 34px;
    }

    .header h1
    {
        font-size: 1.35rem;
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

<div class="container">

    <div class="header">
        <i class="fas fa-key"></i>
        <h1>🔐 Forgot Password?</h1>
        <p>
            Enter your email to continue your password reset mission.
        </p>
    </div>

    <div class="body">

        <?php if($error): ?>
            <div class="error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">
                <label>
                    <i class="fas fa-envelope"></i>
                    Email Address
                </label>

                <input
                type="email"
                name="email"
                required
                placeholder="Enter your email">
            </div>

            <button type="submit" name="reset_password" class="btn">
                🚀 RESET PASSWORD
            </button>

        </form>

        <div class="info-note">
            <i class="fas fa-info-circle"></i>
            Enter your registered email, then you will be directed to reset your password.
        </div>

        <div class="back-link">
            <a href="login.php">
                <i class="fas fa-arrow-left"></i>
                Back to Login
            </a>
        </div>

    </div>

    <div class="footer">
        <small>© 2024 One Step Ahead | Space Learning Platform</small>
    </div>

</div>

</body>
</html>