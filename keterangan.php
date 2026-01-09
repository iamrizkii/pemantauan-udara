<?php
// keterangan.php (disesuaikan untuk keterangan "Baik" / "Buruk")

$connect = mysqli_connect("localhost", "root", "", "skripsi");
if (!$connect) {
    echo "<div class='error'><h1>DB connection error</h1></div>";
    exit;
}

$sql = mysqli_query($connect, "SELECT keterangan FROM sensor ORDER BY id_sensor DESC LIMIT 1");
if (!$sql) {
    echo "<div class='error'><h1>Query error</h1></div>";
    $connect->close();
    exit;
}

$data = mysqli_fetch_assoc($sql);
$keterangan = isset($data['keterangan']) ? trim($data['keterangan']) : '';

// Jika tidak ada keterangan, tampilkan info default
if ($keterangan === '') {
    $kualitas = "<div class='unknown'><h1>Keterangan belum tersedia</h1></div>";
} else {
    if (strcasecmp($keterangan, 'Baik') === 0) {
        $kualitas = "<div class='baik'>
                        <h1>Kualitas Udara: Baik</h1>
                        <p>Semua parameter berada dalam rentang aman.</p>
                     </div>";
    } elseif (strcasecmp($keterangan, 'Buruk') === 0) {
        $kualitas = "<div class='buruk'>
                        <h1>Kualitas Udara: Buruk</h1>
                        <p>Salah satu atau lebih parameter melebihi ambang aman.</p>
                     </div>";
    } else {
        $kualitas = "<div class='lain'><h1>Kualitas Udara: " . htmlspecialchars($keterangan) . "</h1></div>";
    }
}

echo $kualitas;
$connect->close();
?>
