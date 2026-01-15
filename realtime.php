<?php
$connect = mysqli_connect("localhost", "u182036527_udarasehat", "Fatihur5*", "u182036527_udarasehat");

$sql = mysqli_query($connect, "SELECT * FROM realtime ORDER BY id_realtime DESC");

$data = mysqli_fetch_array($sql);
$realtime = $data['realtime'];
if ($realtime == "")
	$realtime = 0;
echo $realtime;
?>