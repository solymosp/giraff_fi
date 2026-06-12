<?php

/////////////////////////
$image_dir = 'images';
$thumb_dir = 'thumbs';
$descr_dir = 'descriptions';
$video_thumb = '../_img/video.jpg';
$nothumb_thumb = '../_img/nothumb.jpg';
/////////////////////////

// Fetch files into arrays
$dir = $_GET['dir'];
$img_array = get_files("$dir/$image_dir");
if (file_exists("$dir/$thumb_dir")) {
	$thumb_array = get_files("$dir/$thumb_dir");
}
else {
	mkdir("$dir/$thumb_dir", 0777);
	$thumb_array = false;
}

// Open HTML page with CSS
echo "
<html>
<head>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-2\">
	<style type=\"text/css\">
		body {
			background-color: #bb2222;
			border: 0;
			margin: 0;
		}
		table {
			padding: 0;
			border: 0;
			border-top: 1px black solid;
		}
		td {
			vertical-align: middle;
			text-align: center;
		}
		img {
			border: 1px #000000 solid;
		}
	</style>
</head>

<body>
	<table cellspacing=0>
	<tr>
";

// Parse thumbnails
$no = 0;
foreach($img_array as $img) {
	
	// Prepare tooltip
	if (file_exists("$dir/$descr_dir/$img.txt")) {
		$tooltip = file_get_contents("$dir/$descr_dir/$img.txt");
	}
	else {
		$tooltip = $img;
	}
	$tooltip = str_replace('"', '&quot;', $tooltip);
	
	// Prepare thumbnail picture
	$count = $no+1;
	$ext = substr($img, (strrpos($img, '.')+1));
	if(($ext == 'mov') || ($ext == 'MOV') || ($ext == 'mpg') || ($ext == 'MPG') || ($ext == 'avi') || ($ext == 'AVI')) {
		$thumb = $video_thumb;
	}
	else {
		if(!$thumb_array || !in_array($img, $thumb_array)) {
			if(makethumbfromjpeg("$dir/$image_dir/$img", 80, "$dir/$thumb_dir")) {
				$thumb = "$dir/$thumb_dir/$img";
			}
			else {
				$thumb = $nothumb_thumb;
			}
		}
		else {
			$thumb = "$dir/$thumb_dir/$img";
		}
	}

	// Paste picture with tooltip
	echo "
		<td>
			<a href=\"show_pic.php?dir=$dir&no=$no\" target=pic><img src=\"str_on_jpeg.php?img=$thumb&str=$count\" alt=\"$img\" title=\"$tooltip\"></a>
		</td>
	";
	$no++;
}

// Close HTML page
echo "
	</tr>
	</table>
</body>
</html>
";

/************************************************** FUNCTIONS **************************************************/

// Get filenames from a directory into an array of strings
function get_files($dir) {
	$handle = @opendir($dir);
	while ($file = readdir($handle)) {
		if(!is_dir("$dir/$file")) {
			$array[] = $file;
		}
	}
	closedir($handle);
	if(isset($array) && is_array($array)) {
		sort($array);
		return $array;
	}
	else {
		return false;
	}
}

// Create a thumbnail from a jpeg $image with a given $thumbheight to a given $thumbdir
function makethumbfromjpeg($image, $thumbheight, $thumbdir) {
	if(file_exists($image) && is_readable($image) && file_exists($thumbdir) && is_dir($thumbdir) && is_writeable($thumbdir)) {
		$src_img = imagecreatefromjpeg($image);
		$origh = imagesy($src_img);
		$origw = imagesx($src_img);
		$new_h = $thumbheight;
		$new_w = $origw/($origh/$new_h);
		$dst_img = imagecreatetruecolor($new_w,$new_h);
		imagecopyresized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,$origw,$origh);
		imagejpeg($dst_img, $thumbdir.'/'.basename($image));
		return true;
	}
	else {
		return false;
	}
}
										

?>
