<?php
	
// counts visit and returns current counterstand
function count_visit($counterFile) {

	session_set_cookie_params(1800);	// cookie lifetime in sec
	session_start();

	// if no counterfile, create one
	if(!file_exists($counterFile)) {
		$fp = fopen($counterFile, "w");
		fwrite($fp, '0');
		fclose($fp);
	}

	// read counterstand, increment if no session cookie
	$fp = fopen($counterFile, "r+");
	$counterstand = fgets($fp, 10);
	if(!session_is_registered('counter_ip')) {
		$counterstand++;
		rewind($fp);
		fwrite($fp, $counterstand);
		session_register('counter_ip');
	}
	fclose($fp);
	
	return $counterstand;
}
?>
