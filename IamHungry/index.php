<?php
	require_once('config.inc.php');
	include('includes/script_base.php');
	
//echo  $_SERVER["PHP_SELF"];		//debug
	if(isset($_REQUEST['page']))
		$page = (file_exists(FOLDER_BASE_SITE.'/pages/'.$_REQUEST['page'].'.php')) ? $_REQUEST['page'] : 'homepage';
	else
		$page = 'homepage';
		
	include_once('/pages/'.$page.'.php');
	
?>