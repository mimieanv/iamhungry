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
    	 * to make the grocery list, we review all recipes of the week and add needed ingredients
    	 * juste have to do a difference with what it is in the fridge
    	 */
		IAMHUNGRY::getInstance()->user->getSumNeededIngredientsWeek();
    }
}

?>