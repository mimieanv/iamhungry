<?php

/**
 * @version 2013 April, 23
 */

class ShowRecipe implements IModule
{
	private $recipe;
	
    function __construct()
    {
    	$this->recipe = new Recipe($id_recipe);
    }
    
    function preProcess($construct)
    {
    	//
    }

    function display()
    {
    	
    }

}

?>