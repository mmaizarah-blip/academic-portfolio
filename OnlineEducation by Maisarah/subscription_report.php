<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_code']) || strtolower($_SESSION['role']) != "admin")
{
    header("Location: login.php");
    exit();
}

$sql =
"SELECT
users.username,
users.email,
subscriptions.plan_name,
subscriptions.amount,
subscriptions.payment_status,
subscriptions.subscribed_at
FROM subscriptions
INNER JOIN users
ON subscriptions.user_code = users.user_code
ORDER BY subscriptions.subscribed_at DESC";

$result = $conn->query($sql);

$totalSql = "SELECT COUNT(*) AS total FROM subscriptions";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalSubscribers = $totalRow['total'];

$paidSql = "SELECT COUNT(*) AS total FROM subscriptions WHERE payment_status='Paid'";
$paidResult = $conn->query($paidSql);
$paidRow = $paidResult->fetch_assoc();
$totalPaid = $paidRow['total'];

$revenueSql = "SELECT SUM(amount) AS total FROM subscriptions WHERE payment_status='Paid'";
$revenueResult = $conn->query($revenueSql);
$revenueRow = $revenueResult->fetch_assoc();
$totalRevenue = $revenueRow['total'];

if($totalRevenue == NULL)
{
    $totalRevenue = 0;
}

$premiumSql = "SELECT COUNT(*) AS total FROM subscriptions WHERE plan_name='Premium Plan'";
$premiumResult = $conn->query($premiumSql);
$premiumRow = $premiumResult->fetch_assoc();
$totalPremium = $premiumRow['total'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Subscription Report</title>

<style>
body {
    font-family: Arial;
    background: #eef5ff;
    padding: 30px;
}

h1 {
    color: #1e3a8a;
    margin-bottom: 20px;
}

.stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
    margin-bottom: 25px;
}

.card {
    background: white;
    padding: 25px;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,.08);
    text-align: center;
}

.card h2 {
    color: #1e3a8a;
    font-size: 34px;
}

.card p {
    color: #555;
    font-weight: bold;
}

.tableCard {
    background: white;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,.08);
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background: #1e3a8a;
    color: white;
    padding: 14px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: center;
}

.paid {
    color: green;
    font-weight: bold;
}

.pending {
    color: orange;
    font-weight: bold;
}

.failed {
    color: red;
    font-weight: bold;
}

.backBtn {
    display: inline-block;
    margin-top: 20px;
    background: #1e3a8a;
    color: white;
    padding: 12px 20px;
    border-radius: 12px;
    text-decoration: none;
}
</style>
</head>

<body>

<h1>💳 Subscription Report</h1>

<div class="stats">

    <div class="card">
        <h2><?php echo $totalSubscribers; ?></h2>
        <p>Total Subscriptions</p>
    </div>

    <div class="card">
        <h2><?php echo $totalPaid; ?></h2>
        <p>Paid Subscriptions</p>
    </div>

    <div class="card">
        <h2>RM<?php echo number_format($totalRevenue, 2); ?></h2>
        <p>Total Revenue</p>
    </div>

    <div class="card">
        <h2><?php echo $totalPremium; ?></h2>
        <p>Premium Users</p>
    </div>

</div>

<div class="tableCard">

<table>
<tr>
    <th>No</th>
    <th>Student</th>
    <th>Email</th>
    <th>Plan</th>
    <th>Amount (RM)</th>
    <th>Status</th>
    <th>Date</th>
</tr>

<?php
$no = 1;

if($result && $result->num_rows > 0)
{
    while($row = $result->fetch_assoc())
    {
        $statusClass = strtolower($row['payment_status']);

        echo "<tr>";
        echo "<td>".$no++."</td>";
        echo "<td>".$row['username']."</td>";
        echo "<td>".$row['email']."</td>";
        echo "<td>".$row['plan_name']."</td>";
        echo "<td>".number_format($row['amount'], 2)."</td>";
        echo "<td class='".$statusClass."'>".$row['payment_status']."</td>";
        echo "<td>".$row['subscribed_at']."</td>";
        echo "</tr>";
    }
}
else
{
    echo "<tr><td colspan='7'>No subscription data found.</td></tr>";
}
?>

</table>

<a href="A_home.php" class="backBtn">← Back to Dashboard</a>

</div>

</body>
</html>