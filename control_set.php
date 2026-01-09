<?php
// control_set.php
// Mengubah mode / nilai purifier / humidifier
$connect = mysqli_connect("localhost","root","","skripsi");
if (!$connect) {
    http_response_code(500);
    die("DB connect error");
}

// Terima via GET atau POST
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : null;
$purifier = isset($_REQUEST['purifier']) ? intval($_REQUEST['purifier']) : null;
$humidifier = isset($_REQUEST['humidifier']) ? intval($_REQUEST['humidifier']) : null;

// Ambil id row paling akhir
$row = mysqli_fetch_assoc(mysqli_query($connect, "SELECT id FROM controls ORDER BY id DESC LIMIT 1"));
if (!$row) {
    mysqli_query($connect, "INSERT INTO controls (mode, purifier, humidifier) VALUES ('auto',0,0)");
    $row = mysqli_fetch_assoc(mysqli_query($connect, "SELECT id FROM controls ORDER BY id DESC LIMIT 1"));
}
$id = intval($row['id']);

// Build update clause
$updates = [];
if ($mode !== null && ($mode === 'auto' || $mode === 'manual')) {
    $updates[] = "mode = '".mysqli_real_escape_string($connect, $mode)."'";
}
if ($purifier !== null) {
    $val = ($purifier ? 1 : 0);
    $updates[] = "purifier = $val";
}
if ($humidifier !== null) {
    $val = ($humidifier ? 1 : 0);
    $updates[] = "humidifier = $val";
}

if (count($updates) > 0) {
    $sql = "UPDATE controls SET ".implode(", ", $updates)." WHERE id = $id";
    $ok = mysqli_query($connect, $sql);
    if ($ok) {
        echo "OK";
    } else {
        http_response_code(500);
        echo "Failed update";
    }
} else {
    echo "No changes";
}
$connect->close();
?>
