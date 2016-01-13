<?php

require_once("/home/kulhan/creds.php");

function dateformat($time) {
  return date("Y-m-d H:i:s", $time);
  }
  
$json = $_POST['json'];
$in_data = json_decode( $json, true) ;

#var_dump($json);
#var_dump($in_data);
date_default_timezone_set('UTC');

require 'dibi.phar';

try {
 dibi::connect(array(
  'driver' => 'mysql',
  'database' => $wgDBname,
  'host' => $wgDBserver,
  'username' => $wgDBuser,
  'password' => $wgDBpassword
   ));
   #echo 'Connected';
} catch (Exception $e) {
	http_response_code(400);
	echo "Connect error";
    echo get_class($e), ': ', $e->getMessage(), "\n";
    exit('Connection failed'); 
}

$sensor=$in_data["sensor"];
switch($sensor):
	case 's0':
		$sensor = 1;
		break;
	case 'elmer':
		$sensor = 0;
		break;
	default:
		break;
endswitch;

$rec = array (
	"time" => dateformat($in_data["timestamp"]),
	"sensor" => $sensor,
	"value" => $in_data["value"]);

try
{
	dibi::query('replace into smrz_values ', $rec);
} catch (Exception $e) {
	echo "Insert error";
    echo get_class($e), ': ', $e->getMessage(), "\n";
    http_response_code(400);
    exit('Insert failed'); 
}

echo 'OK';
?>
