<?php
$connect = mysqli_connect("localhost","root","","skripsi");
$sql = mysqli_query($connect, "SELECT co FROM sensor ORDER BY id_sensor DESC LIMIT 1");
$data = mysqli_fetch_assoc($sql);
$co = isset($data['co']) ? $data['co'] : 0;
echo $co;
?>
