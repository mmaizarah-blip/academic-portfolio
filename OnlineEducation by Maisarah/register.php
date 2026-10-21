<?php

include "db.php";

if(isset($_POST['register']))
{
	$username = $_POST['username'];

	$email = $_POST['email'];
	
	$phone = $_POST['phone'];

	$standard = $_POST['standard'];

	$password = $_POST['password'];

	$confirmPassword = $_POST['confirm_password'];

	$role = "Student";

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
		"SELECT *
		FROM users
		WHERE username='$username'";

		$result =
		$conn->query($check);

		if($result->num_rows > 0)
		{
			echo
			"<script>

				alert('Username already exists');

			</script>";
		}

		else
		{
			$getLastCode =
			"SELECT user_code

			FROM users

			WHERE role='Student'

			ORDER BY user_id DESC

			LIMIT 1";

			$resultCode =
			$conn->query($getLastCode);

			if($resultCode->num_rows > 0)
			{
				$rowCode =
				$resultCode->fetch_assoc();

				$number =
				(int)substr
				(
					$rowCode['user_code'],
					1
				);

				$number++;

				$userCode =
				"S" .
				str_pad
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
				'$role',
				'$standard',
				'$hashedPassword'
			)";

			if($conn->query($sql))
			{
				echo
				"<script>

					alert('Register Successful');

					window.location.href='login.php';

				</script>";
			}

			else
			{
				echo
				"<script>

					alert('Register Failed');

				</script>";
			}
		}
	}
}

?>


<!DOCTYPE html>

<html>
<head>

	<title>Register</title>

	<style>

		body
		{
			background-color: rgba(231, 134, 255, 0.5);
			font-family: Arial;
		}

		.container
		{
			width: 800px;
			background-color: white;
			padding: 40px;
			margin: auto;
			margin-top: 50px;
			border-radius: 15px;
			border-top: 6px solid purple;
			box-shadow: 0px 4px 15px rgba(0,0,0,0.2);
		}

		.header
		{
			text-align: center;
			margin-bottom: 30px;
		}

		.logo
		{
			width: 120px;
			height: 120px;
			object-fit: contain;
			margin-bottom: 10px;
		}

		.header h1
		{
			margin: 0;
			color: purple;
			font-size: 32px;
		}

		.header p
		{
			color: gray;
			margin-top: 5px;
			font-size: 14px;
		}

		.form-group
		{
			display: flex;
			gap: 20px;
			margin-bottom: 15px;
		}

		.form-box
		{
			flex: 1;
		}

		input, select
		{
			width: 100%;
			padding: 10px;
			margin-top: 5px;
			box-sizing: border-box;
			border: 1px solid #ccc;
			border-radius: 5px;
		}

		input[type="checkbox"]
		{
			width: auto;
			margin: 0;
		}

		select:focus,
		input:focus
		{
			border-color: purple;
			outline: none;
			box-shadow: 0px 0px 5px violet;
		}

		.showpass
		{
			display: flex;
			align-items: center;
			gap: 5px;
			margin-top: 10px;
		}

		button
		{
			width: 100%;
			padding: 12px;
			background-color: purple;
			color: white;
			border: none;
			border-radius: 5px;
			font-size: 16px;
			cursor: pointer;
			transition: 0.3s;
		}

		button:hover
		{
			background-color: darkviolet;
		}

	</style>

</head>

<body>

	<div class="container">

		<div class="header">

			<img src="logo.png" class="logo">

			<h1>OneStepAhead</h1>

			<p>Create Your Account</p>

		</div>

		<form method="POST">

			<div class="form-group">

				<div class="form-box">

					Username:<br>

					<input
					type="text"
					name="username"
					required>

				</div>

				<div class="form-box">

					Email:<br>

					<input
					type="email"
					name="email"
					required>

				</div>

			</div>

			<div class="form-group">

				<div class="form-box">

					Phone Number:<br>

					<input
					type="text"
					name="phone"
					required>

				</div>

				<div class="form-box" id="standardBox">

					Standard:<br>

					<select
					id="standard"
					name="standard">

						<option>Standard 1</option>
						<option>Standard 2</option>
						<option>Standard 3</option>
						<option>Standard 4</option>
						<option>Standard 5</option>
						<option>Standard 6</option>

					</select>

				</div>
			</div>

			<div class="form-group" id="lastGroup">

				<div class="form-box" id="passwordBox">

					Password:<br>

					<input
					type="password"
					id="pass"
					name="password"
					required>

				</div>
				
				<div class="form-box">

					Confirm Password:<br>

					<input
					type="password"
					id="confirmPass"
					name="confirm_password"
					required>

				</div>

			</div>

			<label class="showpass">

				<input
				type="checkbox"
				onclick="showPass()">

				Show Password

			</label>

			<br><br>

			<button
			type="submit"
			name="register">

				REGISTER

			</button>

		</form>

	</div>

	<script>

		function showPass()
		{
			var pass =
			document.getElementById("pass");

			var confirmPass =
			document.getElementById("confirmPass");

			if(pass.type == "password")
			{
				pass.type = "text";
				confirmPass.type = "text";
			}

			else
			{
				pass.type = "password";
				confirmPass.type = "password";
			}
		}

	</script>

</body>
</html>




<!-- <!DOCTYPE html>

<html>
<head>
	
	<title>Register</title>

	<style>

		body
		{
			background-color: rgba(231, 134, 255, 0.5);
			font-family: Arial;
		}

		.container
		{
			width: 800px;
			background-color: white;
			padding: 40px;
			margin: auto;
			margin-top: 50px;
			border-radius: 15px;
			border-top: 6px solid purple;
			box-shadow: 0px 4px 15px rgba(0,0,0,0.2);
		}

		.header
		{
			text-align: center;
			margin-bottom: 30px;
		}

		.logo
		{
			width: 120px;
			height: 120px;
			object-fit: contain;
			margin-bottom: 10px;
		}

		.header h1
		{
			margin: 0;
			color: purple;
			font-size: 32px;
		}

		.header p
		{
			color: gray;
			margin-top: 5px;
			font-size: 14px;
		}

		.form-group
		{
			display: flex;
			gap: 20px;
			margin-bottom: 15px;
		}

		.form-box
		{
			flex: 1;
		}

		input, select
		{
			width: 100%;
			padding: 10px;
			margin-top: 5px;
			box-sizing: border-box;
			border: 1px solid #ccc;
			border-radius: 5px;
		}

		input[type="checkbox"]
		{
			width: auto;
			margin: 0;
		}

		select:focus,
		input:focus
		{
			border-color: purple;
			outline: none;
			box-shadow: 0px 0px 5px violet;
		}

		.showpass
		{
			display: flex;
			align-items: center;
			gap: 5px;
			margin-top: 10px;
		}

		button
		{
			width: 100%;
			padding: 12px;
			background-color: purple;
			color: white;
			border: none;
			border-radius: 5px;
			font-size: 16px;
			cursor: pointer;
			transition: 0.3s;
		}

		button:hover
		{
			background-color: darkviolet;
		}

	</style>
	
</head>

<body>

	<div class="container">

		<div class="header">

			<img src="logo.png" class="logo">

			<h1>OneStepAhead</h1>

			<p>Create Your Account</p>

		</div>

		<form>

			<div class="form-group">

				<div class="form-box">

					Username:<br>
					<input type="text">

				</div>

				<div class="form-box">

					Email:<br>
					<input type="email">

				</div>

			</div>


			<div class="form-group">

				<div class="form-box">

					Phone Number:<br>
					<input type="text">

				</div>

				<div class="form-box">

					Role:<br>

					<select id="role" onchange="checkRole()">

						<option>Student</option>
						<option>Instructor</option>
						<option>Admin</option>

					</select>

				</div>

			</div>


			<div class="form-group" id="lastGroup">
				
				<div class="form-box" id="passwordBox">

					Password:<br>
					<input type="password" id="pass">

				</div>
				
				<div class="form-box" id="standardBox">

					Standard:<br>

					<select id="standard">

						<option value="videoS1.html">Standard 1</option>
						<option value="videoS2.html">Standard 2</option>
						<option value="videoS3.html">Standard 3</option>
						<option value="videoS4.html">Standard 4</option>
						<option value="videoS5.html">Standard 5</option>
						<option value="videoS6.html">Standard 6</option>

					</select>
				</div>
			</div>


			<label class="showpass">

				<input type="checkbox" onclick="showPass()">
				Show Password

			</label>

			<br><br>

			<button type="button" onclick="goHome()">REGISTER</button>

		</form>

	</div>


	<script>

		function goHome()
		{
			var standard = document.getElementById("standard").value;
			var role = document.getElementById("role").value;

			localStorage.setItem("userStandard", standard);
			localStorage.setItem("userRole", role);

			window.location.href = "home.html";
			
			var role = document.getElementById("role").value;

			if(role == "Student")
			{
				window.location.href = "home.html";
			}

			else if(role == "Instructor")
			{
				window.location.href = "I_home.html";
			}

			else if(role == "Admin")
			{
				window.location.href = "A_home.html";
			}
		}

		function showPass()
		{
			var x = document.getElementById("pass");

			if(x.type == "password")
			{
				x.type = "text";
			}

			else
			{
				x.type = "password";
			}
		}

		function checkRole()
		{
			var role = document.getElementById("role").value;

			var standardBox = document.getElementById("standardBox");

			var passwordBox = document.getElementById("passwordBox");


			if(role == "Student")
			{
				standardBox.style.display = "block";

				passwordBox.style.flex = "1";
			}

			else
			{
				standardBox.style.display = "none";

				passwordBox.style.flex = "100%";
			}
		}
		checkRole();
	</script>

</body>
</html>	-->
