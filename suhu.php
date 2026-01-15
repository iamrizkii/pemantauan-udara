<?php
$connect = mysqli_connect("localhost", "u182036527_udarasehat", "Fatihur5*", "u182036527_udarasehat");
$sql = mysqli_query($connect, "SELECT suhu FROM suhu ORDER BY id_suhu DESC LIMIT 1");
$data = mysqli_fetch_assoc($sql);
$suhu = isset($data['suhu']) ? $data['suhu'] : 0;
echo $suhu;
?>