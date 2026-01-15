<?php
$connect = mysqli_connect("localhost", "u182036527_udarasehat", "Fatihur5*", "u182036527_udarasehat");
$sql = mysqli_query($connect, "SELECT co2 FROM sensor ORDER BY id_sensor DESC LIMIT 1");
$data = mysqli_fetch_assoc($sql);
$co2 = isset($data['co2']) ? $data['co2'] : 0;
echo $co2;
?>