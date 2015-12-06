<html>
<head>
<!--Load the Ajax API-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script type="text/javascript">

// Load the Visualization API and the piechart package.
google.load('visualization', '1', {'packages':['corechart']});

// Set a callback to run when the Google Visualization API is loaded.
google.setOnLoadCallback(drawChartCallback);

function update_divs(date) {
	drawChart(date);
	update_numbers(date);
}

function drawChartCallback() {
	drawChart();
}

function drawChart(date) {

	// Create our data table out of JSON data loaded from server.
	//////var data = new google.visualization.DataTable(<?=$jsonTable?>);
	var url = "getdata.php";
	if (date !== undefined) {
		url = url + "?d=" + date;
	}

	var jsonData = $.ajax({
		// url: "getdata.php",
		url: url,
			dataType: "json",
			async: false
	}).responseText;
	var data = new google.visualization.DataTable(jsonData);
	var options = {
		title: 'Smrzovka',
			seriesType: "line",
			series: {
				0:{ 
					type: 'bars',
					targetAxisIndex:1
				},
				vAxes: [ { title: 'Teplota'},
					{ title: 'Pulses'}
				]
			}
// is3D: 'true',
// width: 800,
// height: 600
			};
		// Instantiate and draw our chart, passing in some options.
	// Do not forget to check your div ID
	var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
	chart.draw(data, options);
}
function update_graph(date)
{
	var xmlhttp;
	xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function()
{
	if (xmlhttp.readyState==4 && xmlhttp.status==200)
	{
		drawChart(date);
	}
}

xmlhttp.open("GET","graph.php?q="+date, true);
xmlhttp.send();
}
</script>
</head>
<script>
function update_numbers(date) {
	console.log("nums");
	var jsonData = $.ajax({
		type: "GET",
		url: "getnumbers.php",
		data: "d=" + date,
		dataType: "json",
		async: false
	}).responseText;

	$("div.numbers_div").html(jsonData);
}
</script>
<body>
<select name="date" id="drop" onchange="update_divs(this.value);">
<?php

require_once("/home/kulhan/creds.php");

/* Establish the database connection */
$mysqli = new mysqli($wgDBhost, $wgDBuser, $wgDBpassword, 'netfort_cz');

if ($mysqli->connect_errno) {
printf("Connect failed: %s\n", $mysqli->connect_error);
exit();
}

$days = $mysqli->query("select distinct date(time) as day from smrz_values order by date(time) desc");
while ($row = $days->fetch_assoc()) {
	$day = strtotime($row['day']);
	echo "<option value='{$day}'>{$row['day']}</option>\n";
}
mysqli_close($mysqli);
?>
</select>


<!--this is the div that will hold the pie chart-->
<div id="chart_div" style="width: 90%; height: 80%;"></div>
<div class="numbers_div"></div>
<script>
console.log("xx");
update_numbers();
</script>
</body>
</html>
