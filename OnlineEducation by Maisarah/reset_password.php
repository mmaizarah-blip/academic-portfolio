<?php
session_start();
include "db.php";

// Check if user is allowed to reset password
if(!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = '';
$success = '';

if(isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $user_id = $_SESSION['reset_user_id'];
    
    if($new_password != $confirm_password) {
        $error = "Password tidak sama! / Passwords do not match!";
    } elseif(strlen($new_password) < 5) {
        $error = "Password perlulah sekurang-kurangnya 5 aksara! / Password must be at least 5 characters!";
    } else {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        
        $updateSql = "UPDATE users SET password='$hashedPassword' WHERE user_id='$user_id'";
        
        if($conn->query($updateSql)) {
            $success = "Password berjaya ditukar! Sila login semula.";
            // Clear reset session
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_username']);
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000);
            </script>";
        } else {
            $error = "Gagal menukar password. Cuba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | One Step Ahead</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;700;800&family=Fredoka+One&display=swap" rel="stylesheet">
    <style>
        *{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Comic Neue',cursive;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:25px;
    overflow-x:hidden;

    background:
    radial-gradient(circle at 15% 20%, rgba(255,255,255,0.18) 1px, transparent 3px),
    radial-gradient(circle at 80% 25%, rgba(255,255,255,0.15) 1px, transparent 3px),
    radial-gradient(circle at 45% 70%, rgba(255,255,255,0.18) 1px, transparent 3px),
    linear-gradient(135deg,#020617 0%,#0f172a 35%,#1e3a8a 70%,#2563eb 100%);
}

.container{
    width:100%;
    max-width:470px;
    background:rgba(255,255,255,.96);
    border-radius:40px;
    overflow:hidden;
    box-shadow:
    0 25px 60px rgba(0,0,0,.35),
    0 0 40px rgba(59,130,246,.25);

    animation:slideUp .5s;
}

@keyframes slideUp{
from{
opacity:0;
transform:translateY(30px);
}
to{
opacity:1;
transform:translateY(0);
}
}

.header{

background:
linear-gradient(
135deg,
#0F172A,
#1E3A8A,
#3B82F6);

padding:35px 25px;
text-align:center;
color:white;
position:relative;
overflow:hidden;
}

.header::before{
content:"🔐";
position:absolute;
left:20px;
top:18px;
font-size:30px;
}

.header::after{
content:"✨";
position:absolute;
right:20px;
bottom:18px;
font-size:28px;
}

.header i{
font-size:55px;
margin-bottom:10px;
display:block;
}

.header h1{
font-family:'Fredoka One',cursive;
font-size:30px;
margin-bottom:8px;
}

.header p{
color:#dbeafe;
}

.body{
padding:35px;
background:white;
}

.info-box{

background:#eff6ff;
border-left:5px solid #2563eb;
padding:18px;
border-radius:18px;
margin-bottom:25px;
text-align:center;
color:#1e3a8a;
font-weight:bold;
}

.form-group{
margin-bottom:22px;
}

.form-group label{
display:block;
margin-bottom:8px;
font-weight:800;
color:#1e3a8a;
}

.form-group label i{
margin-right:8px;
color:#2563eb;
}

.input-group{
position:relative;
}

.input-group input{

width:100%;
padding:15px 18px;
border:2px solid #bfdbfe;
border-radius:40px;
background:#f8fbff;
font-size:15px;
font-family:'Comic Neue',cursive;
font-weight:bold;
transition:.3s;
outline:none;

}

.input-group input:focus{

border-color:#2563eb;
background:white;
box-shadow:0 0 0 5px rgba(37,99,235,.12);

}

.btn-primary{

width:100%;
padding:15px;
border:none;
border-radius:40px;
background:linear-gradient(135deg,#2563eb,#3b82f6);
color:white;
font-family:'Fredoka One',cursive;
font-size:18px;
cursor:pointer;
box-shadow:0 6px 0 #1e40af;
transition:.3s;

}

.btn-primary:hover{

transform:translateY(-3px);
box-shadow:0 10px 0 #1e40af;

}

.btn-primary:active{

transform:translateY(2px);

}

.btn-secondary{

display:block;
width:100%;
margin-top:15px;
padding:14px;
border-radius:40px;
border:2px solid #2563eb;
color:#2563eb;
text-align:center;
text-decoration:none;
font-weight:bold;
transition:.3s;

}

.btn-secondary:hover{

background:#2563eb;
color:white;

}

.error-message{

background:#fef2f2;
color:#dc2626;
padding:14px;
border-radius:20px;
margin-bottom:20px;
font-weight:bold;
text-align:center;

}

.success-message{

background:#dcfce7;
color:#15803d;
padding:14px;
border-radius:20px;
margin-bottom:20px;
font-weight:bold;
text-align:center;

}

.footer{

text-align:center;
padding:16px;
background:#eff6ff;
color:#1e3a8a;
font-size:12px;
font-weight:bold;

}
    </style>
</head>
<body>

<div class="container">
    <div class="header">
    <i class="fas fa-user-shield"></i>
    <h1>Reset Password</h1>
    <p>Create your new secure password</p>
</div>
    
    <div class="body">
        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <div class="info-box">
            <i class="fas fa-user"></i> Reset password untuk: <strong><?php echo htmlspecialchars($_SESSION['reset_username'] ?? 'User'); ?></strong><br>
            <small><?php echo htmlspecialchars($_SESSION['reset_email'] ?? ''); ?></small>
        </div>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Password Baru / New Password</label>
                <div class="input-group">
                    <input type="password" name="new_password" placeholder="Masukkan password baru (min 5 aksara)" required>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-check-circle"></i> Sahkan Password Baru / Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" placeholder="Taip password sekali lagi" required>
                </div>
            </div>
            
            <button type="submit" name="reset_password" class="btn-primary">✅ Reset Password</button>
            <a href="login.php" class="btn-secondary">← Back to Login</a>
        </form>
    </div>
    
    <div class="footer">
        <small>© 2024 One Step Ahead | Education Platform</small>
    </div>
</div>

</body>
</html>