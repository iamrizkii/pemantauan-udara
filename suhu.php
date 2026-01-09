<?php
$connect = mysqli_connect("localhost", "root", "", "skripsi");
$sql = mysqli_query($connect, "SELECT suhu FROM suhu ORDER BY id_suhu DESC LIMIT 1");
$data = mysqli_fetch_assoc($sql);
$suhu = isset($data['suhu']) ? $data['suhu'] : 0;
echo $suhu;
?>
