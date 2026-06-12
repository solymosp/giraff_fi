<?php

$image_dir = "images";
$thumb_dir = "thumbs";
$descr_dir = "description";

$dir = $_GET['dir'];

$img_array = get_files("$dir/$image_dir");
foreach($img_array as $img) {
	$newname = strip_accents($img);
	if($img != $newname) {
		rename("$dir/$image_dir/$img", "$dir/$image_dir/$newname");
	}
}

echo "
	<html>
	<head>
		<title>Pictures</title>
	</head>
	
	<frameset rows=\"473,107\" frameborder=no framespacing=0>
			<frame name=\"pic\" src=\"show_pic.php?dir=$dir\" marginwidth=0 marginheight=0 scrolling=no frameborder=0 noresize>
			<frame name=\"thumbs\" src=\"show_thumbs.php?dir=$dir\" marginwidth=0 marginheight=0 scrolling=auto frameborder=0 noresize>
	</frameset>
	
	<body>
	</body>
	</html>
";

/********************************* FUNCTIONS *****************************/

// Get filenames from a directory into an array of strings
function get_files($dir) {
	$handle = @opendir($dir);
	while ($file = readdir($handle)) {
		if(!is_dir("$dir/$file")) {
			$array[] = $file;
		}
	}	closedir($handle);
	sort($array);
	return $array;
}

// Clear $string from Hungarian accent marks and whitespaces, and put $string to lowercase
function strip_accents($string) {
	$replace_array = array ("+" => "_", "&"=>"_", "'"=>"", '"'=>"", ","=>"", " "=>"_", "á"=>"a", "é"=>"e", "í"=>"i", "ó"=>"o", "ö"=>"o", "õ"=>"o", "ú"=>"u", "ü"=>"u", "û"=>"u", "Á"=>"a", "É"=>"e", "Í"=>"i", "Ó"=>"o", "Ö"=>"o", "Õ"=>"o", "Ú"=>"u", "Ü"=>"u", "Û"=>"u");
	$string = strtolower(strtr($string, $replace_array));
	return $string;
}

?>
