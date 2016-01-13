<?php

include "dbconnect.php";

$reset = empty($_POST['reset']) ? false : $_POST['reset'];

if ($reset === "true") {
    $r1 = dibi::query("update smrz_values_current set value = 0, time = NOW() where sensor = 1");
}

$res = dibi::query("select id, name, time, value from smrz_values_current JOIN smrz_sensor ON smrz_values_current.sensor = smrz_sensor.id");
$all = $res->fetchAll();

?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <style>
        table.db-table 		{ border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
        table.db-table th	{ background:#eee; padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
        table.db-table td	{ padding:5px; border-left:1px solid #ccc; border-top:1px solid #ccc; }
    </style>
    <meta charset="UTF-8">
    <title>Title</title>
    <table cellpadding="0" cellspacing="0" class="db-table">
        <tr><th>Sensor</th><th>Value</th><th>Time</th><th></th></tr>
        <?php
        foreach ($all as $row) {
            echo "<tr>\n";
            echo '<td>',$row['name'],'</td><td>',$row['value'],'</td><td>',$row['time'],'</td>';
            if ($row['id'] == 1) {
                echo '<td><form action="index.php" method="post">',"\n";
                echo '<input type="hidden" name="reset" value="true"/>',"\n";
                echo '<input type="submit" class="button" name="submit" value="Reset"/>',"\n";
                echo "</form></td>\n";
            }
            else echo '<td></td>',"\n";

            echo '</tr>',"\n";
        }
        ?>
    </table>

    <br>
    <a href="graph.php">Graphs</a>
</head>
<body>

</body>
</html>