<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];

$update =
"UPDATE subscriptions
SET payment_status='Paid'
WHERE user_code='$userCode'
ORDER BY subscription_id DESC
LIMIT 1";

$conn->query($update);
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Success</title>
</head>

<body style="font-family:Arial;text-align:center;padding-top:100px;">

<h1>✅ Payment Successful!</h1>
<p>Your subscription has been activated.</p>

<a href="home.php">
    <button style="padding:12px 30px;font-size:18px;">
        Continue Learning
    </button>
</a>

</body>
</html>