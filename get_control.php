<?php
// get_control.php
// Mengembalikan kontrol terakhir dalam format query-string:
// mode=auto|manual&purifier=0|1&humidifier=0|1

header('Content-Type: text/plain; charset=utf-8');

$connect = mysqli_connect("localhost","root","","skripsi");
if (!$connect) {
    http_response_code(500);
    echo "mode=auto&purifier=0&humidifier=0";
    exit;
}

// Ambil record kontrol terakhir
$sql = mysqli_query($connect, "SELECT * FROM controls ORDER BY id DESC LIMIT 1");
if (!$sql) {
    // query gagal — kembalikan default
    echo "mode=auto&purifier=0&humidifier=0";
    mysqli_close($connect);
    exit;
}

$data = mysqli_fetch_assoc($sql);
if (!$data) {
    // tidak ada record — default
    echo "mode=auto&purifier=0&humidifier=0";
    mysqli_close($connect);
    exit;
}

// Normalisasi nilai
$mode = isset($data['mode']) ? strtolower(trim($data['mode'])) : 'auto';
if ($mode !== 'manual') $mode = 'auto'; // hanya 'manual' atau 'auto'

// Pastikan purifier/humidifier numeric (0 atau 1)
$purifier = isset($data['purifier']) ? intval($data['purifier']) : 0;
$humidifier = isset($data['humidifier']) ? intval($data['humidifier']) : 0;
$purifier = ($purifier ? 1 : 0);
$humidifier = ($humidifier ? 1 : 0);

// Output sederhana (mudah di-parse oleh ESP)
echo "mode={$mode}&purifier={$purifier}&humidifier={$humidifier}";

mysqli_close($connect);
