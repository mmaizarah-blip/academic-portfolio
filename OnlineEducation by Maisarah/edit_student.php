<?php

include "db.php";

$code = $_GET['id'];

$sql =
"SELECT *
FROM users
WHERE user_code='$code'";

$result = $conn->query($sql);

$row = $result->fetch_assoc();

if(isset($_POST['update']))
{
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $standard = $_POST['standard'];
    $password = $_POST['password'];

    if(!empty($password))
    {
        $hashedPassword =
        password_hash(
            $password,
            PASSWORD_DEFAULT
        );

        $update =
        "UPDATE users
        SET
        username='$username',
        email='$email',
        phone='$phone',
        standard='$standard',
        password='$hashedPassword'
        WHERE user_code='$code'";
    }
    else
    {
        $update =
        "UPDATE users
        SET
        username='$username',
        email='$email',
        phone='$phone',
        standard='$standard'
        WHERE user_code='$code'";
    }

    if($conn->query($update))
    {
        echo "
        <script>
            alert('Student Updated');
            window.location.href='s_profile.php';
        </script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Student</title>

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

<h1>Edit Student</h1>

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

Standard:

<select name="standard">

<option <?php if($row['standard']=="Standard 1") echo "selected"; ?>>
Standard 1
</option>

<option <?php if($row['standard']=="Standard 2") echo "selected"; ?>>
Standard 2
</option>

<option <?php if($row['standard']=="Standard 3") echo "selected"; ?>>
Standard 3
</option>

<option <?php if($row['standard']=="Standard 4") echo "selected"; ?>>
Standard 4
</option>

<option <?php if($row['standard']=="Standard 5") echo "selected"; ?>>
Standard 5
</option>

<option <?php if($row['standard']=="Standard 6") echo "selected"; ?>>
Standard 6
</option>

</select>

Password:

<input
type="password"
name="password"
placeholder="Leave blank if no change">

<button
type="submit"
name="update">

Update Student

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