<?php

$site_root = '..';
$index = 'index.html';
$logfile = '../_log/update.log';

$menu_array_hu = array(
	"<a class=on href=\"news_hu.html\">.: hírek :.</a><br><br>",
	"<a class=on href=\"me_hu.html\">.: én :.</a><br><br>",
	"<a class=on href=\"tomgreen_hu.html\">.: tom green :.</a><br><br>",
	"<a class=on href=\"finland_hu.html\">.: finnország :.</a><br><br>",
	"<a class=on href=\"transsyb_hu.html\">.: transszibéria :.</a><br><br>",
	"<a class=on href=\"aviation_hu.html\">.: repülés :.</a><br><br>",
	"<a class=on href=\"guestbook_hu.html\">.: vendégkönyv :.</a><br><br>",
	"<p>A nattezsvír figye' ám tígedet!</p>",
	"<img src=\"_img/giranim.gif\">"
);
	
$menu_array_en = array(
	"<a class=on href=\"news_en.html\">.: welcome :.</a><br><br>",
	"<a class=on href=\"me_en.html\">.: me :.</a><br><br>",
	"<a class=on href=\"tomgreen_en.html\">.: tom green :.</a><br><br>",
	"<a class=on href=\"finland_en.html\">.: finland :.</a><br><br>",
	"<a class=on href=\"transsyb_en.html\">.: transsyberia :.</a><br><br>",
	"<a class=on href=\"aviation_en.html\">.: aviation :.</a><br><br>",
	"<a class=on href=\"guestbook_en.html\">.: guestbook :.</a><br><br>",
	"<p>Da big brada is watchin' ya!</p>",
	"<img src=\"_img/giranim.gif\">"
);

$menu_array_de = array(
	"<a class=on href=\"news_de.html\">.: wilkommen :.</a><br><br>",
	"<a class=on href=\"me_de.html\">.: ich :.</a><br><br>",
	"<a class=on href=\"tomgreen_de.html\">.: tom green :.</a><br><br>",
	"<a class=on href=\"finland_de.html\">.: finnland :.</a><br><br>",
	"<a class=on href=\"transsyb_de.html\">.: transsiberien :.</a><br><br>",
	"<a class=on href=\"aviation_de.html\">.: segelflug :.</a><br><br>",
	"<a class=on href=\"guestbook_de.html\">.: gästebuch :.</a><br><br>",
	"<p>Da big brada is watchin' ya!</p>",
	"<img src=\"_img/giranim.gif\">"
);

/*************************************** VARIABLES ********************************************************/
// which directory to process
if(isset($_GET['dir']))
{
	$dir = $_GET['dir'];
}
else
{
	$dir = $site_root;
}

// set up $file_array and $page_array
$file_array = get_html_files($dir, $index);
foreach($file_array as $file)
{
	$page_array[] = substr($file, 0, strrpos($file, "."));
}

// set up $log
$gmtime = '(GMT)'.gmdate("Y.m.d-H:i:s");
$time = '(LOCAL)'.date("Y.m.d-H:i:s");
$log = $gmtime.' * '.$time."\n\n";

/*************************************** PROCESSING ****************************************************/
// loop for every file
foreach($page_array as $key => $page)
{
	// set up $log

	// determine pagetitle, language and other languages
	$page_title = substr($page, 0, strrpos($page, "_"));
	$page_lang = substr($page, -2);
	unset($lang_array);
	foreach($page_array as $item)
	{
		if(substr($item, 0, strrpos($item, "_")) == $page_title)
		{
			$lang_array[] = substr($item, -2);
		}
	}

	// create the flags section to be inserted unset($flags_array)
	unset($flags_array);
	foreach($lang_array as $lang)
	{
		if($lang == $page_lang)
		{
			continue;
		}
		else
		{
			$flags_array[] = "<a href=\"$page_title" . "_" . "$lang.html\"><img src=\"_img/flag_$lang.jpg\"></a>";
		}
	}
	
	// initialize class.replace 
	require_once('_class/class.replace.php');
	$replace = &new replace;

	/***************************************************************************************************/
	// do some mass replacements and log it
	$log = $log.$replace -> mass_replace("www.aeroegom.tk", "aeroegom.extra.hu", "$dir/$file_array[$key]");

	// insert flags, or leave a blank row if nothing to insert, log results
	if(isset($flags_array))
	{
		$log = $log.$replace -> replace_section("$dir/$file_array[$key]", 'FLAGS', $flags_array); 
	}
	else
	{
		$log = $log.$replace -> replace_section("$dir/$file_array[$key]", 'FLAGS', "\n"); 
	}
	
	// insert the menus according to $page_lang and log operation
	$log = $log.$replace -> replace_section("$dir/$file_array[$key]", 'MENU', ${"menu_array_$page_lang"});
	/***************************************************************************************************/
}

// write logfile
$fp = fopen($logfile, "a") or die("update.php: '$logfile' nem nyitható meg!");
fwrite($fp, $log);
fclose($fp);

// print log of update
$log = ereg_replace("\n","<br>\n",$log);		// add <BR> to newlines
echo $log;
$log = "";

/************************************** FUNCTIONS *************************************************/

// Get html filenames from a directory into an array of strings, excluding $exclude
function get_html_files($dir, $exclude)
{
	$handle = @opendir($dir);
	while ($file = readdir($handle))
	{
		if(isset($exclude) && ($file == $exclude))
		{
			continue;
		}
		if(!is_dir($dir."/".$file) && (substr($file, -4) == "html"))
		{
			$array[] = $file;
		}
	}
	closedir($handle);
	sort($array);
	return $array;
}

// Find first filename beginning with a certain pattern in a directory
function find_file($dirname, $pattern)
{
	$dir = dir($dirname);
	while ($file = $dir->read())
	{
		if (substr($file, 0, strlen($pattern)) == $pattern)
		{
			return $file;
		}
	}
	return false;
}

?>
