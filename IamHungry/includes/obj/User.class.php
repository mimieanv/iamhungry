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
		//TODO check id_ingredient exist
		
		if($this->hasIngredientInhand($_idIngredient))
			//if user already have this ingredient, UPDATE
			//DB::getInstance()->real_query("UPDATE user_inhand SET quantity={$_quantity} WHERE id_user={$this->id} AND id_ingredient={$_idIngredient} ;");
			echo("UPDATE user_inhand SET quantity={$_quantity} WHERE id_user={$this->id} AND id_ingredient={$_idIngredient} ;");
		else
			//if not, INSERT
			//DB::getInstance()->real_query("INSERT INTO user_inhand VALUES ({$this->id}, {$_idIngredient}, {$_quantity}) ;");
			echo("INSERT INTO user_inhand VALUES ({$this->id}, {$_idIngredient}, {$_quantity}) ;");
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
    
/******* GROCERY LIST ********/
    
public function makeGroceryList()
{
	
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

// disgusting :s
public function getSumNeededIngredientsWeek()
{
	$recipes = $this->getSumPlannedMealsOfWeek();
	
//	var_dump($recipes);
	foreach($recipes as $recipe) {
//		var_dump($recipe);
		foreach($recipe as $ingredient) {
//			var_dump($ingredient); echo '<br />ppp<br />';
			foreach($ingredient as $prout) {
//				var_dump($prout);
				$sum 				= Array();
				$sum['id']			= $prout['id'];
				$sum['quantity']	= $prout['quantity'] * 1;
			}
		}
	}
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