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
    $subjectTaught = $_POST['subject_taught'];
    $password = $_POST['password'];

    if(!empty($password))
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $update =
        "UPDATE users
        SET
        username='$username',
        email='$email',
        phone='$phone',
        subject_taught='$subjectTaught',
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
        subject_taught='$subjectTaught'
        WHERE user_code='$code'";
    }

    if($conn->query($update))
    {
        echo
        "<script>
            alert('Instructor Updated');
            window.location.href='i_profile.php';
        </script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Instructor</title>

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

<h1>Edit Instructor</h1>

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

Subject Taught:
<select name="subject_taught" required>
    <option value="">-- Select Subject --</option>

    <option value="Math"
    <?php if($row['subject_taught'] == "Math"){ echo "selected"; } ?>>
        Math
    </option>

    <option value="Science"
    <?php if($row['subject_taught'] == "Science"){ echo "selected"; } ?>>
        Science
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
Update Instructor
</button>

</form>

<a
href="i_profile.php"
class="backBtn">
← Back to Instructor Profile
</a>

</div>

</body>
</html>