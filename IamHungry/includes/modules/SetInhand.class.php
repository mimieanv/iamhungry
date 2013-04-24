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
?>

	<form name="choice" action="index.php?page=inhand&action=" method="POST">
		<select>
<?php
		$ingredients = IAMHUNGRY::getInstance()->getAllIngredients();
		foreach($ingredients as $ingredient) {
			echo 	"<option value=\"{$ingredient->id}\">{$ingredient->name} (in {$ingredient->serving_unit})</option>
					<input type=\"text\" name=\"mail\" size=\"25\">" ;
		}
?>
		</select>
	</form>
	
<?php
    }

}

?>