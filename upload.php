<?php

require_once("/home/kulhan/creds.php");

function dateformat($time) {
  return date("Y-m-d H:i:s", $time);
  }
  
#phpinfo();
#echo "begin";
$json = $_POST['json'];

$in_data = json_decode( $json, true) ;
#echo "ACCEPTED";

#if ( ! count($in_data)) {
#header("HTTP/1.1 400 No Data");
#  die('No input data');
#  }

#var_dump($json);
#var_dump($in_data);
#exit('error');
date_default_timezone_set('Europe/Prague');

require 'dibi.phar';

try {
 #echo "Connect";
 dibi::connect(array(
  'driver' => 'mysql',
  'database' => $wgDBname,
  'host' => $wgDBserver,
  'username' => $wgDBuser,
  'password' => $wgDBpassword
   ));
   #echo 'Connected';
#} catch (DibiException $e) {
} catch (Exception $e) {
	http_response_code(400);
	echo "Connect error";
    echo get_class($e), ': ', $e->getMessage(), "\n";
    exit('Connection failed'); 
}

#echo "dibi";
#$times = array_keys($in_data);
#$counters  =array_values($in_data);

#print_r( $times);
#var_dump($counters);



#$tstamps = array_map("dateformat", $times);
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
#$vals = array (
#  "timestamp" => $tstamps,
#  "counter" => $counters);

#print_r($vals);

$rec = array (
	"time" => dateformat($in_data["timestamp"]),
	"sensor" => $sensor,
	"value" => $in_data["value"]);
#var_dump($rec);

try
{
	dibi::query('replace into smrz_values ', $rec);
} catch (Exception $e) {
    http_response_code(400);
	echo "Connect error";
    echo get_class($e), ': ', $e->getMessage(), "\n";
    exit('Connection failed'); 
}


//dibi::dump();
// foreach ($in_data as $time => $count) {
//   echo date('Y-m-d H:i:s', $time), "=>", $count;
//   dibi::insert('quido_temp', $temp_rec);
//    'insert into [quido_temp] set [temp] = %f, [status] = %i where quido = %i',
//    $temp, $tempst, $value['id']);  
// }
// exit('blabla');


// $res = dibi::query('
//  select id,name from [quido_mac] where [mac] = %s', $mac
//  );

//$value = $res->fetch();

// $temp_rec = array(
//   'temp' => $temp,
//   'status' => $tempst);
// 

//if (!$value) {
//  echo 'Unknown MAC';
//  $record = array(
//     'name' => $name,
//     'mac' => $mac,
//     'id' => 0,
//     'ip' => $_SERVER['REMOTE_ADDR']
//     );
//   $res2 = dibi::query('insert into [quido_mac]', $record);
//   unset($res2);
//   
//   $res3 = dibi::query(
//   'select id,name from [quido_mac] where [mac] = %s', $mac
//   );
//   $value = $res2->fetch();
// 
  //$temp_rec['quido'] = $value['id'];
  
//  unset($res3);

  //dibi::insert('quido_temp', $temp_rec);
  // 'insert into [quido_temp] set [temp] = %f, [status] = %i where quido = %i',
  // $temp, $tempst, $value['id']);  

// } else {
// 
//   $res2 = dibi::query(
//   'update [quido_mac] set [name] = %s, [ip] = %s where [id] = %i',
//   $name, $_SERVER['REMOTE_ADDR'], $value['id']
//   );
//   unset($res2);
// }
// 

//   $temp_rec['quido'] = $value['id'];
//   dibi::query(
//   'insert into [smrz_el_pulse] ([timestamp],[counter]) values (%f, %i)',
//   $temp, $tempst,
//   'on duplicate key update [counter]=%i'
//   $temp); 
//  
//   dibi::dump();
// 
// dibi::dump();
echo 'OK';
?>
