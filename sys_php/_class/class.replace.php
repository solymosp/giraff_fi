<?php

/***********************************************************************
This class is suitable for replacing sections in HTML files in two ways:

1) replace all instance of $a with $b in file:
mass_replace($a, $b, $file);

2) replace sections marked by HTML comment lines:
replace_section($file='my.html', $marker='WELCOME_MSG', $content='yippeee');

Example before:
<!-- WELCOME_MSG -->
Hello world!
<!-- /WELCOME_MSG -->

After:
<!-- WELCOME_MSG -->
yippeee
<!-- /WELCOME_MSG -->

FEATURES:
a) If $content is an array, all elements get inserted as new lines. If
not specified, a single empty line gets inserted.
b) Marker line recognition is tab and whitespace safe, you may have as
many of them as you don't feel ashamed of ÷)
c) If the section opener line is indented, all the newly inserted lines
will have the same line indent - useful to keep the HTML code clean.
d) Excessive error handling and operation reporting

After operation all functions return the log messages in a string, which
can be written to a file or echo()-ed.

               ===== THIS SCRIPT IS FREE, ENJOY! ÷) =====

giraff
***********************************************************************/

class replace {

	// replaces all instances of the strings $old with $new in $file
	function mass_replace($old, $new, $file) {
		ob_start();
		$string = file_get_contents($file);
		$newstring = str_replace($old, $new, $string);
		$fp = fopen($file, 'w') or die("mass_replace(): can't open file '$file' for writing!");
		fwrite($fp, $newstring);
		fclose($fp);
		
		echo "$file: mass_replace '$old' with '$new' \n\n";
		
		// return and clean output buffer
		return ob_get_clean();
	}
	
	// replaces the section marked by $marker and /$marker with $content in $file
	function replace_section($file, $marker, $content) {
		$arr = file($file);
		ob_start();
		echo "$file: ===== $marker ===== \n";
	
		// find section marker lines in $file: first set some variables
		$begin_marker_count = 0;
		$end_marker_count = 0;

		// loop for each line
		foreach($arr as $key => $line) {
			$string = $this->get_marker($line);
			
			// if the line doesn't have a marker, jump to the next
			if($string == false) {
				continue;
			}
			else {
				// find out if it's and endmarker or a beginning
				switch($string) {
					case $marker:
						$section_begin = $key;
						$indent = substr($line, 0, strpos($line, '<!--'));
						echo "$file: beginning mark found on: line ".($section_begin+1)."\n";
						++$begin_marker_count;
						break;
					case '/'.$marker:
						$section_end = $key;
						echo "$file: ending mark found on: line ".($section_end+1)."\n";
						++$end_marker_count;
						break;
				}
			}
		} // end of line looping
		
		// some error handling
		if($begin_marker_count == 0) {
			$writelock = true;
			echo("$file: NO BEGINNING MARK! \n");
		}
		if($end_marker_count == 0) {
			$writelock = true;
			echo("$file: NO ENDING MARK! \n");
		}
		if($begin_marker_count > 1) {
			$writelock = true;
			echo("$file: MULTIPLE BEGINNING MARK! \n");
		}
		if($end_marker_count > 1) {
			$writelock = true;
			echo("$file: MULTIPLE ENDING MARK! \n");
		}
		if(($begin_marker_count == 1) && ($end_marker_count == 1) && ($section_begin > $section_end)) {
			$writelock = true;
			echo("$file: ENDING MARK IS BEFORE BEGINNING MARK! \n");
		}

		// if everything's okay let's replace section and write file
		if(!isset($writelock)) {
			$section_length = $section_end - $section_begin -1;
			
			// what to put between the markers? first unset variable
			unset($output);
			// if not specified, set up an empty line
			if(!isset($content)) {
				$output = "\n";
			}
			else {
				// if it's an array, set them up as indented lines with linebrakes
				if(is_array($content)) {
					foreach($content as $data) {
						$output[] = $indent.$data."\n";
					}
				}
				// if it's a string, set it up indented with linebrake
				else {
					$output = $indent.$content."\n";
				}
			}
			
			// do the section substitution and write file
			array_splice($arr, $section_begin+1, $section_length, $output);
			$output = implode("", $arr);
			$fp = fopen($file, 'w') or die("replace_section(): can't open file '$file' for writing!");
			fwrite($fp, $output);
			fclose($fp);
			echo("$file: write OK \n");
		}
		// just for readable outputs
		echo "\n";

		// return and clean message buffer
		return ob_get_clean();
	}
	
	// how should we recognize which line containes a marker
	function get_marker($line) {
		// look up lines containing "<!--"
		$string = strstr($line, "<!--");
		if($string == false) {
			return false;
		}
		// take out spaces and tabs
		$string = str_replace(" ", "", $string);
		$string = str_replace("\t", "", $string);
		// crop comment marks
		$string = str_replace("<!--", "", $string);
		$string = str_replace("-->", "", $string);
		// crop line endings
		$string = rtrim($string);
		return $string;
	}
}

?>
