<?php 
$file = file("/app/site/mark-ubuntu/web/backend.mcgoldfish.com/tmp/alipay_record_20160607_1711_1.csv");
foreach ($file as $k => $v) {
	echo $v;
	echo "<br>";
}