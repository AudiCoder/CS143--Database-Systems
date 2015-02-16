<html>
<head><title>CALCULATOR</title></head>
<body>

<h1>CALCULATOR</h1>
(- by AKSHIT CHOPRA)<br />
<p>
Enter the expression in the following box (e.g., 10.5+20*3/25). :

    <form action="calculator.php" method="GET">
        <input type="text" name="ques">
        <input type="submit" value="Calculate" >
    </form>
</p>

<ul>
    <li>Only numbers and +,-,* and / operators are allowed in the expression.
    <li>The evaluation follows the standard operator precedence.
    <li>The calculator does not support parentheses.
    <li>The calculator handles invalid input "gracefully". It does not output PHP error messages.
	<li>Both integers and floating point numbers are allowed.</li>
</ul>

Here are some(but not limit to) reasonable test cases:
<ul>
  <li> Basic arithmetic operations like  8*8-4=60 </li>
  <li> Expressions with floating point or negative sign : -3.2+2*4-1/3 = 4.46666666667, 3*-2.1*2 = -12.6 </li>
  <li> Some typos inside operation (e.g. alphabetic letter): Invalid input expression 2d4+1 </li>
</ul>
<br>
<br>
<br>

<b>
RESULT :

<br>
</b>


<?php
$ques2=$_GET["ques"];

if(preg_match("/([A-Za-z!@#$%^&()~`=]+)|(^[\s]*[@_#$%^A-Za-z!&~`]+$)|(^$)/",$ques2))  // Checking for invalid input (i.e. spaces, alphabets and symbols  used in expression)
{
	echo "Invalid input expression";
}

elseif(preg_match("/([0-9\s*]+\s*\/[0\s]+$)/",$ques2))  // Checking for invalid input (i.e. whitespaces at the end) 
{
	echo "Invalid input expression";
}
else if(preg_match("/([+-][0]^[.])|([\D]0[^\.]+)/",$ques2))
 {
	echo "Invalid input Expression";
 }
elseif(preg_match("/(^[^0-9+-. ])|([^0-9\s]+$)|(\s[\*\/])/",$ques2)) // Checking for invalid input (i.e. beginning is with a digit or positive/negative sign or a decimal point or ending is with a digit or)
{
	echo "Invalid input expression";
}
else if(preg_match("/([-][-][-]+)/",$ques2))
 {
	echo "Invalid input expression";
 }
elseif(preg_match("/([0-9\.]\s+[0-9])|([\*\/\+\-]\s*[\*\/\+])/",$ques2)) // Checking for invalid input (i.e. usage of multiple operators with spaces between them and spaces between operands instead of operators )
{
	echo "Invalid input expression";

}
else
{
	if(preg_match("/(--)/",$ques2))
	{
		$copy_ques2 = preg_replace("(--)", "- -", $ques2); //replacing -- (double negation) for eval() to work properly
	}
	else
	$copy_ques2 =$ques2;
	
	eval("\$ans = $copy_ques2;"); // Evaluating the expression using eval() method
	echo $copy_ques2." = ".$ans;
}

?>
</body>
</html>