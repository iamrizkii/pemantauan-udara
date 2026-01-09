<?php 	
	$connect = mysqli_connect("localhost", "root", "", "skripsi");

	$sql = mysqli_query($connect, "SELECT * FROM realtime ORDER BY id_realtime DESC"); 

	$data = mysqli_fetch_array($sql);
	$realtime = $data['realtime'];
		if ($realtime == "" ) $realtime = 0;
	echo $realtime;
 ?>