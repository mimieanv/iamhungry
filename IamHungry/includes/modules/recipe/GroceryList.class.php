<?php

/**
 * @version 2013 April, 24
 */

class GroceryList implements IModule
{
	private $user;
	
    function __construct()
    {
    	$this->user = IAMHUNGRY::getInstance()->user;
    }
    
    function preProcess($construct)
    {
	}

    function display()
    {
    	/*
    	 * each update of mealplanning or inhand, we have to update the grocerylist
    	 * 		IAMHUNGRY::getInstance()->user->makeGroceryList();
    	 * to make the grocery list, we review all recipes of the week and add needed ingredients
    	 * juste have to do a difference with what it is in the fridge
    	 */

    	$gList = IAMHUNGRY::getInstance()->user->getGroceryList();
		
		echo "Don't forget to buy that in order to follow your planning:<br /><br />";
		foreach($gList as $ing) {
			echo $ing['ing']->name .' - '. $ing['quantity'] .' '. $ing['ing']->serving_unit .'<br />';
		}
		echo '<br /><br />';
    }
}

?>