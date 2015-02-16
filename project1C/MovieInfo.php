<html>
	<head>
		<title>Actor Information</title>
		<style type="text/css">
		body {background: #ECECEC;padding: 5px 0;}
		.wrapper{margin: 0 auto; width: 900px; height: auto;padding: 3px; background: white;border-style:solid; border-color:#C8C8C8 ;border-radius:10px;border-width:2px;}
		.subwrapper{margin: 0 auto; width: 890px; height: auto;padding: 3px; background: white;border-style:solid; border-color:#C8C8C8 ;border-radius:10px;border-width:1px;}
		.header {color: #686868 ;font-size: 17px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.radio_header {padding-left: 220px;color: #686868;font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.movie_name_header {color: #000000;font-size: 27px;font-weight: normal;font-family: Verdana,Arial,sans-serif;float: left;}
		.text {color: #3366FF;font-size: 17px;font-weight: normal;padding-top: 6px;font-family: Verdana,Arial,sans-serif;}
		.star {background-image: url(star.jpg);color: #B80000; margin: 0px 0 0 0;background-position: -290px -50px;width: 90px;height:80px;line-height:66px;display: inline-block;text-align: center;vertical-align: middle;font-size: 20px;font-weight: bold;font-family:Verdana,Arial,sans-serif;float : right}
		.text2 {color: #3366FF;font-size: 13px;font-weight: normal;padding-top: 6px;font-family: Verdana,Arial,sans-serif;}
		.ytext {color: #336699;font-size: 17px;font-weight: normal;padding-top: 6px;font-family: Verdana,Arial,sans-serif;}
		.ytextheader {color: #000000;font-size: 20px;font-weight: normal;padding-top: 6px;font-family: Verdana,Arial,sans-serif;}
		.sectionheader {font-size: 15px;color:#a58500;margin: 0 0 .5em;padding: 0;font-family: Verdana,Arial,sans-serif;font-weight:bold;}
		
		.findList {border-collapse: collapse;width: 100%;display: table;border-spacing: 2px;border-color: gray;font-family: Verdana,Arial,sans-serif;color: #333;font-size: 13px;}
		.findResult_odd {background-color: #f6f6f5;border: #fff 1px solid;display: table-row;border-spacing: 2px;padding: 20px}
		.findResult_even {background-color: #fbfbfb;border: #fff 1px solid;display: table-row;padding: 20px}
		
		
		.result_Text {color: #3366FF;font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.error_Text {font-size: 13px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.image_header {height: 80px; margin: 0px 0 0 0; background-image: url(movies_banner.jpg);}
		.info_header {color: #202020;font-size: 27px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.Info_Text {font-size: 17px;font-weight: normal;padding-bottom: 0;font-family: Verdana,Arial,sans-serif;}
		.year_col {float: right;text-align: right;font-size: 13px;font-family: Verdana,Arial,sans-serif;font-weight: normal;color: #000000}
		.movieInfo_odd {color: #3366FF;vertical-align: middle;padding: 5px 10px 6px;background-color: #f6f6f5;border: #fff 1px solid;display:block;}
		.movieInfo_even {color: #3366FF;vertical-align: middle;padding: 5px 10px 6px;background-color: #fbfbfb;border: #fff 1px solid;display: block;}
		.header2 {box-shadow: 10px 10px 5px #F8F8F8 inset;background-color: #Eee;border-top: #e8e8e8 1px solid;cursor: pointer;font-size: 15px;color: #a58500;margin: 0 0 1px 0;padding: 6px 10px;display: block;font-family: Verdana,Arial,sans-serif;}
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
		</div>
		
		<div class= "wrapper">
<?php
require_once 'MovieQuery.php';
	if (isset($_GET['id'])) {
		$mid=trim($_GET['id']);
		$mresult =  getMovieInfo($mid);
		if($mresult!=""){
			while ($mrow = mysql_fetch_row($mresult)) {
				$title = $mrow[0];
				$year = $mrow[1];
				$MPAArating = $mrow[2];
				$company = $mrow[3];		
			}
			if($MPAArating==""){
				$MPAArating = "N/A";
			}
			if($company==""){
				$company = "N/A";
			}
			$gresult = getMovieGenre($mid);
			
			$avgresult = getAverageRating($mid);
			while ($avgrow = mysql_fetch_row($avgresult)) {
				$avgrating = $avgrow[0];
//echo $avgrating;
			}
			$avgrating = round($avgrating,2);
			
			$cresult = getRatingCount($mid);
			while ($crow = mysql_fetch_row($cresult)) {
				$countratings = $crow[0];
			}
			$crresult = getReviewCount($mid);
			while ($crrow = mysql_fetch_row($crresult)) {
				$countreviews = $crrow[0];
			}
			$dresult = getMovieDirector($mid);
			echo "<div><table width=\"100%\"><tbody><tr><td width=\"60%\"><div><div class =\"movie_name_header\"><B>".$title."</B></div> <div class=\"text\">(".$year.")</div></div><br><div><a href=\"./updateMovie.php?input_id=".$mid."\" style=\"text-decoration: none\"> (edit )</a></div><br><div class=\"text\" style=\"padding-left: 5px;color: #a58500;font-family: Verdana,Arial,sans-serif;\"> ";
			
			if($gresult!=""){
			$num_rows = mysql_num_rows($gresult);
			$i = 0;
			
			while ($grow = mysql_fetch_row($gresult)) {	
				$genre = $grow[0];
				echo $genre;
				if($i!=$num_rows-1){
					echo "|";
				}			 
				$i = $i+1;
			}}
			
			echo "</div></td>";
			if($avgrating == 0){
				$avgrating = "N/A";
				echo "<td width=\"30%\"> <div class = \"text2\"><B>Ratings:</B> ".$avgrating;
			}else{
				echo "<td width=\"30%\"> <div class = \"text2\"><B>Ratings:</B> ".$avgrating."/"."5 from ".$countratings." users";
			}
			echo "<br><B><a href=\"#reviewId\" style=\"text-decoration: none; color: #3366FF;font-family: Verdana,Arial,sans-serif\"><U>Reviews:</U></B> <U>".$countreviews." users</U>  </a> <B>  MPAA Rating:</B> ".$MPAArating;
			echo "<br> <br><a href=\"./AddReviews.php?input_id=".$mid."\" style=\"text-decoration: none;\">Add Your Review Now!</a>";
			
			echo "</div></td>";
			if($avgrating != "N/A"){
			echo "<td width=\"10%\" class = \"star\">".$avgrating."</td>";
			}
			echo "</tr></tbody></table></div>";
			
			echo "<div><table><tbody>";
			echo "<tr><td class = \"ytext\">Director: </td>";
			$i = 0;
			if($dresult!=""){
			$num_rows = mysql_num_rows($dresult);
			while ($drow = mysql_fetch_row($dresult)) {	
				$dir = $drow[0]." ".$drow[1]." (".$drow[2].")";
				echo "<td class = \"text2\">".$dir;
				echo "<a href=\"./updateActorDirector.php?input_id=".$drow[3]."&input_type=D\" style=\"text-decoration: none\">(edit)</a>";
				if($i!=$num_rows-1){
					echo ",";
				}			
					echo "</td>";
				$i = $i+1;
			}
			}else echo "<td class = \"text2\">N/A</td>";
			
			echo "</tr>";
			echo "<tr><td class = \"ytext\">Producer: </td>";
			echo "<td class = \"text2\">".$company."</td>";
			echo "</tr></tbody></table></div>";
			
			$aresult = getMovieActor($mid);
			if($aresult!=""){
				echo "<br><div class = \"subwrapper\"><div class = \"header2\"><B>Cast</B></div>";
				$i=1;
				
				while ($arow = mysql_fetch_row($aresult)) {
						
						if($i%2==1){
							echo "<div class = \"movieInfo_odd\"><a href=\"./ActorInfo.php?id=".$arow[0]."\" style=\"text-decoration: none;\">".$arow[1]." ".$arow[2]."</a><div><a href=\"./updateActorDirector.php?input_id=".$arow[0]."&input_type=A\" style=\"text-decoration: none\">(edit)</a></div><br>(".$arow[3].")</div>";
						
						}else{
								echo "<div class = \"movieInfo_even\"><a href=\"./ActorInfo.php?id=".$arow[0]."\" style=\"text-decoration: none;\">".$arow[1]." ".$arow[2]."</a><div><a href=\"./updateActorDirector.php?input_id=".$arow[0]."&input_type=A\" style=\"text-decoration: none\">(edit)</a></div><br>(".$arow[3].")</div>";
						}
						
					$i = $i+1;
				
				}
				echo "</div>";
			  
			}
		
			$rresult = getReview($mid);
			if($rresult!=""){
					echo "<br><div class = \"subwrapper\"><div class = \"header2\" id = \"reviewId\"><B>User Reviews</B></div>";
					$i=1;
				while ($rrow = mysql_fetch_row($rresult)) {
						
						if($i%2==1){
							echo "<div class = \"movieInfo_odd\"> Rating: ".$rrow[2]."/5<br>".$rrow[1]." | by ".$rrow[0]."<br> ".$rrow[3]."</div>";
						
						}else{
							echo "<div class = \"movieInfo_even\"> Rating: ".$rrow[2]."/5<br>".$rrow[1]." | by ".$rrow[0]."<br> ".$rrow[3]."</div>";
						}
						
					$i = $i+1;
				
				}
				echo "</div>";
			
			
			}
			
			
		
		
		}else{		
		
		}
		
		
		
	}else{
		echo "Movie Information Not Available!!";
	}
	
?>

</div>
	</body>
</html>		