<?php

/**
 * @version 2013 April, 24
 * @author jeremy
 */

class AllRecipes implements IModule
{
	
    function __construct()
    {
    	//
    }
    
    function preProcess($construct)
    {
		//
    }

    function display()
    {
    	echo 'Recipes availables:<br /><br />';
    	
		$recipes = IAMHUNGRY::getInstance()->getAllRecipes();
		foreach($recipes as $recipe)
			echo "<a href=\"index.php?page=recipe&id_recipe={$recipe->id}\">{$recipe->name}</a>";
    }

}

?>