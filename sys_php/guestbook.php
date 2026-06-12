<?php

////////////////////////////////////////////
$logs_dir = '../guestbook/logs';
$html_dir = '..';
$html_filename = 'guestbook';
$message_log = '../_log/guestbook.log';
////////////////////////////////////////////

$self = $_SERVER['PHP_SELF'];
unset($ip, $name, $location, $email, $web, $body);

// what do we wanna do
if(isset($_GET['do'])) {
	switch ($_GET['do']) {
		case 'store':
			$do = 'store';
			break;
		case 'sign':
			$do = 'sign';
			break;
	}
}
else {
	$do = 'update';
}

// if we have new comment, store it in a file
if($do == 'store')
{
	// set up variables
	$time = date("Y.m.d - H:i:s");
	$day = date("D");
	$ip = $_SERVER["REMOTE_ADDR"];
	$name = $_POST['name'];
	$location = $_POST['location'];
	$email = $_POST['email'];
	$web = $_POST['web'];
	$body = $_POST['body'];
	$do = 'update'; // new post needs update
	
/***********************************************
	if ($day == 'Mon') {$day = 'Hétfõ';}
	elseif ($day == 'Tue') {$day = 'Kedd';}
	elseif ($day == 'Wed') {$day = 'Szerda';}
	elseif ($day == 'Thu') {$day = 'Csütörtök';}
	elseif ($day == 'Fri') {$day = 'Péntek';}
	elseif ($day == 'Sat') {$day = 'Szombat';}
	elseif ($day == 'Sun') {$day = 'Vasárnap';}
************************************************/

	// write logfile
	$filename = 'GMT_'.gmdate("Y.m.d_H:i:s");
	$fp = fopen("$logs_dir/$filename", "w");
	fwrite($fp, $time."\n");
	fwrite($fp, $day."\n");
	fwrite($fp, $ip."\n");
	fwrite($fp, $name."\n");
	fwrite($fp, $location."\n");
	fwrite($fp, $email."\n");
	fwrite($fp, $web."\n");
	fwrite($fp, $body);
	fclose($fp);

	echo "<h3>Thanks for posting!</h3>";
}

// if we want to sign, show up html form
if($do == 'sign') {
	echo "
	
	<html>
	<head>
		<title>Guestbook</title>
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
				padding: 7px
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
	
	<body>
	<center>
	
		<form name=sign_guestbook enctype=\"multipart/form-data\" action=\"$self?do=store\" method=post>
			<fieldset>
				<legend id=main>
					Guestbook - new post
				</legend>
				<legend>
					Name:<br>
					<input type=text name=name><br>
				</legend>
				<legend>
					Location:<br>
					<input type=text name=location><br>
				</legend>
				<legend>
					Email address:<br>
					<input type=text name=email><br>
				</legend>
				<legend>
					Website:<br>
					<input type=text name=web value=http://><br>
				</legend>
				<br>
				<br>
				<legend>
					Comment:<br>
					<textarea name=body rows=5 cols=40></textarea><br><br>
				</legend>
				<legend>
				<input id=button type=button onClick=\"this.form.submit(); setTimeout(opener.location.reload(true),1000); self.close();\" value=\"push them button\">
				</legend>
			</fieldset>
		</form>
	
		<script type=\"text/javascript\">
			<!--
			document.sign_guestbook.name.focus();
			//-->
		</script>
	
	</center>
	</body>
	</html>
	";
}

// update guestbook html files if required
if($do == 'update') {	
	$files_array = find_html_files($html_dir, $html_filename);
	if($files_array == false) {
		echo "Can't find guestbook html files '$guestbook' in '$html_dir'!";
	}
	else {
		require_once("_class/class.replace.php");
		$replace = &new replace;	// initialize replace
		require_once("_class/class.html_parser.php");
		$parser = &new html_parser;	// initialize parse_string
		
		// set up $log
		$gmtime = '(GMT)'.gmdate("Y.m.d-H:i:s");
		$time = '(LOCAL)'.date("Y.m.d-H:i:s");
		$log = $gmtime.' * '.$time."\n\n";

		foreach($files_array as $file) {	// loop for each html guestbook file
			unset($formatted_array, $output_array);
			$logs_array = get_files($logs_dir);
			if($logs_array == false) {
				echo "No guestbook logs found in '$logs_dir'!";
			}
			else {
				$log_count = 1;
				foreach($logs_array as $logfile) {	// loop for each logfile
				
					// read logfile
					$fp = fopen("$logs_dir/$logfile", "r") or die("Guestbook logfile '$logfile' not readable!");
					$time = rtrim(fgets($fp));
					$day = rtrim(fgets($fp));
					$ip = rtrim(fgets($fp));
					$name = rtrim(fgets($fp));
					$location = rtrim(fgets($fp));
					$email = rtrim(fgets($fp));
					$web = rtrim(fgets($fp));
					$body = rtrim(fread($fp, filesize("$logs_dir/$logfile")));
					fclose($fp);

					// do some variable adjustments
					if($location == "") {
						$location = 'unknown';
					}
					if($parser -> make_link($email, 0, 'mail') == false) {	// make a link from $email if valid
						$email = "mail";
					}
					if($parser -> make_link($web, 0, 'web') == false) {	// make a link from $web if valid
						$web = "web";
					}

					$body = $parser -> parse_string($body, 40);	// process message body

					// format log
					$formatted_array[] = array(
						"<div class=head>($day, $time) <span class=highlight>$name</span></div>",
						"<div class=small>|  $email  |  $web  |   <a href=\"JavaScript://\" OnClick=\"popup_guestbook('_php/guestbook.php?do=sign');\">new post</a>  |  #$log_count</div>",
						"<div class=small>Location: $location</div>",
						"<div class=small>IP address: $ip</div>",
						"<div class=body><br>$body<br></div><br>");

					++$log_count;
				}

				$formatted_array = array_reverse($formatted_array);
				foreach($formatted_array as $lines_array) {
					foreach($lines_array as $line) {
						$output_array[] = $line;
					}
				}
				$log = $log.$replace->replace_section("$html_dir/$file", 'LOGS', $output_array);
			}
		}
		// write $message_log
		$fp = fopen($message_log, "a") or die("guestbook.php: '$logfile' nem nyitható meg!");
		fwrite($fp, $log);
		fclose($fp);
		
		// print log of update
		$log = ereg_replace("\n","<br>\n",$log);		// add <BR> to newlines
		echo $log;
	}
}

// Get filenames from a directory into an array of strings
function get_files($dir) {
	$handle = @opendir($dir);
	while ($file = readdir($handle)) {
		if(!is_dir($dir."/".$file)) {
			$array[] = $file;
		}
	}
	closedir($handle);
	if (is_array($array))	{
		sort($array);
		return $array;
	}
	return false;
}

// Find html files beginning with a certain pattern in a directory
function find_html_files($dirname, $pattern) {
	$dir = dir($dirname);
	while ($file = $dir->read()) {
		if (!is_dir($file) && (substr($file, -4) == "html") && (substr($file, 0, strlen($pattern)) == $pattern))	{
			$array[] = $file;						
		}
	}
	if (is_array($array))	{
		sort($array);
		return $array;
	}
	return false;
}

?>
