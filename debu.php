<?php
$connect = mysqli_connect("localhost","root","","skripsi");
$sql = mysqli_query($connect, "SELECT debu FROM sensor ORDER BY id_sensor DESC LIMIT 1");
$data = mysqli_fetch_assoc($sql);
$debu = isset($data['debu']) ? $data['debu'] : 0;
echo $debu;
?>
