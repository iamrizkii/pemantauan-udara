<?php
$connect = mysqli_connect("localhost","root","","skripsi");
$sql = mysqli_query($connect, "SELECT kelembaban FROM sensor ORDER BY id_sensor DESC LIMIT 1");
$data = mysqli_fetch_assoc($sql);
$kelembaban = isset($data['kelembaban']) ? $data['kelembaban'] : 0;
echo $kelembaban;
?>
