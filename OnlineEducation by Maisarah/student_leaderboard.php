<?php
// Sorok error database buat sementara waktu
@include('config.php'); 

// Data Contoh (Dummy) untuk susunan ranking carta interface
$dummy_leaderboard = [
    ['rank' => '1', 'name' => 'Siti Nurhaliza', 'points' => '1,250 pts'],
    ['rank' => '2', 'name' => 'Ahmad Awang', 'points' => '1,180 pts'],
    ['rank' => '3', 'name' => 'Chong Wei', 'points' => '1,050 pts']
];

// Jika database dah siap nanti, kod ini akan tarik susunan asal dari SQL
if (isset($conn) && $conn) {
    $query = "SELECT * FROM leaderboard ORDER BY points DESC";
    $result = @mysqli_query($conn, $query);
    // Bahagian looping data database boleh ditambah di sini kemudian
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 40px;">

    <h2 style="color: #5b3c88;">🏆 STUDENT LEADERBOARD</h2>
    <br>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0px 4px 10px rgba(0,0,0,0.05); max-width: 600px;">
        <h3 style="color: #5b3c88; margin-top: 0;">Kedudukan Markah Tertinggi</h3>
        <hr style="border: 1px solid #f2f2f2; margin-bottom: 20px;">

        <table border="0" cellpadding="12" cellspacing="0" style="width: 100%; text-align: left;">
            <tr style="background-color: #f0ebf7; color: #5b3c88; font-weight: bold;">
                <td>Rank</td>
                <td>Nama Pelajar</td>
                <td style="text-align: right;">Jumlah Mata</td>
            </tr>
            <?php foreach($dummy_leaderboard as $row): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="font-weight: bold; color: #5b3c88;">#<?php echo $row['rank']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td style="text-align: right; font-weight: bold; color: #5b3c88;"><?php echo $row['points']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <br><br>
        <a href="i_home.php" style="color: #5b3c88; text-decoration: none; font-weight: bold;">← Kembali ke Home</a>
    </div>

</body>
</html>