<?php 
$file = file("/app/site/mark-ubuntu/web/backend.mcgoldfish.com/tmp/xiaofei.csv");
foreach ($file as $k => $v) {
	echo $v;
	echo "<br>";
}