<?php

// Request authorization
if ((!isset($_SERVER['PHP_AUTH_USER'])) || (!isset($_SERVER['PHP_AUTH_PW'])) || ($_SERVER['PHP_AUTH_USER'] != 'giraff') || ($_SERVER['PHP_AUTH_PW'] != 'blabla')) {
	header( 'WWW-Authenticate: Basic realm="kakamaki"' );
	header( "HTTP/1.1 401 Unauthorized" );
	echo 'Authorization Required!';
  exit;
}

/////////////////////////////////////
$edit_engine = 'edit.php';
/////////////////////////////////////

/*******************************************************************************/
$self = $_SERVER['PHP_SELF'];
$basedir = getcwd();

if(isset($_GET['dir'])) {
	$current_dir = $_GET['dir'];
}
else {
	$current_dir = $basedir;
}

$subdir_array = explode('/', substr($current_dir, 1));

$path = '';
$show_path = '';
foreach($subdir_array as $subdir) {
	$path = $path.'/'.$subdir;
	$show_path = $show_path.'/'."<a href=\"$self?dir=$path\">$subdir</a>";
}

echo "
<html>
<head>
	<title>ADMIN - Main</title>
  <style type=\"text/css\">
		body {
			border: 0;
			margin: 10;
			font-family: arial,helvetica,sans-serif;
			font-size: 14px;
			background-color: #ffffff;
		}
		span.dir {
			color: #000070;
			font-size: 16;
			font-weight: bold;
			text-decoration: none;
		}
		a:link,a:visited,a:active,a:hover {
			font-style: normal;
			color: #808080;
			text-decoration: none;
		}
		a:active,a:hover {
			color: #bb1111;
		}
		hr {
			width: 100%;
			height: 1px;
			border: 0;
			color: #000;
			background-color: #000;
		}
	</style>		
</head>
<body>

| <a href=\"update.php\" target=_blank>update site</a> | <a href=\"guestbook.php\" target=_blank>update guestbook</a> | <a href=\"gallery.php?dir=..\" target=_blank>administer gallery</a> | <a href=\"logout.php\">logout</a> |

<hr>
<span class=dir>$show_path</span>
<hr>
";

/*******************************************************************************/
$dirs_array = get_dirs($current_dir);
if($dirs_array != false) {
	foreach($dirs_array as $dir) {
		if($dir == '.') {
			continue;
		}
		elseif($dir == '..') {
			$dir = substr($current_dir, 0, strrpos($current_dir, '/'));
			echo "<a href=\"$self?dir=$dir\"><span class=dir>..</span></a><br>";
		}
		else {
			echo "<a href=\"$self?dir=$current_dir/$dir\"><span class=dir>$dir</span></a><br>";
		}
	}
}

$files_array = get_files($current_dir);
if($files_array != false) {
	foreach($files_array as $file) {
		echo "<a href=\"$edit_engine?file=$current_dir/$file\" target=_blank>$file</a><br>";
	}
}

echo "
</body>
</html>
";



/*******************************************************************************/
function get_dirs($dir) {
	$handle = @opendir($dir);
	while ($file = readdir($handle)) {
		if(is_dir($dir."/".$file)) {
			$array[] = $file;
		}
	}
	closedir($handle);
	if(isset($array) && is_array($array)) {
		sort($array);
		return $array;
	}
	return false;
}

function get_files($dir) {
	$handle = @opendir($dir);
	while ($file = readdir($handle)) {
		if(!is_dir($dir."/".$file)) {
			$array[] = $file;
		}
	}
	closedir($handle);
	if(isset($array) && is_array($array)) {
		sort($array);
		return $array;
	}
	return false;
}

?>
