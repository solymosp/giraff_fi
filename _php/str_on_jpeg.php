<?php

/* Writes text upon a JPG image and dumps image stream */

// string position
$x = 3;
$y = 0;

// string color
$r = "255";
$g = "255";
$b = "255";

//if (isset($_GET['img']) && isset($_GET['str']) && file_exists($_GET['img'])){

	// open image, set textcolor
	$im = imagecreatefromjpeg($_GET['img']);
	$textcolor = imagecolorallocate($im, $r, $g, $b);

	// write the string
	imagestring($im, 5, $x, $y, $_GET['str'], $textcolor);

	// output the image
	header("Content-type: image/jpg");
	imagejpeg($im);
	
/*}

else {
	
	echo "Couldn't write string on image: no 'img' or 'str' specified, or file doesn't exist!";
	
}
*/
?>
