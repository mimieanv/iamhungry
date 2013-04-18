<?php

/**
 * Class User
 * Writed to deal with Shoputt's User System
 * @version 2012, 05 june
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
        	$this->error = "Membre introuvable ({$user})";
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
    
    /*
     * Activate the current User
     */
    //TODO
	public function activate()
	{
		
	}
    
    /**
     * Function isActive
     * Return the status of activation of the current member
     * @return boolean
     */
    public function isActive()
    {
		return ($this->valid == 1) ? true : false;
    }
    
    /**
     * Function isAdmin
     * Return if the selected user is an administrator or not (admin is the first user in the DB)
     * @return boolean
     */
    public function isAdmin()
    {
		return ($this->id == 1);
    }
    
    /**
     * Function checkPassword
     * Check if the password given by the user matches with the password in the database
     * @return boolean
     */
	public function checkPassword($passwd)
	{
		$hPasswd = hash('sha512', ($this->first_name . 'la meule!' . $passwd));
        if($this->password == $hPasswd)
            return true;
        else
            return false;
	}
	
	/**
	 * Function addWebsiteLogin
	 * Add an entry in website_logins at each connection of the user
	 */
	//TODO check
	public function addWebsiteLogin()
	{
		DB::getInstance()->real_query("INSERT INTO `website_logins` ( `ip` , `id_user` , `when` ) VALUES (
													'{$_SERVER["REMOTE_ADDR"]}',
													'{$this->id}', 
													CURRENT_TIMESTAMP
													);
		");
	}
	
	/**
	 * Function createNewCart
	 * Enter description here ...
	 */
    public function createNewCart()
    {
    	DB::getInstance()->real_query("INSERT INTO user_product_list (id_user, name, description, state)
									VALUES(
										'{$this->id}',
										'Mon panier',
										'Mon panier Shop\'utt',
										'1'
									); ");
    }
	
	/**
	 * Function getCart
	 * get the main list of the user
	 */
	public function getCart() {
		$id = sql_result("SELECT id_product_list FROM user_product_list WHERE id_user = '{$this->id}' AND state=1 ;");
		return ($id != -1) ? new ProductList($id) : null;
	}
 
    /**
     * Function getLists
     * Get lists tagged as favorite or createg by the user
     * @param <int> $max
     * @param <int> state: 0->list, 1->cart, 2->order 
     */
	public function getLists($state = 1, $max = 0)
	{
		$lists = Array();
		$maxReq = ($max) ? 'LIMIT 0, {$max}' : '';
		$q = DB::getInstance()->query("SELECT id_product_list FROM user_product_list WHERE id_user={$this->id} AND state={$state} ORDER BY date_creation DESC ;");
		$i = 0;
		while($list = $q->fetch_object()) {
			$l    		= new ProductList($list->id_product_list);
			$lists[]	= $l;
		}
		return $lists;
	} 
	
	/**
	 * Function getListsCount
	 * Count the number of lists of the user
	 */
	public function getListsCount()
	{
		return sql_result("SELECT COUNT(id) FROM user_product_list WHERE id_user = '{$this->id}' ;");
	}
	
	/**
	 * Function getCommentsCount
	 * Count the number of comments of the user
	 */
	public function getCommentsCount()
	{
		return sql_result("SELECT COUNT(id) FROM product_comment WHERE id_from = '{$this->id}' ;");
	}

	/**
	 * Function getNotesCount
	 * @return number of notes given by the user
	 */
	public function getNotesCount()
	{
		return sql_result("SELECT COUNT(id_product) FROM product_note WHERE id_user='{$this->id}' ;");
	}
	
	/**
	 * Function getLoginsCount
	 * @return number of connections by the user
	 */
	public function getLoginsCount()
	{
		return sql_result("SELECT COUNT(id) FROM website_logins WHERE id_user='{$this->id}' ;");
	}
	
	/**
	 * Function getFavoritesCount
	 * @return number of products marked as favorites by the user
	 */
	public function getFavoritesProductsCount()
	{
		return sql_result("SELECT COUNT(id_tripbook) FROM user_favorites WHERE id_user='{$this->id}' ORDER BY `when` DESC;");
	}
	
	/**
	 * Function getProductsInCartCount
	 * @return number of products put in their shopping cart
	 */
	public function getProductsInCartCount()
	{
		return sql_result("SELECT COUNT(id_list) AS count_products FROM `product_list` WHERE id_list = (SELECT id_product_list FROM  `user_product_list` WHERE id_user='{$this->id}' AND state=1");
	}	
	
	/**
	 * Function getNote
	 * //check if the user has already rated a product
	 * @param <int> $id_product
	 */
	public function getNote($id_product)
	{
		$note = sql_result("SELECT note FROM product_note WHERE id_product='{$id_product}' AND id_user='{$this->id}' ;");
		return ($note != -1) ? $note : null;
	}
	
	public function getConnections()
	{
		$connections = Array();
		$q = DB::getInstance()->query("SELECT * FROM website_logins WHERE id_user={$this->id} ;");
		while($log = $q->fetch_object()) {
			$connections[]['ip']	= $log->ip;
			$connections[]['when']	= $log->when; 
		}
		return $connections;
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