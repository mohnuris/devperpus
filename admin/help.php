<?php


// key to authenticate
define('INDEX_AUTH', '1');

// required file
require '../sysconfig.inc.php';
// IP based access limitation
require LIB.'ip_based_access.inc.php';
do_checkIP('smc');
// start the session
require SB.'admin/default/session.inc.php';
// session checking
require SB.'admin/default/session_check.inc.php';
// markdown library
require LIB.'parsedown/Parsedown.php';

//Load Markdown File  
$parsedown = new Parsedown();


if(isset($_GET['url']) && !empty($_GET['url'])) {		
	$file_path = HELP.'/'.$sysconf['default_lang'].'/'.$_GET['url'];
	if(!file_exists($file_path)|| !preg_match("/^.*\.(md)$/i", $file_path)) {
		echo __('File Not Found');
	} else {
		//Convert Markdown to HTML
		$markdown_text = file_get_contents($file_path); //bibliography/add-new-bibliography.md		
		echo Parsedown::instance()->setBreaksEnabled(true)->text($markdown_text); 
	}
} else {
		echo __('Cannot Access This File Directly');	
		exit;
}
