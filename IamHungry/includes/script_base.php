<?php

	// Set the default timezone
	date_default_timezone_set ('Europe/Paris');
	
	// Start a session
	if(session_id() == '')
		session_start();
	
	// MySQL Server connexion, UTF-8 needs
	DB::getInstance()->real_query("SET NAMES 'UTF8' ;");
	DB::getInstance()->real_query("SET CHARACTER_SET_SERVER = 'UTF8' ;");
	DB::getInstance()->real_query("SET COLLATION_SERVER ='utf8_general_ci' ;");
	
	// Autoload of objects
	function __autoload($class_name) {
		require_once (FOLDER_BASE_SITE.'/includes/obj/'.$class_name . '.class.php');
	}
	
	// Tools
	require_once(FOLDER_BASE_SITE.'/includes/tools/sql.php');
	require_once(FOLDER_BASE_SITE.'/includes/tools/misc.php');
	
	// Instanciation of IAMHUNGRY object
	$IAMHUNGRY = IAMHUNGRY::getInstance();

?>