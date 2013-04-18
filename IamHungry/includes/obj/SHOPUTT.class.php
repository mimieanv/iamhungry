<?php

class SHOPUTT
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
			self::$_instance = new SHOPUTT();
		return self::$_instance;
	}
	
	public function isAdmin($user = 0)
	{
		$user = ($user == 0) ? $this->user : $user;
		if($user->id == 1)
			return true;
		return false;
	}
	
	public function isInvite()
	{
		return ($this->user == null) ? true : false;
	}
	
/**********************
 * CUSTOMERS FUNCTIONS
 **********************/
	
	/**
	 * Function addUser()
	 * Create a new customer in the database
	 * return id of the new customer
	 */
	public function createUser($email, $password, $first_name, $last_name, $sex, $address, $zip_code, $city, $country, $phone) {		
		DB::getInstance()->real_query("INSERT INTO user (email, password, first_name, last_name, sex, address, zip_code, city, country, phone)
											VALUES(
												'{$email}',
												'{$password}',
												'{$first_name}',
												'{$last_name}',
												'{$sex}',
												'{$address}',
												'{$zip_code}',
												'{$city}',
												'{$country}',
												'{$phone}'
											); ");
		
		$id_user = DB::getInstance()->insert_id;

		DB::getInstance()->real_query("INSERT INTO user_product_list (id_user, name, description, state)
											VALUES(
												'{$id_user}',
												'Mon panier',
												'Mon panier Shop\'utt',
												'1'
											); ");
		
		//TODO envoyer mail validation compte
		
		return $id_user;	// l'id du dernier insert dans la db
	}
	
	
	
/*********************
 * SEARCH FUNCTIONS *
 *********************/
	
	/**
	 * Function searchProducts
	 * search a product in the DB
	 * @param <string> $search
	 * @param <int> $max
	 */
	public function searchProducts($search, $max = '')
	{
		$products = Array();
		$maxReq = ($max) ? "LIMIT 0, {$max}" : '';
		$q_Products = DB::getInstance()->query("SELECT id FROM product WHERE online=1 AND (name LIKE '%{$search}%' OR description LIKE '%{$search}%') ORDER BY name ASC {$maxReq} ;");
		while($qId = $q_Products->fetch_object())
			$products[] = new Product($qId->id);
		return (isset($products)) ? $products : null;	
	}
	
	/**
	 * Function getUsersList
	 * return an array with all the users in the DB
	 */
	public function getUsersList()
	{
		$q = DB::getInstance()->query("SELECT id FROM user ORDER BY first_name ASC ;");
		while($user = $q->fetch_object())
			$usersList[] = new User($user->id);
			
		return $usersList;		
	}

	
/***********************************************************************
 * LISTS FUNCTIONS (USERS LISTS, PRODUCTS LISTS, PRODUCT TYPES LISTS...)
 ***********************************************************************/
	
	/*
	 * PRODUCTS
	 */
	/**
	 * Function getProductsAvailables
	 * Get lists which contains products
	 * @param <int> $max
	 */
	public function getProductsAvailables($max = 0)
	{
		$productsAvailables = Array();
		$maxReq = ($max) ? "LIMIT 0, {$max}" : '';
		$q_Products = DB::getInstance()->query("SELECT * FROM product WHERE online=1 ORDER BY id DESC {$maxReq} ;");
		while($qId = $q_Products->fetch_object())
			$productsAvailables[] = new Product($qId->id);
		return $productsAvailables;		
	}
	
	/*
	 * PRODUCTS TYPES
	 */
	/**
	 * Function getAllProductsTypes
	 * Get all products types (order like a tree)
	 * @param <int> $from id of the parent
	 */
	//TODO Marche enfin ! :) -_-
	public function getProductTypes($from = null)
	{
		$productTypesList = Array();
		$fromReq = ($from) ? $from : '0';
		$q = DB::getInstance()->query("SELECT * FROM product_type WHERE id_type_from = '{$fromReq}' ORDER BY name ASC ;");
		$i = 0;
		while($productType = $q->fetch_object()) {
			$i++;
			$type = new ProductType($productType->id);
			if(!($type->hasSon())) {
				$productTypesList[] = Array(
					'id_type'		=> $type->id,
					'name_type'		=> $type->name,
				);
			} else {
				$productTypesList[] = Array(
					'id_type'		=> $type->id,
					'name_type'		=> $type->name,
					'son'			=> $this->getProductTypes($type->id),
				);
			}
		}
		return $productTypesList;
	}
	
	/**
	 * Function getMainProductTypes
	 * Enter description here ...
	 */
	public function getMainProductTypes()
	{
		$q = DB::getInstance()->query("SELECT * FROM product_type WHERE id_type_from = 0 ORDER BY name ASC ;");
		$i = 0;
		while($productType = $q->fetch_object())
			$productTypesList[] = new ProductType($productType->id);
			
		return $productTypesList;		
	}
	
    /**
     * Function getLists
     * Get lists (cart, favorites lists and order)
     * @param <int> $max
     * @param <int> state: 0->list, 1->cart, 2->order 
     */
	public function getProductLists($state = 1, $max = 0)
	{
		$lists = Array();
		$maxReq = ($max) ? 'LIMIT 0, {$max}' : '';
		$q = DB::getInstance()->query("SELECT id_product_list FROM user_product_list WHERE state={$state} ORDER BY date_creation DESC ;");
		$i = 0;
		while($list = $q->fetch_object()) {
			$l    		= new ProductList($list->id_product_list);
			$lists[]	= $l;
		}
		return $lists;
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
					'account',
					'home',
					'product',
					'product_type',
					'all_products',
					'product_list'
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