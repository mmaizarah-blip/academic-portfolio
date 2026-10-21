<?php
// Letakkan simbol @ di depan include untuk sorok error jika fail/database tiada
@include('config.php'); 

$class_name = isset($_GET['class_name']) ? $_GET['class_name'] : 'Alpha 1';

// Data Contoh (Dummy) untuk paparan interface sementara database belum siap
$dummy_progress = [
    ['name' => 'Sarah Tan', 'quiz' => '90%', 'videos' => '80%'],
    ['name' => 'Siti Nurhaliza', 'quiz' => '75%', 'videos' => '92%']
];

// Kod di bawah ini hanya akan berjalan APABILA database awak dah siap nanti.
// Buat masa sekarang, jika $conn tiada, PHP akan abaikan bahagian ini secara selamat!
if (isset($conn) && $conn) {
    $query = "SELECT * FROM students WHERE class = '$class_name'";
    $result = @mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $dummy_progress = []; // Padam data contoh jika data betul dalam database wujud
        while($row = mysqli_fetch_assoc($result)) {
            $dummy_progress[] = [
                'name' => $row['name'],
                'quiz' => '0%', // Boleh disambung dengan kiraan markah nanti
                'videos' => '0%'
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Progress</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f9; padding: 40px;">

    <h2 style="color: #5b3c88;">📈 STUDENT PROGRESS</h2>
    <br>

    <form action="student_progress.php" method="GET">
        Pilih Kelas:
        <select name="class_name" style="padding: 5px; border-radius: 4px;">
            <option value="Alpha 1" <?php if($class_name == 'Alpha 1') echo 'selected'; ?>>Alpha 1</option>
            <option value="Beta 2" <?php if($class_name == 'Beta 2') echo 'selected'; ?>>Beta 2</option>
        </select>
        <button type="submit" style="background-color: #5b3c88; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Lihat</button>
    </form>

    <br><br>

    <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0px 4px 10px rgba(0,0,0,0.05); max-width: 600px;">
        <h3 style="color: #5b3c88; margin-top: 0;">Status untuk Kelas: <?php echo $class_name; ?></h3>
        <hr style="border: 1px solid #f2f2f2; margin-bottom: 20px;">

        <?php foreach($dummy_progress as $student): ?>
        <div style="margin-bottom: 20px; border-bottom: 1px dashed #eee; padding-bottom: 15px;">
            <h4 style="margin: 0 0 10px 0; color: #333;"><?php echo $student['name']; ?></h4>
            
            <div style="margin-bottom: 10px;">
                <small style="color: #666;">Kuiz Selesai: <?php echo $student['quiz']; ?></small>
                <div style="width: 100%; background: #e2e8f0; border-radius: 10px; height: 10px; margin-top: 5px;">
                    <div style="width: <?php echo $student['quiz']; ?>; background: #5b3c88; height: 100%; border-radius: 10px;"></div>
                </div>
            </div>

            <div>
                <small style="color: #666;">Video Ditonton: <?php echo $student['videos']; ?></small>
                <div style="width: 100%; background: #e2e8f0; border-radius: 10px; height: 10px; margin-top: 5px;">
                    <div style="width: <?php echo $student['videos']; ?>; background: #f59e0b; height: 100%; border-radius: 10px;"></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <br>
        <a href="i_home.php" style="color: #5b3c88; text-decoration: none; font-weight: bold;">← Kembali ke Home</a>
    </div>

</body>
</html>