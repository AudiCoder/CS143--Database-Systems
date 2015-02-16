<?php	
	// error handling
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
	
	function getActors($keywords,$category){
		$keyLen = count($keywords);
		if($keyLen==1){
		
		$Actor_query1 = "Select id,first,last,dob,dod from Actor where first like '%".$keywords[0]."%' and sex like '".$category."'";
		$Actor_query2 = "Select id,first,last,dob,dod from Actor where last like '%".$keywords[0]."%' and sex like '".$category."'";
		$Actor_query3 = "Select id,first,last,dob,dod from Actor where first like '%".$keywords[0]."%' and sex like '".$category."' UNION Select id,first,last,dob,dod from Actor where last like '%".$keywords[0]."%' and sex like '".$category."'";
		
		}else{
		$Actor_query1 = "Select id,first,last,dob,dod from Actor where first like '%".$keywords[0]."%' and last like '%".$keywords[1]."%' and sex like '".$category."'";
		$Actor_query2 = "Select id,first,last,dob,dod from Actor where last like '%".$keywords[0]."%' and first like '%".$keywords[1]."%' and sex like '".$category."'";
		$Actor_query3 = "Select id,first,last,dob,dod from Actor where first like '%".$keywords[0]."%' or last like '%".$keywords[1]."%' and sex like '".$category."' UNION Select id,first,last,dob,dod from Actor where last like '%".$keywords[0]."%' or first like '%".$keywords[1]."%' and sex like '".$category."'";
		}
		$db_connection = getConnection();
		
		$Aresult = mysql_query($Actor_query1, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$Anum_rows = mysql_num_rows($Aresult);
		if($Anum_rows!=0){
			// return
			return $Aresult;
		}else{
			$Aresult = mysql_query($Actor_query2, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
			$Anum_rows = mysql_num_rows($Aresult);
			if($Anum_rows!=0){
				// return
				return $Aresult;
			}else{
				$Aresult = mysql_query($Actor_query3, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
				$Anum_rows = mysql_num_rows($Aresult);
				if($Anum_rows!=0){
					// return
					return $Aresult;
				}else{
					return "";
				}
			}
		}
	}
	
	function getMovies($keywords){
	
		//forming the query
		$keyLen = count($keywords);
		$MovieCheck = "";
		for ($x=0; $x<$keyLen; $x++)
		{
			$MovieCheck  = 	$MovieCheck.$keywords[$x]." ";
		}
		$MovieCheck=trim($MovieCheck);
		$Movie_query1 = "select id,title,year from Movie where title like '%".$MovieCheck."%'";
		
		//get results
		$db_connection = getConnection();
		$Mresult = mysql_query($Movie_query1, $db_connection) or die ("<h3>" . mysql_errno() . " : " . mysql_error() . "</h3>");
		$Mnum_rows = mysql_num_rows($Mresult);
		if($Mnum_rows!=0){
			return $Mresult;
		}else{
			return "";
		}
	}

	
?>
