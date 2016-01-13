<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
	<meta charset="UTF-8">
	<title>Title</title>
</head>
<body>

	<?php
$id = $_GET['id'];
$time = $_GET['time'];
$v = $_GET['v'];
$missing = $_GET['missing'];

date_default_timezone_set('UTC');
require_once("dbconnect.php");

#echo "Temp: ", $v;
#echo "Id: ", $id;
#echo "Time: ", $time;

#echo "Epoch: ",mktime(0, 0, 0, 1, 1, 2000);

date_default_timezone_set('UTC');
$epoch = 946684800; #mktime(0, 0, 0, 1, 1, 2000);
#$tstamp = date("Y-m-d H:i:s", $time + $epoch);
#echo $tstamp;
if ($time < 1420070400)
	$time = $time + $epoch;
$rec = array(
	"time" => date("Y-m-d H:i:s", $time), # + $epoch),
	"sensor" => $id,
	"value" => round($v,1)
);

// print_r($rec);

#$value = $res->fetch();
#exit('Connection failed');
#
try
{
	#dibi::query('replace into smrz_values ', $rec);
	dibi::query('call insert_smrz_value %l', $rec);
} catch (Exception $e) {
	http_response_code(400);
	echo "DB error";
	echo get_class($e), ': ', $e->getMessage(), "\n";
	exit('Connection failed');
}

echo 'OK';
?>
</body>
</html>
