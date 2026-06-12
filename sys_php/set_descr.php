<?php

///////////////////////////////////
$image_dir = "images";
$descr_dir = "descriptions";
///////////////////////////////////

// set some variables
$self = $_SERVER['PHP_SELF'];
$img = $_GET['file'];

$picdir = substr($img, 0, strrpos($img, "/"));
$picfile = substr($img, (strrpos($img, "/")+1));

$desdir = $picdir.'/../'.$descr_dir;
$desfile = find_file($desdir, $picfile);

if($desfile == false) {
	$desfile = $picfile.".txt";
}

$descr = $desdir.'/'.$desfile;

// if we have new comment, update the file
if(isset($_POST['body'])) {
	$body = $_POST['body'];
	$fp = fopen($descr, "w");
	fwrite($fp, $body);
	fclose($fp);
}

// get current description
if(file_exists($descr)) {
	$description = file_get_contents($descr);
}
else {
	$description = "";
}

echo "

<html>
<head>
	<title>ADMIN Set description</title>
	<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-2\">
	                <style type=\"text/css\">
				body {
					border: 0;
					margin: 6;
					font-family: arial,helvetica,sans-serif;
					font-size: 14px;
					color: #000000;
					background-color: #ffffff;
				}
				fieldset {
					border: 1px #bb1111 dashed;
				}
				legend {
					font-size: 12px;
					color: #000070;
				}
				legend#main {
					font-size: 14px;
					color: #bb1111;
					font-weight: bold;
				}
				input {
					border: 1px #000070 dotted;
					color: #808080;
				}
				input#button {
					background-color: #cccccc;
					color: #000070;
				}
				textarea {
					border: 1px #808080 dotted;
					color: #808080;
				}
				img {
					border: 1px black solid;
				}
			</style>
				
</head>

<body onLoad=\"document.forms.admin_form.body.focus(); document.forms.admin_form.body.select();\">
<center>

	<a href=\"#\" onClick=\"self.close(); return false;\"> <img alt=\"close window\" height=480 src=\"$img\"></a>

	<form name=admin_form enctype=\"multipart/form-data\" action=$self?file=$img method=post>
	<fieldset>
		<legend id=main>
			Admin - change description
		</legend>
		<legend>
			Képleírást ide hoci:
		</legend>
		<textarea name=body rows=2 cols=50>$description</textarea>
		<br>
		<br>
		<input id=button type=button onClick=\"this.form.submit(); self.close(); setTimeout(opener.location.reload(true),1000);\" value=\"nyomi\">
	</fieldset>
	</form>

</center>
</body>
</html>
";

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
