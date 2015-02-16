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

function getActorInfo($aid){
	$query = "select first,last,sex,dob,dod from Actor where id = ".$aid;
	$db_connection = getConnection();
		
		$Aresult = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$Anum_rows = mysql_num_rows($Aresult);
		if($Anum_rows!=0){
			// return
			return $Aresult;
		}else{
		return "";
		}
}
function getMovieInfo($aid){
	$query = "select DISTINCT mid,title,year,role FROM Movie M, MovieActor MA where MA.mid = M.id AND MA.aid = ".$aid;
	$db_connection = getConnection();
		
		$Mresult = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$Mnum_rows = mysql_num_rows($Mresult);
		if($Mnum_rows!=0){
			// return
			return $Mresult;
		}else{
		return "";
		}


}

function getMovieCount($aid){
	$query = "select count(mov) FROM (select DISTINCT mid as mov from MovieActor where aid = ".$aid.") ActorMovie";
	$db_connection = getConnection();
		
		$Cresult = mysql_query($query, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$Cnum_rows = mysql_num_rows($Cresult);
		if($Cnum_rows!=0){
			// return
			return $Cresult;
		}else{
		return "";
		}


}







?>