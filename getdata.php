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

$id = $_GET['i'];
$date = $_GET['d'];

$sql[] = 'select time,sensor,value,smrz_sensor.type from [smrz_values]';
array_push($sql, 'join smrz_sensor on 
	smrz_sensor.id = smrz_values.sensor where');

if ($id) {
	array_push($sql, 'sensor = %i', $id);
}

if ($id && $date) {
	array_push($sql, 'AND');
}
if ($date) {
	array_push($sql, 'date(time) = %d', $date);
} else {
	array_push($sql, 'order by date(time) desc limit 2880');
}

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
$rows = array();
$table = array();
foreach ($res as $row) {
        $datetime = $row['time'];
        $time = strtotime($datetime);
        #$date = 'Date('. date('Y,n,d,H,i,s',$time).')';
        $date = 'Date('. date('Y',$time).','.
                (date('n',$time)-1).','.
                date('d,H,i,s',$time).')';

        #$rows[] = array( 'time' => $date, 'value' => $row['value']);
        array_push($rows, array( 'c' => array(
                array('v' => $date),
                array('v' => floatval($row['value']))
        )));
        #$rows[] = array(
        #       array('v' => $date),
        #       array('v' => $row['value']));
}
$table['cols'] = array(
        array('label' => 'Date', 'type' => 'datetime'),
        array('label' => 'Pulse', 'type' => 'number')
);
$table['rows'] = $rows;
$jsonTable = json_encode($table);
echo $jsonTable;
#var_dump($table);
unset($result);
exit('ok');


$sql = "SELECT time,value FROM smrz_values where sensor =1 order by day(time) desc limit 2880";
$result = $mysqli->query($sql);
#	$result->close();

$rows = array();
$table = array();

#$table['cols'] = array(
#	array('label' => 'Pulses', 'type' => 'number'),
#	array('label' => 'Time', 'type' => 
#var_dump($result);
#$data[0]=array('time','pulses');

#$table['cols'] = array(

#array('label' => 'pulses', 'type' => 'string'),
#array('label' => 'Percentage', 'type' => 'number')
#);
/* Extract the information from $result */
#date_default_timezone_set('Europe/Prague');
while ($row = $result->fetch_assoc()) {
	#var_dump($row);
	$datetime = $row['time'];
	$time = strtotime($datetime);
	#$date = 'Date('. date('Y,n,d,H,i,s',$time).')';
	$date = 'Date('. date('Y',$time).','.
		(date('n',$time)-1).','.
		date('d,H,i,s',$time).')';

	#$rows[] = array( 'time' => $date, 'value' => $row['value']);
	array_push($rows, array( 'c' => array( 
		array('v' => $date), 
		array('v' => floatval($row['value']))
	)));
	#$rows[] = array( 
	#	array('v' => $date), 
	#	array('v' => $row['value']));
}
$table['cols'] = array(
	array('label' => 'Date', 'type' => 'datetime'),
	array('label' => 'Pulse', 'type' => 'number')
);
$table['rows'] = $rows;
#$temp = array();
// The following line will be used to slice the Pie chart
#$temp[] = array('v' => (string) $r['weekly_task']); 
// Values of the each slice
#$temp[] = array('v' => (int) $r['percentage']); 
#$rows[] = array('c' => $temp);
#}
#$table['rows'] = $rows;
//
//                                                                                         // convert data into JSON format
#$jsonTable = json_encode($rows);
$jsonTable = json_encode($table);
#echo $jsonTable;
//
?>
<html>
<head>
<!--Load the Ajax API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">

// Load the Visualization API and the piechart package.
google.load('visualization', '1', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawChart);

function drawChart() {

	// Create our data table out of JSON data loaded from server.
	//var data = new google.visualization.DataTable(<?=$jsonTable?>);
	var data = new google.visualization.DataTable(<?=$jsonTable?>);
	/*
	data.addColumn("datetime","Date");
	data.addColumn("number","Pulses");

	//alert(JSON.parse(<?=$jsonTable?>));
	var js = JSON.parse(<?=$jsonTable?>);
	data.addRows(js);
	//data.addRows(<?php echo $jsonTable?>);

	 */

	var options = {
		title: 'My Weekly Plan',
			is3D: 'true',
			width: 800,
			height: 600
	};
	// Instantiate and draw our chart, passing in some options.
// Do not forget to check your div ID
var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
chart.draw(data, options);
}
</script>
<script>
var xmlHttp
	function update_graph(date)
	{
		alert(date);
		xmlhttp=new XMLHttpRequest();
		xmlhttp.onreadystatechange=function()
	{
		if (xmlhttp.readyState==4 && xmlhttp.status==200)
		{
			alert(xmlhhtp.responseText);
			drawchart2();
		}
	}

		xmlhttp.open("GET","graph.php?q="+date, true);
		xmlhttp.send();
	}
</script>
</head>

<body>
<select name="date" id="drop" onchange="update_graph(this.value);">
<?php

$days = $mysqli->query("select distinct date(time) as day from smrz_values");
while ($row = $days->fetch_assoc()) {
	$day = $row['day'];
	echo "<option value='{$day}'>{$day}</option>\n";
}
?>
</select>


<!--this is the div that will hold the pie chart-->
<div id="chart_div"></div>
</body>
</html>
