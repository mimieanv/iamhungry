<?php

/**
 * @version 2013 April, 24
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

    //TODO add a meal in the plan
    //TODO show meal plan of the week
    //TODO show grocery list
    //TODO design?
    function display()
    {
    	
     	if(isset($_REQUEST['action'])) {
    		switch($_REQUEST['action']) {

				case 'plann' :
					var_dump($_POST);
					foreach($_POST as $id_ing => $qty)
						if(is_numeric($qty) && $qty > 0)
							IAMHUNGRY::getInstance()->user->addIngredientInhand($id_ing, $qty);
							
				break;
    		}
        }
        
        
    	$rec = $this->recipe->getFullContentInArray();
//    	var_dump($rec);
    	echo 'Recipe: '. $rec['name'] .'<br /><br />'. $rec['description'] .'<br /><br />Ingredients:<br />';
    	
   		foreach($rec['ingredients'] as $ing) {
    		echo $ing['name'] .' ('. $ing['quantity'] .' '. $ing['serving_unit'] .')<br />'; 
    	}
    	
    	echo '
    		<br /> Instructions:<br />'. $rec['instructions'] .'<br />
    		<br />Wanna plann this recipe?<br />'
    	;
    	
    	if(IAMHUNGRY::getInstance()->user != null) {
    		echo "<form name=\"choice\" action=\"index.php?page=recipe&id_recipe={$this->recipe->id}&action=plann\" method=\"POST\">";
    		for($d=1; $d<=7; $d++) {
    			echo IAMHUNGRY::getInstance()->getDay($d) .'<br />';
    			
    			$meals = IAMHUNGRY::getInstance()->getMeals($d);
    			foreach($meals as $meal) {
  	 				if(IAMHUNGRY::getInstance()->user->isRecipePlanned($this->recipe->id, $meal->id))
    					echo "<input type=\"checkbox\" name=\"meal\" value=\"{$meal->id}\" checked >{$meal->mealOfDay}<br>";
    				else
     					echo "<input type=\"checkbox\" name=\"meal\" value=\"{$meal->id}\">{$meal->mealOfDay}<br>";
    			}
    			
    			echo '<br />';
    		}
    		
    		echo "
    			<button type=\"submit\">Plan this recipe!</button>
    			</form>
    		";
    	}
    	
    }
}

?>