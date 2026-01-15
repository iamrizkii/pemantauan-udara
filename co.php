<?php
$connect = mysqli_connect("localhost", "u182036527_udarasehat", "Fatihur5*", "u182036527_udarasehat");
$sql = mysqli_query($connect, "SELECT co FROM sensor ORDER BY id_sensor DESC LIMIT 1");
$data = mysqli_fetch_assoc($sql);
$co = isset($data['co']) ? $data['co'] : 0;
echo $co;
?>