<?php
include "db.php";

/*
ToyyibPay will send payment data to this page automatically.
Common data sent:
- billcode
- status_id
- order_id
*/

$billCode = "";

if(isset($_POST['billcode']))
{
    $billCode = $_POST['billcode'];
}
else if(isset($_GET['billcode']))
{
    $billCode = $_GET['billcode'];
}

$statusId = "";

if(isset($_POST['status_id']))
{
    $statusId = $_POST['status_id'];
}
else if(isset($_GET['status_id']))
{
    $statusId = $_GET['status_id'];
}

/*
status_id:
1 = successful payment
2 = pending
3 = unsuccessful
*/

if($billCode != "")
{
    if($statusId == "1")
    {
        $status = "Paid";
    }
    else if($statusId == "2")
    {
        $status = "Pending";
    }
    else
    {
        $status = "Failed";
    }

    $update =
    "UPDATE subscriptions
    SET payment_status='$status'
    WHERE bill_code='$billCode'";

    $conn->query($update);
}

echo "OK";
?>