<?php 

	/// Some messy helper code 
	function clean_text($string)
	{
		$x= strip_tags(utf8_decode(preg_replace("/\([^)]+\)/","",$string)));
		return utf8_encode($x);
	}
	 function get_inbetween($string)
	{
		if (preg_match_all('/'.preg_quote("(Clearing centre:").'(.*?)'.preg_quote(")").'/s', $string, $matches)) {
    	return ($matches[1][0]);
		}
	}
	
?>