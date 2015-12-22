<?php

require_once "dbconnect.php";

$rows = array();
$table['cols'] = array(
        array('label' => 'Date', 'type' => 'datetime'),
);

$r1 = dibi::query("select id,name,type from smrz_sensor");
$sensors = $r1->fetchAssoc('id');
$scnt = 1;
$empty_vals = array();
foreach($sensors as $sensor) {
	array_push($table['cols'],
		array('label' => $sensor['name'], 'type' => 'number'));
	$sensors[$sensor['id']]['cnt']=$scnt;
	$empty_vals[$scnt]=NULL;
	$scnt++;
}
$numvals = count($sensors) +1;
#var_dump($sensors);

$id = $_GET['i'];
$date = $_GET['d'];

if ( ! $date) 
	$date = time();

$sql[] = 'select time,sensor,value,smrz_sensor.type from [smrz_values]';
array_push($sql, 'join smrz_sensor on 
	smrz_sensor.id = smrz_values.sensor where');

if ($id) {
	array_push($sql, 'sensor = %i and', $id);
}

array_push($sql, 'date(time) = %d', $date);

try
{
	        $result = dibi::query($sql);
} catch (Exception $e) {
	    http_response_code(400);
	            echo "Connect error";
	        echo get_class($e), ': ', $e->getMessage(), "\n";
	        exit('Connection failed');
}
$res = $result->fetchAll();
$tmpres = array();

foreach ($res as $row) {
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
	 */
        #$rows[] = array(
        #       array('v' => $date),
        #       array('v' => $row['value']));
}
// $table['rows'] = array(array_values($tmpres));
foreach ($tmpres as $date=>$val) {
 	$table['rows'][] = $val;
}
// $table['rows'] = $tmpres;
//var_dump($table);
$jsonTable = json_encode($table, JSON_PRETTY_PRINT);
echo $jsonTable;
#var_dump($table);
unset($result);
exit();

?>
