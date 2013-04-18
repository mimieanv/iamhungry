<?php

/**
 * Class DB
 * @author Johan Massin
 * @copyright YouMeTrip, 2011
 */
class DB extends mysqli {
	
	private static $_instance = null;
	
	/**
	 * Function 
	 * Private method : prevents direct creation of DB object
	 */
	private function __construct($host, $login, $password, $database)
	{
		mysqli_report(DEBUG_MODE ? MYSQLI_REPORT_ERROR : MYSQLI_REPORT_OFF);
		parent::__construct($host, $login, $password, $database);
		if(mysqli_connect_errno()) 
			die(mysqli_connect_error().' : '.mysqli_connect_errno()); 
	}
	
	/**
	 * Function getInstance
	 * Singleton method
	 */
	public static function getInstance()
	{
		if(is_null(self::$_instance)) {		// Instanciation de la classe
			$c = __CLASS__;
			self::$_instance = new $c(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		}
		return self::$_instance;
	}
	
	public function __clone()
	{
		throw new Exception("Cannot clone ".__CLASS__." class");
	} 
	
}

?>