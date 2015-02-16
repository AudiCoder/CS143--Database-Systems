<html>
	<head>
		<title>Search Actor / Movie</title>
		<style type="text/css">
		.wrapper{margin: 0 auto; width: 900px; height: auto;padding: 3px; background: white;border-style:solid; border-color:#C8C8C8 ;border-radius:10px;border-width:2px;}
		.header {color: #686868 ;font-size: 17px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.radio_header {padding-left: 220px;color: #686868;font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.sectionheader {font-size: 15px;color:#a58500;margin: 0 0 .5em;padding: 0;font-family: Verdana,Arial,sans-serif;font-weight:bold;}
		.header_result {color: #202020;font-size: 17px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.findList {border-collapse: collapse;width: 100%;display: table;border-spacing: 2px;border-color: gray;font-family: Verdana,Arial,sans-serif;color: #333;font-size: 13px;}
		.findResult_odd {background-color: #f6f6f5;border: #fff 1px solid;display: table-row;border-spacing: 2px;padding: 20px}
		.findResult_even {background-color: #fbfbfb;border: #fff 1px solid;display: table-row;padding: 20px}
		body {background: #ECECEC;padding: 5px 0;}
		.result_Text {color: #3366FF;font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.error_Text {font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.image_header {height: 80px; margin: 0px 0 0 0; background-image: url(movies_banner.jpg);}
		.header2 {box-shadow: 10px 10px 5px #F8F8F8 inset;background-color: #Eee;border-top: #e8e8e8 1px solid;cursor: pointer;font-size: 15px;color: #a58500;margin: 0 0 1px 0;padding: 6px 10px;display: block;font-family: Verdana,Arial,sans-serif;}
		.subwrapper{margin: 0 auto; width: 890px; height: auto;padding: 3px; background: white;border-style:solid; border-color:#C8C8C8 ;border-radius:10px;border-width:1px;}
		.navStyle
		{
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
		li {
		display:inline;

	}
		</style>
	</head>	
	<body>
			
			<div class= "wrapper">
			<div class = image_header> <p></p></div>
			
		
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
			<li><a class="navStyle" href="addActorDirector.php">Add New Actor/Director</a></li>
			<li><a class="navStyle" href="addMovie.php">Add New Movie</a></li>
			<li><a class="navStyle" href="addDirectorToMovies.php">Add Director To Movie</a></li>
			<li><a class="navStyle" href="addActorToMovies.php">Add Actor To Movie</a></li>
			<li><a class="navStyle" href="addGenre.php">Add Genre To Movie</a></li>

		</ul></div>
		
			</div>	<div class= "wrapper">



<?php
require_once 'SearchQuery.php';
if(isset($_GET['keyword'])){
	$keyword=trim($_GET['keyword']);
	$keywords = preg_split("/\s+/", $keyword);

if($keywords[0]!=''){
	
	$keyLen = count($keywords);
	if (isset($_GET['type'])) {
		$type = trim($_GET['type']);
	} else {
		$type = '';
	}
	


	$category = "";
	$noMovies = false;
	$noActor = false;
	if(strcasecmp($type,"actor")==0){
		$category = "Male";
		$noMovies = true;
		$noActor = false;
	}else if(strcasecmp($type,"actress")==0){
		$category = "Female";
		$noMovies = true;
		$noActor = false;
	}else if(strcasecmp($type,"movie")==0){
		$category = "movie";
		$noMovies = false;
		$noActor = true;
	}else if(strcasecmp($type,"")==0){
		$category = "%%";
		$noMovies = false;
		$noActor = false;
	}
	$keyLen = count($keywords);
	if($keyLen<3 && !($noActor)){
		$Aresult = getActors($keywords,$category);
		if($Aresult==""){
			$noActor = true;
		}
	}else{$noActor = true;}
	
	// get movies data
	if(!($noMovies)){
		$Mresult = getMovies($keywords);
		if($Mresult==""){
			$noMovies = true;
		}
	}
	
	if(!($noMovies && $noActor)){
		echo "<h1 class = \"header_result\">Results for <B>\"".$keyword."\"</h1></B>";
		if(!($noActor)){
			echo "<div class = \"subwrapper\">";
			echo "<div class = \"header2\"><B>Names:</B></div>";
			//print actor data
			
			$i = 1;
			echo "<table class = \"findList\">";
			echo "<tbody>";
			while ($row = mysql_fetch_row($Aresult)) {
				if($i%2==1){ 
				echo "<tr class = \"findResult_odd\"><td class = \"result_Text\" height = \"40\"><a href=\"./ActorInfo.php?id=".$row[0]."\" style=\"text-decoration: none\">".$row[1]." ".$row[2]." (".$row[3].")</a></td><td><a href=\"./updateActorDirector.php?input_id=".$row[0]."&input_type=A\" style=\"text-decoration: none\">(edit)</a></td></tr>";
			
				}else{
					echo "<tr class = \"findResult_even\"><td class = \"result_Text\" height = \"40\"><a href=\"./ActorInfo.php?id=".$row[0]."\" style=\"text-decoration: none\">".$row[1]." ".$row[2]." (".$row[3].")</a></td><td><a href=\"./updateActorDirector.php?input_id=".$row[0]."&input_type=A\" style=\"text-decoration: none\">(edit)</a></td></tr>";
				}
				
				$i = $i+1;
			}
			echo "</tbody></table>";
			echo "</div><br>";
		}
		if(!($noMovies)){
		echo "<div class = \"subwrapper\">";
		echo "<div class=\"header2\"><B> Titles:</B></div>";
		//print movie data
			
			$j = 1;
			echo "<table class = \"findList\">";
			echo "<tbody>";
			while ($mrow = mysql_fetch_row($Mresult)) {
				if($j%2==1){
				echo "<tr class = \"findResult_odd\"><td class = \"result_Text\" height = \"40\"><a href=\"./MovieInfo.php?id=".$mrow[0]."\" style=\"text-decoration: none\">".$mrow[1]." (".$mrow[2].")</a></td><td><a href=\"./updateMovie.php?input_id=".$mrow[0]."\" style=\"text-decoration: none\">(edit)</a></td></tr>";
			
				}else{
					echo "<tr class = \"findResult_even\"><td class = \"result_Text\" height = \"40\"><a href=\"./MovieInfo.php?id=".$mrow[0]."\" style=\"text-decoration: none\">".$mrow[1]." (".$mrow[2].")</a></td><td><a href=\"./updateMovie.php?input_id=".$mrow[0]."\" style=\"text-decoration: none\">(edit)</a></td></tr>";
				}
				
				$j = $j+1;
			}echo "</tbody></table>";
			echo "</div>";
		}
		
	
	
	
	}else{
		echo "<h1 class  =\"error_Text\">Your search - <B>".$keyword."</B> - did not match any records.</h1>" ;
		echo "<h2 class = \"header\"><B>Suggestions: </B></h2><h1 class  =\"error_Text\">Make sure all words are spelled correctly.<BR />Try different keywords.";
		echo "<BR />Try more general keywords.<BR />Try fewer keywords.</h1>";
	}




}
	
	
	
}


?>
	</div>
	</body>
</html>