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

	/**
	 * Function getProductsInCartCount
	 * @return number of products put in their shopping cart
	 */
	public function getIngredientsInHandCount()
	{
		return sql_result("SELECT COUNT(id_list) AS count_products FROM `product_list` WHERE id_list = (SELECT id_product_list FROM  `user_product_list` WHERE id_user='{$this->id}' AND state=1");
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