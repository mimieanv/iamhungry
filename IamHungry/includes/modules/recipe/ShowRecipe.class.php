<?php

/**
 * @version 2013 April, 23
 */

class ShowRecipe implements IModule
{
	private $recipe;
	
    function __construct()
    {
    	//
    }
    
    function preProcess($id_recipe)
    {
    	$this->recipe = new Recipe($id_recipe);
    }

    function display()
    {
    	echo 'Recipe: '. $this->recipe->name .'<br />';
		var_dump($this->recipe->getFullContentInArray());
    }

}

?>