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
$email = $_SESSION['email'];

if(!isset($_POST['plan_name']) || !isset($_POST['amount']))
{
    header("Location: subscription.php");
    exit();
}

$planName = $_POST['plan_name'];
$amount = $_POST['amount'];

$secretKey = "1my10b4h-coxp-dv0c-fa1p-xknh44ug56cm";
$categoryCode = "z4imd56m";

$amountInSen = (int)($amount * 100);

$baseUrl = "http://localhost/online_education";

$data = array(
    'userSecretKey' => $secretKey,
    'categoryCode' => $categoryCode,
    'billName' => $planName,
    'billDescription' => "One Step Ahead Subscription - ".$planName,
    'billPriceSetting' => 1,
    'billPayorInfo' => 1,
    'billAmount' => $amountInSen,
    'billReturnUrl' => $baseUrl."/payment_success.php",
    'billCallbackUrl' => $baseUrl."/payment_callback.php",
    'billExternalReferenceNo' => $userCode,
    'billTo' => $username,
    'billEmail' => $email,
    'billPhone' => "0123456789",
    'billSplitPayment' => 0,
    'billSplitPaymentArgs' => "",
    'billPaymentChannel' => 2,
    'billContentEmail' => "Thank you for subscribing to One Step Ahead.",
    'billChargeToCustomer' => 1
);

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, "https://toyyibpay.com/index.php/api/createBill");
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

$result = curl_exec($curl);

if(curl_errno($curl))
{
    die("Curl Error : ".curl_error($curl));
}

curl_close($curl);

$response = json_decode($result, true);

if(isset($response[0]['BillCode']))
{
    $billCode = $response[0]['BillCode'];

    $insert =
    "INSERT INTO subscriptions
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
        'Pending'
    )";

    $conn->query($insert);

    header("Location: https://toyyibpay.com/".$billCode);
    exit();
}
else
{
    echo "<h2>ToyyibPay Error</h2>";
    echo "<pre>";
    print_r($response);
    echo "</pre>";
    echo "<b>Raw Response:</b><br>";
    echo $result;
    echo "<br><br><a href='subscription.php'>Back</a>";
}
?>