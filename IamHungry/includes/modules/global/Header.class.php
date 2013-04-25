<?php

/**
 * @version 2013 April, 24
 * @author jeremy
 */

class Header implements IModule
{
	public $userName;
	
    function __construct()
    {
    	$this->userName = IAMHUNGRY::getInstance()->user->name;
    }
    
    function preProcess($construct)
    {
		//
    }

    function display()
    {
    	echo "*********** WELCOME TO I.AM.HUNGRY! ***********<br /><br /><br />";
    	echo "Welcome back {$this->userName}!<br />";
    	echo "<a href=\"index.php\" >Home</a>
    		  #  <a href=\"index.php?page=registration\" >Register</a>
    		  #  Login
    		  #  <a href=\"index.php?page=recipes_all\">Recipes</a>
    		  #  <a href=\"index.php?page=fridge\">My fridge</a>
    		  #  <a href=\"index.php?page=planning\">My planning</a>
    		  #  <a href=\"index.php?page=grocery\">My shopping list</a>
    		<br /><br /><br /><br />
    	";
    }

}

?>