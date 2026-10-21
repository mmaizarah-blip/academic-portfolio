<?php

include "db.php";

if(isset($_POST['save']))
{
	$username = $_POST['username'];

	$email = $_POST['email'];

	$phone = $_POST['phone'];

	$standard = $_POST['standard'];

	$password = $_POST['password'];

	$confirmPassword = $_POST['confirm_password'];

	if($password != $confirmPassword)
	{
		echo
		"<script>
			alert('Password and Confirm Password do not match');
		</script>";
	}
	else
	{
		$check =
		"SELECT * FROM users
		WHERE username='$username'";

		$result = $conn->query($check);

		if($result->num_rows > 0)
		{
			echo
			"<script>
				alert('Username already exists');
			</script>";
		}
		else
		{
			$lastStudent =
			"SELECT user_code
			FROM users
			WHERE role='Student'
			ORDER BY user_id DESC
			LIMIT 1";

			$resultStudent =
			$conn->query($lastStudent);

			if($resultStudent->num_rows > 0)
			{
				$row =
				$resultStudent->fetch_assoc();

				$number =
				(int)substr($row['user_code'],1);

				$number++;

				$userCode =
				"S" . str_pad
				(
					$number,
					3,
					"0",
					STR_PAD_LEFT
				);
			}
			else
			{
				$userCode = "S001";
			}

			$hashedPassword =
			password_hash
			(
				$password,
				PASSWORD_DEFAULT
			);

			$sql =
			"INSERT INTO users
			(
				user_code,
				username,
				email,
				phone,
				role,
				standard,
				password
			)

			VALUES
			(
				'$userCode',
				'$username',
				'$email',
				'$phone',
				'Student',
				'$standard',
				'$hashedPassword'
			)";

			if($conn->query($sql))
			{
				echo
				"<script>

					alert('Student Added Successfully');

					window.location.href =
					's_profile.php';

				</script>";
			}
			else
			{
				echo
				"<script>
					alert('Failed to Add Student');
				</script>";
			}
		}
	}
}

?>

<!DOCTYPE html>

<html>
<head>

<title>Add Student</title>

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

input,
select
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

<h1>Add Student</h1>

<form method="POST">

Username:

<input
type="text"
name="username"
required>

Email:

<input
type="email"
name="email"
required>

Phone Number:

<input
type="text"
name="phone"
required>

Standard:

<select
name="standard">

	<option>Standard 1</option>

	<option>Standard 2</option>

	<option>Standard 3</option>

	<option>Standard 4</option>

	<option>Standard 5</option>

	<option>Standard 6</option>

</select>

Password:

<input
type="password"
name="password"
required>

Confirm Password:

<input
type="password"
name="confirm_password"
required>

<button
type="submit"
name="save">

Save Student

</button>

</form>

<a
href="s_profile.php"
class="backBtn">

← Back to Student Profile

</a>

</div>

</body>
</html>