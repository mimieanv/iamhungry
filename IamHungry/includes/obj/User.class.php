<?php

/**
 * Class User
 * Writed to deal with Iamhungry's User System
 * @version 2013 April, 23 
 */
class User extends sqlRow {
		
	/**
	 * User Class constructor
	 * Creates a new user using id, sso token or email
	 * @param int $user contains the id of the user to create
	 * @param string $user contains the sso token of the user to create
	 * @param string $user contains the email of the user to create
	 */
    public function __construct($user)
    {
        if((is_numeric($user)))
            $id_user = $user;
        else {
        	$user = DB::getInstance()->real_escape_string($user);
           	$id_user = sql_result("SELECT id FROM user WHERE email = '{$user}';");
        }
		
        if($id_user != -1)
            parent::__construct('user', $id_user);
        else
        	$this->error = "Impossible to find user: ({$user})";
    }
    
	public function getFullContentInArray()
	{
		return Array(
					'id'			=> $this->id,
					'email'			=> $this->email,
					'first_name'	=> $this->first_name,
					'last_name'		=> $this->last_name,
					'birthdate'		=> $this->birthdate,
					'sex'			=> ($this->sex == 1) ? 'M.' : 'Mme/Melle',
					'address'		=> $this->address,
					'date_creation'	=> $this->date_creation,
					'valid'			=> $this->valid,
					'cart'			=> $this->getCart()			
				);	
	}
    
    /**
     * Function checkPassword
     * Check if the password given by the user matches with the password in the database
     * @return boolean
     */
	public function checkPassword($passwd)
	{
		$hPasswd = hash('sha512', ($this->name . 'lasagnes' . $passwd));
        if($this->password == $hPasswd)
            return true;
        else
            return false;
	}
	
	/**
	 * Function getCart
	 * get the main list of the user
	 */
	public function getInHand() {
		$id = sql_result("");
		return ($id != -1) ? new IngredientsList($id) : null;
	}


	public function getIngredientsInhand()
	{
		$q_Ingredients = DB::getInstance()->query("SELECT id_ingredient, quantity FROM user_inhand WHERE id_user={$this->id} ;");

		while($qId = $q_Ingredients->fetch_object()) {
			$ing = new Ingredient($qId->id_ingredient);
			$ingredientsList[] = array('ing' => $ing, 'quantity' => $qId->quantity);
		}
				
		return ($ingredientsList != null) ? $ingredientsList : null;
	}
	
	// Nice
	public function getAllIngredientsQuantityInhand()
	{
		$lists = Array();
		$q = DB::getInstance()->query("SELECT id FROM ingredient ORDER BY name ;");
		$j = 0;
		while($ingredient = $q->fetch_object()) {
			$ing	= new Ingredient($ingredient->id);
			$qty	= $this->getIngredientQuantity($ing->id);
				
			$ingredients[]	= array('ing' => $ing, 'quantity' => $qty);			
		}
		
		return $ingredients;
	}
	
	public function hasIngredientInhand($_id)
	{
		return ((sql_result("select count(*) from user_inhand where id_user={$this->id} and id_ingredient={$_id}") == 1) ? true : false);
	}
	
	public function getIngredientQuantity($id)
	{
		$quantity = sql_result("select quantity from user_inhand where id_user={$this->id} and id_ingredient={$id}");		
		return ($quantity > 0 ) ? $quantity : 0;
	}
	
	public function addIngredientInhand($_idIngredient, $_quantity)
	{
		//TODO check id_ingredient exists
		
		if($this->hasIngredientInhand($_idIngredient))
			//if user already have this ingredient, UPDATE
			DB::getInstance()->real_query("UPDATE user_inhand SET quantity={$_quantity} WHERE id_user={$this->id} AND id_ingredient={$_idIngredient} ;");
		else
			//if not, INSERT
			DB::getInstance()->real_query("INSERT INTO user_inhand VALUES ({$this->id}, {$_idIngredient}, {$_quantity}) ;");
	}

	public function isRecipePlanned($_recipeId, $_mealId)
	{
		return ((sql_result("select count(*) from ass_recipe_meal where id_user={$this->id} and id_recipe={$_recipeId} and id_meal={$_mealId}") == 1) ? true : false);
	}
	
	public function getWeekPlanning()
	{
		$planning = Array();
		
		for($d=1; $d<=7; $d++) {			
    		$day = IAMHUNGRY::getInstance()->getDay($d);
    		$meals = IAMHUNGRY::getInstance()->getMeals($d);
   
    //$jour 			= Array();
	//$jour['day']	= $day;
    		
    		foreach($meals as $meal) {
   				$mealPlanned = $this->getRecipesPlanned($meal->id);
   	//echo '-------';
   	//var_dump($mealPlanned);
				if($mealPlanned == null)
					$mealPlanned = "Diet!";
				
				$plann = Array();
				$plann['day']		= $day;
				$plann['meal']		= $meal->mealOfDay;
				$plann['recipes']	= $mealPlanned;
				
				$planning[] = $plann;
				
	//$planning[]['day']		= $day;
	//$planning[]['meal']		= $meal->mealOfDay;
	//$planning[]['recipes']	= $mealPlanned;
	//$m++;
    		}
    	}
    	
    //var_dump($planning);
    	return $planning;
    }
    
    public function getRecipesPlanned($_idMeal)
    {
		$recipes = Array();
    	$q = DB::getInstance()->query("SELECT id_recipe FROM ass_recipe_meal WHERE id_meal={$_idMeal} AND id_user={$this->id} ;");

		while($r = $q->fetch_object())
			$recipes[] = new Recipe($r->id_recipe);	

		return $recipes;
    }
    
	public function planRecipe($_idRecipe, $_meal)
	{
		//TODO check id_recipe (and meal) exist
		
		if(!($this->isRecipePlanned($_idRecipe, $_meal)))
			//if user already have this meal planned, UPDATE
//			DB::getInstance()->real_query("UPDATE user_inhand SET quantity={$_quantity} WHERE id_user={$this->id} AND id_ingredient={$_idIngredient} ;");
//		else
			//if not, INSERT
			DB::getInstance()->real_query("INSERT INTO ass_recipe_meal VALUES ({$this->id}, {$_idRecipe}, {$_meal}) ;");
	}
	
/******* GROCERY LIST ********/

/**
 * Tested, ok!
 */
public function makeGroceryList()
{
	$this->getSumNeededIngredientsWeek();
	$this->differenceNeedHave();
}



// OK
public function getSumPlannedMealsOfWeek()
{
	$recipes = Array();
    $q = DB::getInstance()->query("SELECT id_recipe, count(id_recipe) as quantity FROM ass_recipe_meal WHERE id_user={$this->id} GROUP BY id_recipe ;");

	while($r = $q->fetch_object()) {
		
		$meals 					= Array();
		$recipe 				= new Recipe($r->id_recipe);
		$meals['ingredients']	= $recipe->getIngredients();
		$meals['quantity']		= $r->quantity;
		$recipes[]				= $meals;
	}
	
	return $recipes;
}

/**
 * Tested, ok!
 */
public function getSumNeededIngredientsWeek()
{
	// We do a new grocery list each time the user add ingredients or plan a meal
	// (disgusting way but... I lack time and all my code is disgusting! :s)
	DB::getInstance()->real_query("DELETE FROM grocery_list WHERE id_user=\"{$this->id}\" ");
	
	$recipes = Array();
    $q = DB::getInstance()->query("SELECT id_recipe, count(id_recipe) as quantity FROM ass_recipe_meal WHERE id_user={$this->id} GROUP BY id_recipe ;");

	while($r = $q->fetch_object()) {
//		var_dump($r);
		
		$ingredients	= Array();
		$recipe 		= new Recipe($r->id_recipe);
//		var_dump($recipe);
		
		foreach($recipe->getIngredients() as $ing) {
//			var_dump($ing);
			$ingredients['id']				= $ing['id'];
			$ingredients['name']			= $ing['name'];
			$ingredients['quantity']		= ($ing['quantity'] * $r->quantity);
			$ingredients['serving_unit']	= $ing['serving_unit'];
//			var_dump($ingredients);
			
			if(sql_result("SELECT count(*) FROM grocery_list WHERE id_user=\"{$this->id}\" and id_ingredient=\"{$ing['id']}\" ;") == 0) {
				DB::getInstance()->real_query("INSERT INTO grocery_list(id_user, id_ingredient, quantity) VALUES (\"{$this->id}\", \"{$ing['id']}\", \"{$ingredients['quantity']}\") ;");
			} else {
				DB::getInstance()->real_query("UPDATE grocery_list SET quantity = quantity + \"{$ingredients['quantity']}\" WHERE id_user=\"{$this->id}\" AND id_ingredient=\"{$ing['id']}\" ;");
			}
//			echo '<br />TADAA<br />!';
		}
		
		//$ingredientsNeeded[] = $ingredients;
	}
	
//	var_dump($recipes);
	//return $recipes;
}

/**
 * Tested, ok!
 */
public function differenceNeedHave()
{
	/*
	 * For each ingredient of grocery_list table, we check if the user has in his fridge.
	 * If yes, if more than needed we delete it.
	 * If yes, but less than needed we substract it.
	 * If no, Dummy
	 */	
	
	$ingredients = Array();
    	$q = DB::getInstance()->query("SELECT * FROM grocery_list WHERE id_user={$this->id} ;");

		while($ingNeeded = $q->fetch_object()) {
//			var_dump($ing);
			/* We check if the user has this ingredient in his fridge */
			$qtyHave = sql_result("SELECT quantity FROM user_inhand WHERE id_user=\"{$this->id}\" and id_ingredient=\"{$ingNeeded->id_ingredient}\" ;");
			if($qtyHave != -1) {
				if($qtyHave >= $ingNeeded->quantity) {
					// If we have more than that we need we delete the row in grocery_list
					DB::getInstance()->real_query("DELETE FROM grocery_list WHERE id_user=\"{$this->id}\" AND id_ingredient=\"{$ingNeeded->id_ingredient}\" ;");
				} else {
					// If we have less than that we need: UPDATE with 'needed' - 'have'
					DB::getInstance()->real_query("UPDATE grocery_list SET quantity = quantity - \"{$qtyHave}\" WHERE id_user=\"{$this->id}\" AND id_ingredient=\"{$ingNeeded->id_ingredient}\" ;");
				}
			}	
		}	
}

public function getGroceryList()
{
	$ingredients = Array();
    $q = DB::getInstance()->query("SELECT * FROM grocery_list WHERE id_user={$this->id} ;");

	while($ing = $q->fetch_object()) {
//		var_dump($ing);
		$ingredient['ing']		= new Ingredient($ing->id_ingredient);
		$ingredient['quantity']	= $ing->quantity;
		
		$ingredients[] = $ingredient;
		//$recipes[] = new Recipe($r->id_recipe);
	}
//	var_dump($ingredients);

	return $ingredients;	
}

/******* OTHER FUNCTIONS ********/

	/**
	 * Function delete (non-PHPdoc)
	 * @see includes/obj/sqlRow::delete()
	 */
	public function delete()
	{
        //sql_result()
		// TODO : Delete users settings & else...
		parent::delete();
	}

    /**
     * Generic setter for User Object (non-PHPdoc)
     * @see includes/obj/sqlRow::__set()
     */
    public function __set($key, $value)
    {
		if(isset($this->$key))
			parent::__set($key, $value);
		else
			$this->setSetting($key, $value);
		return 1;
    }
}
?>