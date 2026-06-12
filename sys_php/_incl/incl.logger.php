<?php
	
function log_visit($logFile) {

	// set variables
	$gmtime = '(GMT)'.gmdate("Y.m.d-H:i:s").' * ';
	$time = '(LOCAL)'.date("Y.m.d-H:i:s").' * ';

	$ip = $_SERVER["REMOTE_ADDR"].' * ';
	$referer = $_SERVER["HTTP_REFERER"].' * ';
	$browser = $_SERVER["HTTP_USER_AGENT"].' * ';
	$lang = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
	
	$log = $gmtime.$ip.$referer.$browser.$time.$lang."\n";
	
	// write log to $logFile
	$fp = fopen($logFile, "a") or die("log_visit(): '$logFile' nem nyitható meg!");
	fwrite($fp, $log);
	fclose($fp);
}

?>
