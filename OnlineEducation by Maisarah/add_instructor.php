<?php

include "db.php";

if(isset($_POST['save']))
{
	$username = $_POST['username'];
	$email = $_POST['email'];
	$phone = $_POST['phone'];
	$subjectTaught = $_POST['subject_taught'];
	$password = $_POST['password'];
	$confirmPassword = $_POST['confirm_password'];

	if($password != $confirmPassword)
	{
		echo "<script>alert('Password and Confirm Password do not match');</script>";
	}
	else
	{
		$lastInstructor =
		"SELECT user_code
		FROM users
		WHERE role='Instructor'
		ORDER BY user_id DESC
		LIMIT 1";

		$resultInstructor =
		$conn->query($lastInstructor);

		if($resultInstructor->num_rows > 0)
		{
			$row =
			$resultInstructor->fetch_assoc();

			$number =
			(int)substr($row['user_code'],1);

			$number++;

			$userCode =
			"I" . str_pad($number,3,"0",STR_PAD_LEFT);
		}
		else
		{
			$userCode = "I001";
		}

		$hashedPassword =
		password_hash($password,PASSWORD_DEFAULT);

		$sql =
		"INSERT INTO users
		(
			user_code,
			username,
			email,
			phone,
			role,
			standard,
			password,
			avatar,
			subject_taught
		)

		VALUES
		(
			'$userCode',
			'$username',
			'$email',
			'$phone',
			'Instructor',
			NULL,
			'$hashedPassword',
			'avatar1.jpg',
			'$subjectTaught'
		)";

		if($conn->query($sql))
		{
			echo
			"<script>
				alert('Instructor Added Successfully');
				window.location.href='i_profile.php';
			</script>";
		}
		else
		{
			echo "<script>alert('Failed to add instructor');</script>";
		}
	}
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Add Instructor</title>

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

</style>

</head>

<body>

<div class="container">

<h1>Add Instructor</h1>

<form method="POST">

Username:
<input type="text" name="username" required>

Email:
<input type="email" name="email" required>

Phone Number:
<input type="text" name="phone" required>

Subject Taught:
<select name="subject_taught" required>
	<option value="">-- Select Subject --</option>
	<option value="Math">Math</option>
	<option value="Science">Science</option>
</select>

Password:
<input type="password" name="password" required>

Confirm Password:
<input type="password" name="confirm_password" required>

<button type="submit" name="save">
Save Instructor
</button>

</form>

</div>

</body>
</html>