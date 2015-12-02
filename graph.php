<?php

require_once("/home/kulhan/creds.php");

/* Establish the database connection */
$mysqli = new mysqli($wgDBhost, $wgDBuser, $wgDBpassword, 'netfort_cz');

if ($mysqli->connect_errno) {
printf("Connect failed: %s\n", $mysqli->connect_error);
exit();
}


$sql = "SELECT time,value FROM smrz_values order by day(time) desc limit 2880";
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
</head>

<body>
<select name="date" id="drop" onchange="update_graph(this.value);">
<?php

$days = $mysqli->query("select distinct date(time) as day from smrz_values");
while ($row = $days->fetch_assoc()) {
	$day = $row['day'];
	echo "<option value='{$day}'>{$day}</option>";
}
?>
</select>


<!--this is the div that will hold the pie chart-->
<div id="chart_div"></div>
</body>
</html>
