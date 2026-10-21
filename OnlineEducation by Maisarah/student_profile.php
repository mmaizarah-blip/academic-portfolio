<?php
// Sorok error database buat sementara waktu
@include('config.php'); 

$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : '';

// Data Contoh (Dummy) untuk dipaparkan selagi database belum siap
$name = "Sarah Tan (Data Contoh)";
$email = "sarah@email.com";
$class = "Alpha 1";
$status = "Active";

// Jika database dah siap nanti, kod ini akan tarik data sebenar secara automatik
if (isset($conn) && $conn && !empty($student_id)) {
    $query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $result = @mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['name'];
        $email = $row['email'];
        $class = $row['class'];
        $status = $row['status'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Profile</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 40px;">

    <h2 style="color: #5b3c88;">👤 STUDENT PROFILE</h2>
    <br>

    <form action="student_profile.php" method="GET">
        Masukkan ID Pelajar: 
        <input type="text" name="student_id" placeholder="Contoh: 1" style="padding: 5px; border-radius: 4px;">
        <button type="submit" style="background-color: #5b3c88; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Cari</button>
    </form>

    <br><br>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0px 4px 10px rgba(0,0,0,0.05); max-width: 500px;">
        <h3 style="color: #5b3c88; margin-top: 0;">Maklumat Pelajar</h3>
        <hr style="border: 1px solid #f2f2f2; margin-bottom: 15px;">
        
        <p style="margin: 10px 0;"><b>Nama:</b> <?php echo $name; ?></p>
        <p style="margin: 10px 0;"><b>Email:</b> <?php echo $email; ?></p>
        <p style="margin: 10px 0;"><b>Kelas:</b> <?php echo $class; ?></p>
        <p style="margin: 10px 0;"><b>Status:</b> <span style="color: green; font-weight: bold;"><?php echo $status; ?></span></p>

        <br>
        <a href="i_home.php" style="color: #5b3c88; text-decoration: none; font-weight: bold;">← Kembali ke Home</a>
    </div>

</body>
</html>