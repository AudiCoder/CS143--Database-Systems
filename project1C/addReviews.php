<html>
<head>
	<title>Add Reviews</title>
	<style type="text/css">
	.sectionheader {font-size: 15px;color:#a58500;margin: 0 0 .5em;padding: 0;font-family: Verdana,Arial,sans-serif;font-weight:bold;}
	.header {color: #202020;font-size: 17px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
	.findList {border-collapse: collapse;width: 100%;display: table;border-spacing: 2px;border-color: gray;font-family: Verdana,Arial,sans-serif;color: #333;font-size: 13px;}
	.findResult_odd {background-color: #f6f6f5;border: #fff 1px solid;display: table-row;border-spacing: 2px;padding: 20px}
	.findResult_even {background-color: #fbfbfb;border: #fff 1px solid;display: table-row;padding: 20px}
	body {background: #ECECEC;padding: 5px 0;}
	.wrapper{margin: 0 auto; width: 900px; height: auto;padding: 6px; background: white;border-style:solid; border-color:#C8C8C8 ;border-radius:10px;border-width:2px;}
	.result_Text {color: #3366FF;font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
	.error_Text {
		font-size:15px;
		font-weight:normal;
		padding-bottom:0;
		color:#FF0000;
	}
	.input_fields {
		font-size: 12px;
		font-weight: normal;
		padding-bottom: 0;
		font-family: Verdana;
	}
	.image_header {height: 80px; margin: 0px 0 0 0; background-image: url(movies_banner.jpg);}
	.radio_header {padding-left: 220px}
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
	<div class="wrapper">
		<div class="image_header"></div>
		<form action="./search.php" method="GET">		
			<h1 class = "header">Search for actors/movies          
			
			<input type="text" name="keyword" size = "60" autocomplete="off" placeholder="Find Movies and Celebrities..."></input>
			<input type="submit" value="Search"/><BR></h1>
			<div class = "radio_header">
			<input type="radio" name="type" value="actor">Actor
			<input type="radio" name="type" value="actress">Actress
			<input type="radio" name="type" value="movie">Movie
			</div>
		</form>
		<div id="menu" align="center">
			<ul>
			<li><a href="addActorDirector.php">Add New Actor/Director</a></li>
			<li><a href="addMovie.php">Add New Movie</a></li>
			<li><a href="addDirectorToMovies.php">Add Director To Movie</a></li>
			<li><a href="addActorToMovies.php">Add Actor To Movie</a></li>
			<li><a href="addGenre.php">Add Genre To Movie</a></li>
		</ul></div>
	</div>
	<div class= "wrapper">
		<h1 class ="header">Add Reviews</h1>
		<hr/>
		<form action="./addReviews.php" method="GET">		
			<h1 class="input_fields">
				<div class="error_Text">Name of the Reviewer and Rating are required.</div><BR>
				<input type="hidden" name="input_id" value="<?php echo $_GET['input_id'];?>"/>
				Reviewer's Name: <input type="text" name="name"/><span class="error_Text"> *</span><BR><BR>
				Rating (Between 0 and 5): <input type="text" name="rating"/><span class="error_Text"> *</span><BR><BR>
				Comment:<BR><textarea name="comment" cols="60" rows="8"></textarea><BR><BR>
				<input type="submit" value="Submit"/><BR>
			</h1>
		</form>
		<hr/>
		<?php
		function customError($errno, $errstr, $errfile, $errline) {}
		set_error_handler("customError", E_ALL);
		if(isset($_GET['name'])) {
			$id = trim($_GET['input_id']);
			if ($_GET['name'] != '' && $_GET['rating'] != '') {
				$name = trim($_GET['name']);
				$rating = trim($_GET['rating']);
			} else if ($_GET['name'] == '' && $_GET['rating'] == '') {
				die ("<span class=\"error_Text\">Name of the Reviewer and Rating are required.</span>");
			} else if ($_GET['name'] == '') {
				die ("<span class=\"error_Text\">Name of the Reviewer is required.</span>");
			} else {
				die ("<span class=\"error_Text\">Rating is required.</span>");
			}

			if (isset($_GET['comment'])) {
				$comment = trim($_GET['comment']);
			}

			if ($rating < 0 || $rating > 5) {
				die ("<span class=\"error_Text\">Rating should be between 0.0 and 5.0</span>");
			}
			$time = date('Y-m-d H:i:s');
			$db_connection = mysql_connect("localhost", "cs143", "") or die ("<h3 class=\"error_Text\">Database Connection Failed</h3>");
			$db_selected=mysql_select_db("CS143", $db_connection) or die ("<h3 class=\"error_Text\">Could not connect to the database</h3>");
			$newComment = mysql_query("INSERT INTO Review VALUES('$name', '$time', $id, $rating, '$comment');");
			if(!$newComment) {
				die ("<h3 class=\"error_Text\">Adding New Review database failed</h3>");
			}

			echo "<h1 class =\"header\">Review Added Successfully</h1>"; 
		}
		?>
	</div>
</body>
