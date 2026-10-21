<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];
$username = $_SESSION['username'];

$check =
"SELECT *
FROM subscriptions
WHERE user_code='$userCode'
AND payment_status='Paid'
LIMIT 1";

$checkResult = $conn->query($check);

if($checkResult->num_rows > 0)
{
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Subscription | One Step Ahead</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: Arial, sans-serif;
    min-height: 100vh;
    background:
    radial-gradient(circle at 20% 20%, rgba(255,255,255,0.18) 1px, transparent 3px),
    radial-gradient(circle at 80% 30%, rgba(255,255,255,0.15) 1px, transparent 3px),
    linear-gradient(135deg, #020617, #0f172a, #1e3a8a, #2563eb);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 30px;
}

.container {
    width: 1000px;
    background: rgba(255,255,255,0.96);
    border-radius: 35px;
    padding: 35px;
    text-align: center;
    box-shadow: 0 25px 60px rgba(0,0,0,0.35);
}

h1 {
    color: #1e3a8a;
    font-size: 36px;
    margin-bottom: 10px;
}

.subtitle {
    color: #475569;
    margin-bottom: 30px;
    font-size: 17px;
}

.plans {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

.card {
    background: white;
    border: 2px solid #bfdbfe;
    border-radius: 28px;
    padding: 28px;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(37,99,235,0.18);
}

.card h2 {
    color: #1e3a8a;
    margin-bottom: 10px;
}

.price {
    font-size: 38px;
    font-weight: bold;
    color: #2563eb;
    margin: 18px 0;
}

.features {
    text-align: left;
    margin: 20px 0;
    line-height: 1.9;
    color: #334155;
}

button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 30px;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: white;
    font-weight: bold;
    cursor: pointer;
    font-size: 16px;
}

button:hover {
    background: linear-gradient(135deg, #1e40af, #2563eb);
}

.note {
    margin-top: 25px;
    color: #64748b;
    font-size: 14px;
}

@media(max-width: 900px) {
    .plans {
        grid-template-columns: 1fr;
    }

    .container {
        width: 95%;
    }
}
</style>
</head>

<body>

<div class="container">

    <h1>🚀 Choose Your Learning Plan</h1>

    <p class="subtitle">
        Hello, <b><?php echo $username; ?></b>! Please subscribe before accessing One Step Ahead.
    </p>

    <div class="plans">

        <div class="card">
            <h2>🌙 Basic Plan</h2>
            <div class="price">RM10</div>

            <div class="features">
                ✅ Access worksheets<br>
                ✅ Answer quizzes<br>
                ✅ View explanations<br>
                ❌ Video lessons<br>
                ❌ Certificate
            </div>

            <form method="POST" action="create_bill.php">
                <input type="hidden" name="plan_name" value="Basic Plan">
                <input type="hidden" name="amount" value="10.00">
                <button type="submit">Subscribe Basic</button>
            </form>
        </div>

        <div class="card">
            <h2>⭐ Standard Plan</h2>
            <div class="price">RM20</div>

            <div class="features">
                ✅ Access worksheets<br>
                ✅ Answer quizzes<br>
                ✅ View explanations<br>
                ✅ Video lessons<br>
                ❌ Certificate
            </div>

            <form method="POST" action="create_bill.php">
                <input type="hidden" name="plan_name" value="Standard Plan">
                <input type="hidden" name="amount" value="20.00">
                <button type="submit">Subscribe Standard</button>
            </form>
        </div>

        <div class="card">
            <h2>🚀 Premium Plan</h2>
            <div class="price">RM30</div>

            <div class="features">
                ✅ Access worksheets<br>
                ✅ Answer quizzes<br>
                ✅ View explanations<br>
                ✅ Video lessons<br>
                ✅ Certificate & leaderboard
            </div>

            <form method="POST" action="create_bill.php">
                <input type="hidden" name="plan_name" value="Premium Plan">
                <input type="hidden" name="amount" value="30.00">
                <button type="submit">Subscribe Premium</button>
            </form>
        </div>

    </div>

    <p class="note">
        Payment will be processed through ToyyibPay.
    </p>

</div>

</body>
</html>