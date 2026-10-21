<?php

session_start();

include "db.php";

$userCode =
$_SESSION['user_code'];

$sql =
"SELECT *
FROM users
WHERE user_code='$userCode'";

$result =
$conn->query($sql);

$row =
$result->fetch_assoc();

if(isset($_POST['update']))
{
	$username =
	$_POST['username'];

	$email =
	$_POST['email'];

	$phone =
	$_POST['phone'];

	$password =
	$_POST['password'];

	if(!empty($password))
	{
		$hashedPassword =
		password_hash
		(
			$password,
			PASSWORD_DEFAULT
		);

		$update =
		"UPDATE users

		SET

		username='$username',
		email='$email',
		phone='$phone',
		password='$hashedPassword'

		WHERE user_code='$userCode'";
	}
	else
	{
		$update =
		"UPDATE users

		SET

		username='$username',
		email='$email',
		phone='$phone'

		WHERE user_code='$userCode'";
	}

	if($conn->query($update))
	{
		$_SESSION['username'] =
		$username;

		$_SESSION['email'] =
		$email;

		echo
		"<script>

		alert('Profile Updated');

		window.location.href=
		'a_profile.php';

		</script>";
	}
}

?>

<!DOCTYPE html>

<html>
<head>

<title>Edit Admin Profile</title>

<style>

body
{
	font-family: Arial;
	background-color: #f2f2f2;
}

.container
{
	width: 600px;

	margin: auto;
	margin-top: 40px;

	background: white;

	padding: 30px;

	border-radius: 15px;

	box-shadow: 0px 0px 10px lightgray;
}

h1
{
	color: purple;
	text-align: center;
	margin-bottom: 25px;
}

input
{
	width: 100%;

	padding: 10px;

	margin-top: 5px;
	margin-bottom: 15px;

	border: 1px solid #ccc;

	border-radius: 5px;
}

button
{
	width: 100%;

	padding: 12px;

	background: purple;

	color: white;

	border: none;

	border-radius: 5px;

	cursor: pointer;
}

button:hover
{
	background: darkviolet;
}

.backBtn
{
	display: inline-block;

	margin-top: 15px;

	text-decoration: none;

	color: purple;
}

</style>

</head>

<body>

<div class="container">

<h1>Edit My Profile</h1>

<form method="POST">

Username:

<input
type="text"
name="username"
value="<?php echo $row['username']; ?>"
required>

Email:

<input
type="email"
name="email"
value="<?php echo $row['email']; ?>"
required>

Phone Number:

<input
type="text"
name="phone"
value="<?php echo $row['phone']; ?>"
required>

Password:

<input
type="password"
name="password"
placeholder="Leave blank if no change">

<button
type="submit"
name="update">

Update Profile

</button>

</form>

<a
href="a_profile.php"
class="backBtn">

← Back to Profile

</a>

</div>

</body>
</html>