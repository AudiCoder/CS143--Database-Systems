<?php


function customError($errno, $errstr, $errfile, $errline) {
		echo "$errno : $errstr : $errfile : $errline <BR />";
	}
	set_error_handler("customError", E_ALL);
	
	function getConnection(){
		//establishing a connection
		$db_connection = mysql_connect("localhost", "cs143", "") or die ("<h3 class=\"error\">Database Connection Failed due to: " . mysql_errno() . " : " . mysql_error() . "</h3>");
		//connecting to database	
		$db_selected=mysql_select_db("CS143", $db_connection);
		if (!$db_selected) {
			echo "<h3 style=\"padding-left: 280px;float:center;font-family: Verdana,Arial,sans-serif;\" >Web Page Not Available !!! </h3>";
			exit(1);
		}
		return $db_connection;
	}

function getMovieInfo($mid){
	$query = "select title,year,rating,company from Movie where id = ".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}

function getMovieGenre($mid){
	$query = "select genre from MovieGenre where mid= ".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}

function getMovieDirector($mid){
	$query = "select first,last,dob,did from Director D, MovieDirector MD where MD.did = D.id And MD.mid =".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}

function getMovieActor($mid){
	$query = "select A.id as aid,first,last,role from Actor A, MovieActor MA where MA.aid = A.id And MA.mid =".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}

function getReview($mid){
	$query = "select name,time,rating,comment from Review where comment is not null and mid =".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}

function getReviewCount($mid){
	$query = "select count(*) from Review where comment is not null and mid =".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}
function getRatingCount($mid){
	$query = "select count(*) from Review where rating is not null and mid =".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}

function getAverageRating($mid){
	$query = "select AVG(rating) from Review where rating is not null and mid =".$mid;
	$db_connection = getConnection();
		
		$result = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$num_rows = mysql_num_rows($result);
		if($num_rows!=0){
			// return
			return $result;
		}else{
		return "";
		}
}
?>