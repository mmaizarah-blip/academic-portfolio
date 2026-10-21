<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];

if(isset($_GET['billcode']))
{
    $billCode = $_GET['billcode'];

    $update =
    "UPDATE subscriptions
    SET payment_status='Failed'
    WHERE user_code='$userCode'
    AND bill_code='$billCode'";

    $conn->query($update);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment Failed</title>

<style>
body {
    font-family: Arial;
    min-height: 100vh;
    background: linear-gradient(135deg, #020617, #1e3a8a, #2563eb);
    display: flex;
    justify-content: center;
    align-items: center;
}

.card {
    background: white;
    width: 500px;
    padding: 40px;
    border-radius: 30px;
    text-align: center;
    box-shadow: 0 20px 50px rgba(0,0,0,0.35);
}

h1 {
    color: #dc2626;
    margin-bottom: 15px;
}

p {
    color: #475569;
    font-size: 17px;
}

a {
    display: inline-block;
    margin-top: 25px;
    padding: 13px 25px;
    background: linear-gradient(135deg, #2563eb, #3b82f6);
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: bold;
}
</style>
</head>

<body>

<div class="card">
    <h1>❌ Payment Failed</h1>

    <p>
        Your payment was not successful. Please try again to activate your subscription.
    </p>

    <a href="subscription.php">
        Try Again
    </a>
</div>

</body>
</html>