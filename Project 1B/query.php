
<html>
<head><title>CS143 Project 1B</title></head>
<body>
	<h2 align = "center">CS 143 - Database Systems - Project 1B</h2>
	<p>Version 1.0<br />
		Team Members:<br />
		1) Akshit Chopra - 604414277<br />
		2) Ankush Verma  - 904435669<br /><br />

Type an SQL query in the following box:

<p>
<form action="./query.php" method="GET">
<textarea name="query" cols="70" rows="11"></textarea>
<input type="submit" value="Submit" />
</form>
</p>
<p><small>Note: Tables and fields are case sensitive.</small>
</p>

<?php

$var = $_GET['query'];
if($var!='')
{
	
	

$db_connection = mysql_connect("localhost", "cs143", "") or die ("<h3 class=\"error\">Database Connection Failed due to: " . mysql_errno() . " : " . mysql_error() . "</h3>");


$db_selected=mysql_select_db("CS143", $db_connection);
$result = mysql_query($var, $db_connection);
$num_rows = mysql_num_rows($result);

echo "<h3 class=\"common\">Result of the Query is:</h3>";
echo "Total Number of Rows returned is: <b>".$num_rows."</b></br></br>";
echo "<table cellpadding=2 border=1>";
$column_size=mysql_num_fields($result);
$i=0;
		echo '<tr>';
		while ($i < $column_size) {
			$meta = mysql_fetch_field($result, $i);
			echo '<td><b>' . $meta->name . '</b></td>';
			$i = $i + 1;
		}
		echo '</tr>';
		while ($row = mysql_fetch_row($result)) {
			echo '<tr>';
			foreach ($row as $key=>$value) {
				if ($value) {
					echo '<td>', $value, '</td>';
				} else {
					echo '<td>', "N/A", '</td>';
				}
			}
			echo '</tr>';
		}
		echo "</table>";

mysql_close($db_connection);
}
?>

</body>
</html>
