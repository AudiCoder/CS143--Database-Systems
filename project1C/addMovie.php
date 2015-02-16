<html>
<head>
	<title>Add New Movie</title>
	<style type="text/css">
	.header {color: #FF0000;font-size: 17px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
	.findList {border-collapse: collapse;width: 100%;display: table;border-spacing: 2px;border-color: gray;font-family: Verdana,Arial,sans-serif;color: #333;font-size: 13px;}
	.findResult_odd {background-color: #f6f6f5;border: #fff 1px solid;display: table-row;border-spacing: 2px;padding: 20px}
	.findResult_even {background-color: #fbfbfb;border: #fff 1px solid;display: table-row;padding: 20px}
	body {background: #ECECEC;padding: 5px 0;}
	.wrapper{margin: 0 auto; width: 900px; height: auto;padding: 6px; }
	.result_Text {color: #3366FF;font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
	.Error_Message {
		font-size:15px;
		font-weight:normal;
		padding-bottom:0;
		color:#FF0000;
	}
	.nav {
    line-height:30px;
    background-color:#eeeeee;
    height:300px;
    width:250px;
    float:left;
    padding:5px; 
}

	.input_fields {
		font-size: 12px;
		font-weight: normal;
		padding-bottom: 0;
		font-family: Verdana;
	}
	.image_header {height: 0px; margin: 0px 0 0 0;}
	.radio_header {padding-left: 550px}
	li {
		display:inline;
	}
	a {
		background-color: rgb(0, 0, 0);
		color: rgb(255, 255, 255);
		cursor: auto;
		display: inline;
		font-family: verdana;
		font-size: 10px;
		font-weight: bold;
		font-style:italic;
		height: 16px;
		line-height: 16.899999618530273px;
		list-style-type: none;
		padding-bottom: 4px;
		padding-left: 4px;
		padding-right: 4px;
		padding-top: 4px;
		text-align: center;
		text-decoration: none solid rgb(255, 255, 255);
		text-transform: uppercase;
		width: 86px;
	}
	</style>
</head>
<body>
<div class="header"></div>
		<form action="./search.php" method="GET">		
			<h1 class = "header" align="center">Search for actors/movies          
			
			<input type="text" name="keyword" size = "60" autocomplete="off" placeholder="Find Movies and Celebrities..."></input>
			<input type="submit" value="Search"/><BR></h1>
			<div class = "radio_header">
		
			<input type="radio" name="type" value="actor">Actor
			<input type="radio" name="type" value="actress">Actress
			<input type="radio" name="type" value="movie">Movie
			</div>
		</form>
		</div>
	<div class="nav">
		
		<div id="menu" align="center">
			<ul>
			<br><br>
			<li><a href="addActorDirector.php">Add New Actor/Director<br><br></a></li>
			<li><a href="addMovie.php">Add New Movie<br><br></a></li>
			<li><a href="addDirectorToMovies.php">Add Director To Movie<br><br></a></li>
			<li><a href="addActorToMovies.php">Add Actor To Movie<br><br></a></li>
			<li><a href="addGenre.php">Add Genre To Movie<br><br></a></li>
		</ul></div>
	</div>
	<div class= "wrapper">
		<h1 class ="header">Add New Movie</h1>
		<hr/>
		<form action="./addMovie.php" method="GET">		
			<h1 class="input_fields">
				<div class="Error_Message">Title is Required.</div><BR>
				Movie Title:     <input type="text" name="title"/><span class="Error_Message"> *</span><BR>
				Release Year (Year should be in format (YYYY)): <input type="text" name="year"/><BR><BR>
				MPAA Rating:     <input type="text" name="rate"/><BR><BR>
				Production Company:    <input type="text" name="comp"/><BR><BR>
				<input type="submit" value="Submit"/><BR>
			</h1>
		</form>
		<hr/>
		<?php
		function Errorfunc($errno,$errstr,$errfile,$errline) {}
		set_error_handler("Errorfunc", E_ALL);
		if(isset($_GET['title'])) {
			if ($_GET['title'] != '') {
				$title = trim($_GET['title']);
			} else {
				die ("<span class=\"Error_Message\">Title is Required.</span>");
			}
			if (isset($_GET['year'])) {
				$year = trim($_GET['year']);
			}
			if (isset($_GET['rate'])) {
				$rate = trim($_GET['rate']);
			}
			if (isset($_GET['comp'])) {
				$comp = trim($_GET['comp']);
			}

			if(($year != '') && (!preg_match('/^\d\d\d\d$/', $year))) {
				die ("<span class=\"Error_Message\">Year should be in YYYY format.</span>");
			}
			
			$db_connection = mysql_connect("localhost", "cs143", "") or die ("<h3 class=\"Error_Message\">Connection to the Database Failed</h3>");
			$db_selected=mysql_select_db("CS143", $db_connection) or die ("<h3 class=\"Error_Message\">Database could not be connected</h3>");
			$id = mysql_query('SELECT id FROM MaxMovieID;');
			while ($row = mysql_fetch_row($id)) {
				foreach ($row as $key=>$value) {
					$id=$value;
				}
			}
			$next_id = $id + 1;
			$newTuple = mysql_query("INSERT INTO Movie VALUES($next_id, '$title', '$year', '$rate', '$comp');");
			if(!$newTuple) {
				die ("<h3 class=\"Error_Message\">Database updation failed</h3>");
			}
			$updateMaxMovieTable = mysql_query("UPDATE MaxMovieID SET id = $next_id;");
			echo "<h1 class =\"header\">Movie Added Successfully</h1>"; 
		}
		?>
	</div>
</body>
