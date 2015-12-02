<?php
$id = $_GET['id'];
$time = $_GET['time'];
$v = $_GET['v'];
$missing = $_GET['missing'];

date_default_timezone_set('UTC');
require_once("/home/kulhan/creds.php");
require 'dibi.php';

try {
 dibi::connect(array(
  'driver' => 'mysql',
  'database' => 'netfort_cz',
  'host' => $wgDBhost,
  'username' => $wgDBuser,
  'password' => $wgDBpassword
   ));
   // echo 'Connected';
} catch (DibiException $e) {
    echo get_class($e), ': ', $e->getMessage(), "\n";
    exit('Connection failed'); 
}

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
