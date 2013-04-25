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
	}

    function display()
    {
        if(isset($_REQUEST['action'])) {
    		switch($_REQUEST['action']) {

				case 'addIngredients' :
					foreach($_POST as $id_ing => $qty)
						if(is_numeric($qty) && $qty > 0)
							IAMHUNGRY::getInstance()->user->addIngredientInhand($id_ing, $qty);
							
				break;
    		}
        }

   		$allIngredients = IAMHUNGRY::getInstance()->user->getAllIngredientsQuantityInhand();
		echo "What's in your fridge?
			<form name=\"choice\" action=\"index.php?page=fridge&action=addIngredients\" method=\"POST\">";

    		foreach($allIngredients as $ingredient) {
				echo "{$ingredient['ing']->name}: <input type=\"text\" name=\"{$ingredient['ing']->id}\" value=\"{$ingredient['quantity']}\"> {$ingredient['ing']->serving_unit}<br />" ;
			}
		
   		echo '<button type="submit">Refill!</button>
		</form>';

    }

}

?>