<?php

/**
 * @version 2013 April, 24
 */

class testHomepage implements IModule
{
	private $user;
	
    function __construct()
    {
    	$this->user = IAMHUNGRY::getInstance()->user;
    	echo 'prout';
    }
    
    function preProcess($construct)
    {
    	//
    }

    function display()
    {
?>

Welcome to IAmHungry. This is the homepage, but I'm currently too lazy to do something better. Sorry for the convenience. (I'm not)
	
<?php
    }

}

?>