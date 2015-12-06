<?php

require_once("/home/kulhan/creds.php");
date_default_timezone_set('UTC');
require 'dibi.phar';

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

$date = $_GET['d'];
if ( ! $date)
	$date = time();
$date = isset($_GET['d']) ? $_GET['d'] : time();
$result["date"] = $date;
$result["time"] = time();

try
{
	$total = dibi::fetchSingle("select sum(value)/coef 
		from smrz_sensor_values 
		where sensor=1");
	$today = dibi::fetchSingle("select sum(value)/coef 
		from smrz_sensor_values 
		where sensor=1 and date(time)=curdate()");
	$selday = dibi::fetchSingle("select sum(value)/coef 
	 	from smrz_sensor_values 
	 	where sensor=1 and date(time) = %d", $date);
} catch (Exception $e) {
	    http_response_code(400);
	            echo "Connect error";
	        echo get_class($e), ': ', $e->getMessage(), "\n";
	        exit('Connection failed');
}
/*
 * oreach ($res as $row) {
        $datetime = $row['time'];
        $time = strtotime($datetime);
        #$date = 'Date('. date('Y,n,d,H,i,s',$time).')';
        $date = 'Date('. date('Y',$time).','.
                (date('n',$time)-1).','.
                date('d,H,i,s',$time).')';

	$id = $row['sensor'];
	$cnt = $sensors[$id]['cnt'];
	if (! isset($tmpres[$date])) {
		$tmpres[$date] = array( 'c' => array(
			                array('v' => $date)));
		//array_merge($tmpres['date']['c'], $empty_vals);
		$tmpres[$date]['c'] = 
			array_fill(0,$numvals, NULL);
		$tmpres[$date]['c'][0]['v'] = $date;

		//array_fill_keys($tmpres['date']['c'],$sens_keys, NULL);
		// $tmpres[$date] = $empty_vals;
	}
	$tmpres[$date]['c'][$cnt]['v'] = floatval($row['value']);
        #$rows[] = array( 'time' => $date, 'value' => $row['value']);
        /*array_push($rows, array( 'c' => array(
                array('v' => $date),
                array('v' => floatval($row['value']))
	)));
        #$rows[] = array(
        #       array('v' => $date),
        #       array('v' => $row['value']));
}
 */
$result["elTotal"] = $total;
$result["today"] = $today;
$result["sel. date"] = $selday;
$jsonTable = json_encode($result, JSON_PRETTY_PRINT);
echo $jsonTable;
#var_dump($table);
exit();

?>
