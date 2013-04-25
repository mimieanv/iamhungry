<?php

class IAMHUNGRY
{
	private static $_instance = null;
	
	public $user;
	public $time_start;
	public $pageHandler;
	private $modules = Array();
	
	private function __construct()
	{
		$this->time_start	= microtime(true);
		if(isset($_SESSION['id_user'])) {
			$this->user = new User($_SESSION['id_user']);
			$this->invite = ($this->user->valid == 0);		
		}
	}

	public static function getInstance()
	{
		if(is_null(self::$_instance))
			self::$_instance = new IAMHUNGRY();
		return self::$_instance;
	}
	
	public function isInvite()
	{
		return ($this->user == null) ? true : false;
	}
	
/**********************
 * USERS FUNCTIONS
 **********************/
	
	/**
	 * Function addUser()
	 * Create a new customer in the database
	 * return id of the new customer
	 */
	public function createUser($email, $name, $password) {		
		DB::getInstance()->real_query("INSERT INTO user (email, name, password)
											VALUES(
												'{$email}',
												'{$name}',
												'{$password}'
											); ");

		return $id_user = DB::getInstance()->insert_id;	// l'id du dernier insert dans la db
	}	
	
	
/*********************
 * SEARCH FUNCTIONS *
 *********************/
	
	/**
	 * Function search*Recipe
	 * search a recipe in the DB
	 * @param <string> $search
	 * @param <int> $max
	 */
	public function searchRecipe($search, $max = '')
	{
		$products = Array();
		$maxReq = ($max) ? "LIMIT 0, {$max}" : '';
//TODO query
		$q_Products = DB::getInstance()->query("SELECT id FROM product WHERE online=1 AND (name LIKE '%{$search}%' OR description LIKE '%{$search}%') ORDER BY name ASC {$maxReq} ;");
		while($qId = $q_Recipes->fetch_object())
			$recipes[] = new Recipe($qId->id);
		return (isset($recipes)) ? $recipes : null;	
	}
	
	public function getCountIngredients() {
		return sql_result("select count(*) from ingredient ;");
	}
	
	public function getAllIngredients($max = 0)
	{
		$lists = Array();
		$maxReq = ($max) ? 'LIMIT 0, {$max}' : '';
		$q = DB::getInstance()->query("SELECT id FROM ingredient ORDER BY name {$maxReq} ;");
		$j = 0;
		while($ingredient = $q->fetch_object()) {
			$ing			= new Ingredient($ingredient->id);
			$ingredients[]	= array('ing' => $ing, 'quantity' => '0');
			
			//$ingredients[]	= $ing;
		}
		return $ingredients;
	}
	
	public function getAllRecipes($max = 0)
	{
		$lists = Array();
		$maxReq = ($max) ? 'LIMIT 0, {$max}' : '';
		$q = DB::getInstance()->query("SELECT id FROM recipe ORDER BY name {$maxReq} ;");
		$j = 0;
		while($recipe = $q->fetch_object()) {
			$i    		= new Recipe($recipe->id);
			$recipes[]	= $i;
		}
		return $recipes;
	}
	
	public function getDay($_idDay)
	{
		$d = sql_query("select dayOfWeek from week_meals where dayOfWeek_id =\"{$_idDay}\" limit 0,1 ;");
		return $d[0]['dayOfWeek'];
	}
	
	public function getMeals($_idDay)
	{
		$meals = Array();
		$q = DB::getInstance()->query("SELECT * FROM week_meals WHERE dayOfWeek_id = \"{$_idDay}\"ORDER BY mealOfDay_id ;");
		$j = 0;
		while($meal = $q->fetch_object()) {
			$meals[] = $meal;
		}
		return $meals;		
	}

/*********************
 * MODULES FUNCTIONS *
 *********************/
	
	/**
	 * Function getModule
	 * Return the module if he's already loaded.
	 * @param <string> $moduleName
	 */
	public function getModule($moduleName)
	{
		// On verifie si le module est deja charge
		if(array_key_exists($moduleName, $this->modules))
			return $this->modules[$moduleName];
		else
			return false;
	}
	
	public function loadModule($moduleName, $construct = null)
	{
		// Si le module n'est pas charge, on le cherche dans les differents dossiers
		if(!$this->getModule($moduleName)) {
			foreach($this->getModuleDirectories() as $part) {
				if(file_exists(FOLDER_BASE_SITE.'/includes/modules/'.$part.'/'.$moduleName . '.class.php')) {
					include_once(FOLDER_BASE_SITE.'/includes/modules/'.$part.'/'.$moduleName . '.class.php');
					$module = new $moduleName($construct);
					//$module->load();
					$this->modules[$moduleName] = $module;
					return $module;
				}
			}
		}
		
		return $this->getModule($moduleName);
	}

	/**
	 * Function getModuleDirectories
	 * List of directories of includes/modules
	 */
	private function getModuleDirectories()
	{
		return Array(
					'global',
					'home',
					'recipe',
					'test'
				);
	}
	
	public function loadModules()
	{
		$moduleNames = func_get_args();
		foreach($moduleNames as $moduleName) {
			if(!$this->getModule($moduleName)) {
				foreach($this->getModuleDirectories() as $part) {
					if(file_exists(FOLDER_BASE_SITE.'/includes/modules/'.$part.'/'.$moduleName . '.class.php')) {
						include_once(FOLDER_BASE_SITE.'/includes/modules/'.$part.'/'.$moduleName . '.class.php');
						$module = new $moduleName();
						$this->modules[$moduleName] = $module;
					}
				}
			}
		}
		if(!empty($this->modules))
			return $this->modules;
		else
			return false;
	}
}

?>