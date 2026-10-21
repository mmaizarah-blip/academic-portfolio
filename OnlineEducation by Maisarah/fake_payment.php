<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

$userCode = $_SESSION['user_code'];

if(!isset($_POST['plan_name']) || !isset($_POST['amount']))
{
    header("Location: subscription.php");
    exit();
}

$planName = $_POST['plan_name'];
$amount = $_POST['amount'];

$billCode = "SIM" . rand(100000,999999);

$sql = "INSERT INTO subscriptions
(
    user_code,
    plan_name,
    amount,
    bill_code,
    payment_status
)
VALUES
(
    '$userCode',
    '$planName',
    '$amount',
    '$billCode',
    'Paid'
)";

if($conn->query($sql))
{
    header("Location: payment_success.php");
    exit();
}
else
{
    echo "Subscription Failed.";
}
?>