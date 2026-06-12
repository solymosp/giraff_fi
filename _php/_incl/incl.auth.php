<?php

function authorize($login, $password, $realm);
	if ((!isset($_SERVER['PHP_AUTH_USER'])) || (!isset($_SERVER['PHP_AUTH_PW'])) || ($_SERVER['PHP_AUTH_USER'] != $login) || ($_SERVER['PHP_AUTH_PW'] != $password)) {
		header( "WWW-Authenticate: Basic realm=\"$realm\"" );
		header( "HTTP/1.1 401 Unauthorized" );
		echo 'Authorization Required!';
		return false;
	}
	return true;
}

?>
