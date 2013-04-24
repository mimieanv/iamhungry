<?php

/**
 * @version 2013 April, 23
 */

class SetInhand implements IModule
{
	private $user;
	
    function __construct()
    {
    	$this->user = IAMHUNGRY::getInstance()->user;
    }
    
    function preProcess($construct)
    {
    	//
    }

    function display()
    {
    	$ingredients = IAMHUNGRY::getInstance()->getAllIngredients();
		echo "Add some news ingredients which are in your fridge:
			<form name=\"choice\" action=\"index.php?page=inhand&action=\" method=\"POST\">";
		for ($i = 1; $i <= 10; $i++) {
?>
			<select>

<?php
			echo "<option value=\"0\">nothing</option>" ;
			foreach($ingredients as $ingredient) {
				echo "<option value=\"{$ingredient->id}\">{$ingredient->name} (in {$ingredient->serving_unit})</option>" ;
			}
			
			echo "<input type=\"text\" name=\"mail\" size=\"25\"><br />";
			echo "</select>";
		}
		echo "</form>";

    }

}

?>