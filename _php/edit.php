<?php

// set some variables
$self = $_SERVER['PHP_SELF'];
$file = $_GET['file'];

if(!file_exists($file)) {
	echo "edit.php: file '$file' doesn't exist!";
}

else {
	
	// if we have new comment, write file
	$buttontext = '"I wanna update"';
	if(isset($_POST['body'])) {
		$body = $_POST['body'];
		$fp = fopen($file, "w") or die("edit.php: unable to write file '$file'!");
		fwrite($fp, $body);
		fclose($fp);
		$buttontext = '"File updated!"';
	}
	
	// get current description
	$content = htmlspecialchars(file_get_contents($file));
	
	echo "
	
	<html>
	<head>
		<title>ADMIN - Edit file</title>
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
					border: 1px #bb1111 solid;
					padding: 7px;
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
					border: 1px #000070 solid;
					color: #808080;
				}
				input#button {
					background-color: #cccccc;
					color: #000070;
				}
				textarea {
					border: 1px #808080 solid;
					color: #808080;
				}
			</style>
	</head>
	
	<body onLoad=\"document.forms.edit_form.body.focus();\">
	<center>
		<form name=edit_form enctype=\"multipart/form-data\" action=$self?file=$file method=post>
		<fieldset>
			<legend id=main>
				ADMIN - Edit file: $file
			</legend>
			<br>
			<textarea name=body rows=40 cols=100>$content</textarea>
			<br>
			<br>
			<input id=button type=button onClick=\"this.form.submit();\" value=$buttontext>
		</fieldset>
		</form>
	
	</center>
	</body>
	</html>
	";
}

?>
