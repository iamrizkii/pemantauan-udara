<?php
// kirimdata.php - versi diperbaiki untuk masalah bind_param()
// dan disesuaikan agar keterangan Baik/Buruk HANYA berdasarkan CO, CO2, dan Debu

include 'config.php';

// koneksi DB
$connect = mysqli_connect("localhost", "u182036527_udarasehat", "Fatihur5*", "u182036527_udarasehat");
if (!$connect) {
    http_response_code(500);
    die("DB connect error");
}

// helper
function to_num($v)
{
    if ($v === null || $v === '')
        return null;
    $v = str_replace(',', '.', $v);
    return is_numeric($v) ? floatval($v) : null;
}

// ambil parameter
$suhu = isset($_GET['suhu']) ? trim($_GET['suhu']) : null;
$co = isset($_GET['co']) ? trim($_GET['co']) : null;
$co2 = isset($_GET['co2']) ? trim($_GET['co2']) : null;
$kelembaban = isset($_GET['kelembaban']) ? trim($_GET['kelembaban']) : null;
$debu = isset($_GET['debu']) ? trim($_GET['debu']) : null;
$ultrasonic = isset($_GET['ultrasonic']) ? trim($_GET['ultrasonic']) : null;

// convert to numeric
$suhu = to_num($suhu);
$co = to_num($co);
$co2 = to_num($co2);
$kelembaban = to_num($kelembaban);
$debu = to_num($debu);
$ultrasonic = to_num($ultrasonic);

// minimal: harus ada minimal satu parameter
if ($suhu === null && $co === null && $co2 === null && $kelembaban === null && $debu === null && $ultrasonic === null) {
    http_response_code(400);
    echo "No parameters provided";
    exit;
}

/* =========================
   Bagian A: proses sensor utama
   ========================== */
if ($co !== null || $co2 !== null || $kelembaban !== null || $debu !== null || $suhu !== null) {

    // siapkan nilai untuk INSERT ke tabel sensor
    $co_v = ($co !== null ? $co : 0.0);
    $co2_v = ($co2 !== null ? $co2 : 0.0);
    $kelembaban_v = ($kelembaban !== null ? $kelembaban : 0.0);
    $debu_v = ($debu !== null ? $debu : 0.0);

    // sensor: co, co2, debu, kelembaban, keterangan, waktu
    // di sini kita isi dulu co, co2, kelembaban, debu
    $stmt = $connect->prepare("INSERT INTO sensor (co, co2, kelembaban, debu) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        http_response_code(500);
        die("DB prepare error (sensor): " . $connect->error);
    }

    $stmt->bind_param('dddd', $co_v, $co2_v, $kelembaban_v, $debu_v);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo "Failed menyimpan sensor: " . $stmt->error;
        $stmt->close();
        $connect->close();
        exit;
    }
    $id_sensor = $stmt->insert_id;
    $stmt->close();

    // simpan suhu (jika ada) ke tabel suhu (terpisah)
    if ($suhu !== null) {
        $id_sensor_v = (int) $id_sensor;
        $suhu_v = (float) $suhu;
        $stmt2 = $connect->prepare("INSERT INTO suhu (id_sensor, suhu) VALUES (?, ?)");
        if ($stmt2) {
            $stmt2->bind_param('id', $id_sensor_v, $suhu_v);
            $stmt2->execute();
            $stmt2->close();
        }
    }

    /* =========================
       LOGIKA KUALITAS UDARA
       - HANYA berdasarkan CO, CO2, dan Debu
       - Kelembaban dan suhu TIDAK mempengaruhi Baik/Buruk
       ========================== */

    // Ambang batas (samakan dengan yang di ESP32)
    function ok_co2($v)
    {
        return ($v !== null && $v <= 1000.0);
    }   // ppm
    function ok_co($v)
    {
        return ($v !== null && $v < 8.73);
    }      // ppm
    function ok_debu($v)
    {
        return ($v !== null && $v <= 45.0);
    }     // ug/m3

    $present = 0;      // jumlah parameter inti yang terbaca (CO, CO2, Debu)
    $bad_list = [];     // parameter inti yang melewati ambang
    $missing = [];     // parameter inti yang tidak terbaca

    // HANYA 3 PARAMETER INTI: co, co2, debu
    if ($co2 === null) {
        $missing[] = 'co2';
    } else {
        $present++;
        if (!ok_co2($co2))
            $bad_list[] = 'co2';
    }

    if ($co === null) {
        $missing[] = 'co';
    } else {
        $present++;
        if (!ok_co($co))
            $bad_list[] = 'co';
    }

    if ($debu === null) {
        $missing[] = 'debu';
    } else {
        $present++;
        if (!ok_debu($debu))
            $bad_list[] = 'debu';
    }

    // Tentukan keterangan:
    // - Jika semua 3 param inti ada & semua normal  => "Baik"
    // - Jika ada yang melewati ambang               => "Buruk"
    // - Jika data tidak lengkap                     => "Tidak Lengkap"
    if ($present == 3 && count($bad_list) == 0) {
        $keterangan = "Baik";
    } elseif ($present >= 1 && count($bad_list) > 0) {
        $keterangan = "Buruk";
    } else {
        $keterangan = "Tidak Lengkap";
    }

    // catatan opsional untuk debugging
    $notes = [];
    if (count($bad_list) > 0) {
        $notes[] = "problem: " . implode(',', $bad_list);   // hanya co,co2,debu
    }
    if (count($missing) > 0) {
        $notes[] = "missing: " . implode(',', $missing);    // hanya co,co2,debu
    }
    $catatan = implode(' | ', $notes);

    // update kolom keterangan di tabel sensor
    $upd = $connect->prepare("UPDATE sensor SET keterangan = ? WHERE id_sensor = ?");
    if ($upd) {
        $upd->bind_param('si', $keterangan, $id_sensor);
        $upd->execute();
        $upd->close();
    }

    // jika mau, bisa tambahkan kolom 'catatan' di tabel sensor, 
    // lalu aktifkan blok ini (sekarang tabel sensor kamu belum punya kolom catatan)
    /*
    $hasCatatan = false;
    $q = mysqli_query($connect, "SHOW COLUMNS FROM sensor LIKE 'catatan'");
    if ($q && mysqli_num_rows($q) > 0) $hasCatatan = true;

    if ($hasCatatan) {
        $upd2 = $connect->prepare("UPDATE sensor SET catatan = ? WHERE id_sensor = ?");
        if ($upd2) {
            $upd2->bind_param('si', $catatan, $id_sensor);
            $upd2->execute();
            $upd2->close();
        }
    }
    */
}

/* =========================
   Bagian B: ultrasonic (tetap seperti sebelumnya)
   ========================== */
if ($ultrasonic !== null) {
    $stmtU = $connect->prepare("INSERT INTO sensor_ultrasonic (distance) VALUES (?)");
    if ($stmtU) {
        $stmtU->bind_param('d', $ultrasonic);
        $stmtU->execute();
        $stmtU->close();
    }
    // ambang & cooldown
    $LIMIT_CM = 20.0;
    $ALERT_COOLDOWN = 300;
    $cooldown_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "ultrasonic_last_alert.txt";
    $can_alert = true;
    if (file_exists($cooldown_file)) {
        $last = (int) file_get_contents($cooldown_file);
        if (time() - $last < $ALERT_COOLDOWN)
            $can_alert = false;
    }
    if ($ultrasonic < $LIMIT_CM && $can_alert) {
        $message = "🚨 *ALERT ULTRASONIC* 🚨\nJarak terlalu dekat: " . $ultrasonic . " cm\nSegera periksa area!";
        if (defined('TELEGRAM_BOT_TOKEN') && defined('TELEGRAM_CHAT_ID')) {
            $url = "https://api.telegram.org/bot" . TELEGRAM_BOT_TOKEN . "/sendMessage";
            $post_fields = [
                'chat_id' => TELEGRAM_CHAT_ID,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            $res = curl_exec($ch);
            curl_close($ch);
            @file_put_contents($cooldown_file, (string) time());
        } else {
            error_log("TELEGRAM_BOT_TOKEN or TELEGRAM_CHAT_ID not defined in config.php");
        }
    }
}

// Response akhir
if ($ultrasonic !== null) {
    $resp = "Ultrasonic saved | distance: " . $ultrasonic . " cm";
    if ($ultrasonic < (isset($LIMIT_CM) ? $LIMIT_CM : 0))
        $resp .= " (alert if allowed)";
    echo $resp;
} else {
    if (isset($keterangan)) {
        // kirim ringkas: Baik / Buruk / Tidak Lengkap (+ catatan parameter inti)
        $response = $keterangan;
        if (!empty($catatan))
            $response .= " ($catatan)";
        echo $response;
    } else {
        echo "OK";
    }
}

$connect->close();
?>