<?php
// himbauan.php - mengembalikan himbauan berdasarkan keterangan terakhir.
// output: plain text

header('Content-Type: text/plain; charset=utf-8');

$connect = mysqli_connect("localhost", "u182036527_udarasehat", "Fatihur5*", "u182036527_udarasehat");
if (!$connect) {
    echo "Server DB error";
    exit;
}

/*
  Ambil record terakhir dari sensor + suhu (jika ada).
  Menggunakan LEFT JOIN agar tidak error kalau tidak ada baris di tabel suhu.
*/
$query = "
  SELECT s.*, su.suhu
  FROM sensor s
  LEFT JOIN suhu su ON su.id_sensor = s.id_sensor
  ORDER BY s.id_sensor DESC
  LIMIT 1
";
$result = mysqli_query($connect, $query);

if (!$result) {
    // Kalau query error, tampilkan pesan aman (jangan menampilkan detail error di production)
    echo "Query error";
    $connect->close();
    exit;
}

$data = mysqli_fetch_assoc($result);

// jika tidak ada data sama sekali
if (!$data) {
    echo "Data tidak tersedia";
    $connect->close();
    exit;
}

// ambil keterangan dengan aman
$keterangan = isset($data['keterangan']) ? trim($data['keterangan']) : '';

// opsional: baca nilai-nilai yang mungkin berguna
$co = isset($data['co']) ? $data['co'] : null;
$co2 = isset($data['co2']) ? $data['co2'] : null;
$debu = isset($data['debu']) ? $data['debu'] : null;
$suhu = isset($data['suhu']) ? $data['suhu'] : null;
$kelembaban = isset($data['kelembaban']) ? $data['kelembaban'] : null;

// Default himbauan
$himbauan = "Status tidak dikenali, periksa sistem.";

// Aturan himbauan berdasarkan keterangan (sesuaikan kata2 bila perlu)
if (strcasecmp($keterangan, 'Baik') === 0) {
    $himbauan = "Kualitas udara baik, dapat beraktivitas dengan normal.";
} elseif (strcasecmp($keterangan, 'Buruk') === 0) {
    $himbauan = "Kualitas udara buruk, kurangi aktivitas berat dan pastikan purifier aktif.";
} elseif (strcasecmp($keterangan, 'Tidak Lengkap') === 0) {
    $himbauan = "Beberapa parameter tidak terbaca, cek perangkat atau sensor.";
} else {
    // Jika keterangan tidak Baik/Buruk, kita coba buat himbauan berdasarkan parameter yang ada
    // (mis. bila user memakai kategori lain atau belum ada keterangan)
    if ($co !== null || $co2 !== null || $debu !== null) {
        // contoh aturan sederhana fallback:
        $warning = false;
        if (is_numeric($co2) && $co2 > 1000)
            $warning = true;
        if (is_numeric($co) && $co >= 8.73)
            $warning = true;
        if (is_numeric($debu) && $debu > 45)
            $warning = true;

        if ($warning) {
            $himbauan = "Beberapa parameter melebihi ambang aman — kurangi aktivitas berat dan gunakan purifier.";
        } else {
            $himbauan = "Parameter yang tercatat tampak normal.";
        }
    } else {
        $himbauan = "Data tidak lengkap, periksa koneksi sensor/server.";
    }
}

// tampilkan himbauan singkat (plain text)
echo $himbauan;

$connect->close();
?>