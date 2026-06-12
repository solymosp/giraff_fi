<?php

include_once('_php/_incl/incl.counter.php');
include_once('_php/_incl/incl.logger.php');

$counterstand = count_visit('_log/counter.txt');
@log_visit('_log/visits.log');

include_once('news_hu.html');

?>
