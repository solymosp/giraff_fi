<?php

///////////////////////////////
$image_dir = "images";
$descr_dir = "descriptions";
///////////////////////////////

$self = $_SERVER['PHP_SELF'];
$dir = $_GET['dir'];

if(isset($_GET['no'])) {
	$no = $_GET['no'];
}
else {
	$no = 0;
}

$img_array = get_files("$dir/$image_dir");
$img = $img_array[$no];

$ext = substr($img, (strrpos($img, '.')+1));
if(($ext == 'mov') || ($ext == 'MOV') || ($ext == 'mpg') || ($ext == 'MPG') || ($ext == 'avi') || ($ext == 'AVI')) {
	$show_this = "<p>To watch the video, click here:</p><a href=\"$dir/$image_dir/$img\" target=_blank>$img</a>";
}
else {
	$show_this = "<a href=\"$dir/$image_dir/$img\" target=_blank><img alt=\"$img\" height=400 src=\"$dir/$image_dir/$img\"></a>";
}

if (file_exists("$dir/$descr_dir") && is_dir("$dir/$descr_dir")) {
	$descr_file = find_file("$dir/$descr_dir", $img);
	$descr = file_get_contents("$dir/$descr_dir/$descr_file");
}
if(!isset($descr) || ($descr == "")) {
	$descr = "Click on the picture to open it full size!";
}

$count = $no+1;
$maxcount = count($img_array);

echo "
<html>
<head>
  <style type=\"text/css\">
		body {
			border: 0;
			margin: 0;
			font-family: arial,helvetica,sans-serif;
			font-size: 12px;
			color: #808080;
			background-color: #ffffff;
		}
		table {
			border: 0;
			padding: 0;
			width: 660px;
			height: 475px;
		}
		td#img {
			width: 100%;
			height: 430px;
			vertical-align: middle;
			text-align: center;
		}
		td#descr {
			width: 100%;
			vertical-align: middle;
			text-align: center;
			color: #ffffff;
			background-color: #808080;
			font-size: 8pt;

		}
		td#number {
			width: 78px;
			vertical-align: middle;
			text-align: center;
			font-size: 10pt;
		}
		td#filename {
			width: 500px;
			vertical-align: middle;
			text-align: center;
			font-size: 10pt;
		}
		td#navi {
			width: 40px;
			vertical-align: middle;
			text-align: left;
			font-size: 10pt;
		}
		img {
			border: 1px black solid;
		}
		a:link,a:visited,a:active,a:hover {
			font-style: normal;
			font-weight: normal;
			color: #bbbb22;
			text-decoration: none;
		}
		a:active,a:hover {
			color: #bb2222;
		}
	</style>
</head>

<body>
<center>
	<table cellspacing=0>
		<tr>
			<td id=img colspan=4>
				$show_this
			</td>
		</tr>
		<tr>
			<td id=descr colspan=4>
				<small>$descr</small>
			</td>
		</tr>
		<tr>
			<td id=number>$count/$maxcount</td>
			<td id=filename>$img</td>
			<td id=navi>
";

if($no != 0) {
	$prev = $no-1;
	echo "
				<a href=\"$self?dir=$dir&no=$prev\">&lt;&lt;&lt;</a>
	";
}

echo "
	</td>
	<td id=navi>
";

if($no != ($maxcount-1)) {
	$next = $no+1;
	echo "
				<a href=\"$self?dir=$dir&no=$next\">&gt;&gt;&gt;</a>
	";
}

echo "			
			</td>
		</tr>
	</table>
	</span>
</center>
</body>
</html>
";

/************************************ FUNCTIONS **********************************/

// Get filenames from a directory into an array of strings
function get_files($dir) {	$handle = @opendir($dir);
	while ($file = readdir($handle)) {		if(!is_dir("$dir/$file")) {
			$array[] = $file;
		}
	}	closedir($handle);
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
