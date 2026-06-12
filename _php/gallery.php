<?php

/* NOTES
Subdir check is very weak, works like this:
If the first directory name in the current dir is the same as $descr_dir, we list thumbnails,
if not we list the subdirectories (see row 73)
*/

//
// DECLARING SECTION
//

$gallery_dir = ".";
$image_dir = "images";
$thumb_dir = "thumbs";
$descr_dir = "descriptions";
$set_descr_engine = "set_descr.php";
$page_width = 700;
$max_horiz_thumbs = 6;
$max_vert_thumbs = 1;

//
// RENDERING SECTION
//

$self = $_SERVER['PHP_SELF'];

// Request authorization
if ((!isset($_SERVER['PHP_AUTH_USER'])) || (!isset($_SERVER['PHP_AUTH_PW'])) || ($_SERVER['PHP_AUTH_USER'] != 'giraff') || ($_SERVER['PHP_AUTH_PW'] != 'blabla')) {
	header( 'WWW-Authenticate: Basic realm="kakamaki"' );
	header( "HTTP/1.1 401 Unauthorized" );
	echo 'Authorization Required!';
  exit;
}

// Checking working mode (currently only 1 available)
if(isset($_GET['do'])) {
	switch ($_GET['do']) {
		case 1:
			$mode = 'set_description';
			break;
		default:
			echo "WRONG MODE!";
	}
}
else {
	$mode = 'set_description';
}

// Determining our working directory
if(isset($_GET['dir'])) {
	$dir = $_GET['dir'];
}
else {
	$dir = $gallery_dir;
}

// Opening HTML page, making header
echo "
	<html>
	<head>
		<title>Gallery admin page</title>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-2\">
	</head>

	<body>
";

// Getting the contents of working directory
$contents = get_dirs($dir);

// If we run into any subdirectories, we list them
if($contents[0] != $descr_dir) {
	
	// Looping for every subdirectory
	foreach($contents as $subdir) {
		echo "
			<div align=center><a href=$self?do=1&dir=$dir/$subdir>[ $subdir ]</a></div><br>
		";
	}
}

// If no subdirectories, we look for images and parse a gallery of them
else {

	// Fetching image names, resetting variables
	$image_array = get_files($dir."/".$image_dir);
	$horiz_count = 0;
	$vert_count = 0;
	$open_table = "true";
	$open_tr = "true";
	
	// Opening a HTML table
	echo "
		<table width=100% align=center bgcolor=#808080>
	";

	// Looping for every image
	if(is_array($image_array)) {
		foreach ($image_array as $file) {

			// Setting variables
			$thumb_file = find_file($dir."/".$thumb_dir, $file);
			$descr_file = find_file($dir."/".$descr_dir, $file);

			// Opening a HTML table if required
			if($open_table == "true") {
				echo "<table align=center cellpadding=3 bgcolor=#808080>";
				$open_table = "false";
			}
	
			// Opening a new table row if required
			if($open_tr == "true") {
				echo "<tr>";
				$open_tr = "false";
			}
	
			// Open a table data cell, paste thumbnail for view/set subscript, paste filename
			echo "
				<td width= 160px valign=top align=center>
			";
			if($mode == 'set_description') {
				echo "
				<a href=\"JavaScript://\" onClick=\"AdminWindow=window.open('$set_descr_engine?file=$dir/$image_dir/$file','AdminWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=750,height=650,left=100,top=100'); return false;\"><img src=\"$dir/$thumb_dir/$thumb_file\" alt=$file border=0></a>
				";
			}
			else {
				echo "
				<a href=\"$dir/$image_dir/$file\" target=_blank><img src=$dir/$thumb_dir/$thumb_file alt=$file border=0></a>
				";
			}
			echo "			
				<br>
				<font size=2 color=#ffffff>$file</font><br>
			";
	
			// If there's a description file, query for the description and paste it
			if($descr_file != false) {
				$description = file_get_contents($dir."/".$descr_dir."/".$descr_file);
				echo "
				<font size=2 color=#bbbb22>$description</font>
				";
			}

			// Close the data cell, increase count
			echo "</td>";
			++$horiz_count;
	
			// Check the number of horizontal pictures, close the table row if neccessary, require new
			if($horiz_count == $max_horiz_thumbs) {
				echo "</tr>";
				$horiz_count = 0;
				++$vert_count;
				$open_tr = "true";
			}
	
			// Check the number of table rows, close table if neccessary, insert info area and require new table
			if($vert_count == $max_vert_thumbs) {
				echo "</table>";
				$vert_count = 0;
				$open_table = "true";
			}
		}
	}
}

// Close the thumbnail or directory list table, close the HTML document
echo "
	</table>
	</body>
	</html>
";

//
// FUNCTIONS SECTION
//

// Get readable directory names from a directory into an array in alphabetical order, skipping ".", ".."
function get_dirs($dir) {
//	global $image_dir, $thumb_dir, $descr_dir;
	$handle = @opendir($dir);
	while ($file = readdir($handle)) {
		if($file == "." || $file == "..") {
			continue;
		}
	  $mode = fileperms($dir."/".$file);
	  if(($mode & 0x4000) == 0x4000 && ($mode & 0x0004) == 0x0004) {
			$array[] = $file;
		}
	}
	closedir($handle);
	sort($array);
	return $array;
}

// Get filenames from a directory into an array in alphabetical order
function get_files($dir) {
	$handle = @opendir($dir);
	while ($file = readdir($handle)) {
		if(!is_dir($dir."/".$file)) {
			$array[] = $file;
		}
	}
	closedir($handle);
	sort($array);
	return $array;
}

// Find first filename beginning with a certain pattern in a directory
function find_file($dirname, $pattern) {
	$dir = dir($dirname);
	while ($file = $dir->read()) {
		if (substr($file, 0, strlen($pattern)) == $pattern) {
			return $file;
		}
	}
	return false;
}

?>
