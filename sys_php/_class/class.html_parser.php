<?php

class html_parser {

	/*****************************************************************************
	function: strip_tags_pp 
	
	Gets $string and clears all PHP and HTML tags from it, except 'bold' and
	'italic' tags. It also converts smileys to their graphical eqvivalents, if
	picture files available.
	
	(c) code and idea by pp (corrected by KI)
	modified by giraff
	*****************************************************************************/
	
	function strip_tags_pp($string) {
	 
		$string=strip_tags($string,"<B></B><I></I><b></b><i></i>");
	
		$string=eregi_replace(":\)","<img src='picture/emot-blink.gif' alt=':)'>",$string);
		$string=eregi_replace(":\(","<img src='picture/emot-crying.gif' alt=':('>",$string);
		$string=eregi_replace("8\)","<img src='picture/emot-eek.gif' alt='8)'>",$string);
	
		return $string;
	}
	
	/*****************************************************************************
	function: check_url
	
	Gets $string and checks whether it is a valid URL or not, if so, it returns
	either 'http' or 'mailto' depending on the link type. The function gives
	a FALSE result if $string is not a valid URL.
	
	(c) the regexp strings were written by Benjamin
	(c) code was written by KI
	modified by giraff
	*****************************************************************************/
	
	function check_url($string) {
	
		$string = ereg_replace(chr(137),"",$string);
	
	//	$httpurl = 	"(((f|ht){1}tp://)[a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&])";
	//	$wwwsurl = 	"(([[:space:]+]|^)(www[.][a-zA-Z0-9@:%_.~#-\?&]+[a-zA-Z0-9@:%_~#\?&]))";
		$httpurl = 	"(^(http|ftp|https)://)[a-zA-Z0-9/@:%_=~#\?&-]+(\.[a-zA-Z0-9/@:%_=~#\?&-]+)+[a-zA-Z0-9/@:%_=~#\?&-]$";
		$wwwsurl = 	"(^(www)\.[a-zA-Z0-9/@:%_=~#\?&-]+(\.[a-zA-Z0-9/@:%_=~#\?&-]+)+[a-zA-Z0-9/@:%_=~#\?&-]$)";
		$mailurl = 	"([-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.".
				      "[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+)";
	
		if (eregi($httpurl, $string) || eregi($wwwsurl, $string)) {
			return 'http';
		}
		if (eregi($mailurl, $string)) {
			return 'mailto';
		}
		return FALSE;
	}
		
	/*****************************************************************************
	function: make_link
	
	Gets $string and checks whether it is a valid URL or not. (check_url();)
	returns TRUE if yes
	returns FALSE if no
	
	If TRUE, converts $string into to a 'href' tag. 
	$maxlength: if specified, sets the max length of the description part
	$linkname: if specified, sets the name of the link to appear - if unspecified,
		the original URL will be used
	
	(c) the regexp strings were written by Benjamin
	(c) code was written by KI
	modified by giraff
	******************************************************************************/
	
	function make_link(&$string, $maxlength, $linkname) {
	
		$str = str_replace("\n", "", $string); //remove newlines
		$str = str_replace(" ", "", $string); // remove spaces
		$str = rtrim($str);
	
		$url = $this -> check_url($str);
		
		if($url == FALSE) {
			return FALSE;
		}
	
		// setting up link description
		if(isset($linkname)) {
			$link = $linkname;
		}
		else {
			$link = $str;
		}
		if (isset($maxlength) && ($maxlength > 0) && (strlen($link) > $maxlength)) {
			$link = substr($link,0,$maxstrlen-3)."...</a>";
		}
	
		// fix http url
		if($url == 'http') {
			if(substr($str, 0, 7) != 'http://') {
				$str = 'http://'.$str;
			}
			$extensions = ' target=_blank';
		}
	
		// fix mailto url
		if($url == 'mailto') {
			if(substr($str, 0, 7) != 'mailto:') {
				$str = 'mailto:I-HATE-SPAM'.$str;	// 'I-HATE-SPAM' will bw removed on the fly with javascript
			}
			$extensions = ' onClick="fix(this)" onMouseOver="stat(this, true); return true;" onMouseOut="stat(this, false);"';	
		}
		$string = "<a href=".$str.$extensions.">".$link."</a>";
		return TRUE;
	}
	
	/*****************************************************************************
	function: parse_string
	
	Universal function, which was written to be used for guestbook systems. It
	gets $string, removes PHP and HTML tags [and inserts smileys], changes all
	newline to <BR> tag, clears all special character, modifies all URL to
	clickable hyperlink, and then gives back the result.
	
	$maxlength: if set, function splits all words which are longer
	
	(c) main code was written by KI
	modified by giraff
	*****************************************************************************/
	
	function parse_string($string, $maxlength) {
		
		$string = $this -> strip_tags_pp($string); 							// Remove HTML and PHP tags, iclude smiley
		$string = ereg_replace("\n"," ".chr(137),$string);		// Change newlines to <BR>
		$string = str_replace(chr(13),"",$string);
		$string = str_replace(chr(10),"",$string);			// Clears CHR(10) and CHR(13)
		$words = split(" ",$string);									// Split string into words
		
		for ($t = 0; $t < count($words); $t++) {
		
			if ($this -> check_url($words[$t]) != FALSE) {
				@$this -> make_link($words[$t], $maxlength);
				continue;
			}
	
			if (isset($maxlength) && (strlen($words[$t])>$maxlength)) {		// Check long words
				$splitted_part=split(" ",trim(chunk_split($words[$t],$maxlength," ")));
				array_splice($words,$t,1,$splitted_part);
			} 
		}
	
		$string=trim(implode(" ",$words));					// Make the new string
		$string=ereg_replace(chr(137),"<br>",$string);
		$string=stripslashes($string);
		
		return $string;
	}

}
?>
