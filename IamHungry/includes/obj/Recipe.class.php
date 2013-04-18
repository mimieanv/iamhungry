<?php

/**
 * Class Recipe
 * Writed to deal with IamHungry's meal plan System
 * @author Jeremy Choron
 */
class Recipe extends sqlRow
{
	
	// Instanciation and construction of the object with data from the database
	public function __construct($id)
	{
		parent::__construct('recipe', $id);
	}
	
	public function getFullContentInArray()
	{
	return Array(
					'id'				=> $this->id,
					'name'				=> $this->name,
					'type'				=> $this->getType(),

					'description'		=> $this->description,
					'average_rating'	=> $this->getAverageRating(),
					'comments'			=> $this->getComments(),
					'price'				=> $this->getPrice(),
					'quantity'			=> $this->quantity,
					'online'			=> $this->online						
				);

	}
	
    /**
     * Function getType
     * Return the type of the product
     */
    public function getCategory()
    {
        //return new Category($this->???);
    }
	
	
	/**
	 * Function getPictures
	 * Get pictures about the product
	 */
	public function getPictures($max = 0)
	{
		$picturesList = Array();
		$maxReq = ($max) ? "LIMIT 0, {$max}" : '';
		$q_Pictures = DB::getInstance()->query("SELECT id_picture FROM product_picture WHERE id_product={$this->id} {$maxReq};");
		while($qId = $q_Pictures->fetch_object())
			$picturesList[] = new Picture($qId->id_picture);
        
		if(empty($picturesList))
			$picturesList[] = new Picture('no_picture.jpg');
		
		return $picturesList;
	}
	
	/**
	 * Function getUrl
	 * Return the url address of the product page
	 */
	public function getUrl()
	{
		return URL_BASE_SITE.'/index.php?page=product&id_product='.$this->id;
	}
	
	/**
	 * Function getPrice
	 * Return TTC price
	 */
	public function getPrice()
	{
		return round(($this->price_HT + $this->getType()->tax * $this->price_HT), 2);
	}

/*
 * RATING FUNCTIONS
 */
	
	/**
	 * Function addNote
	 * Enter description here ...
	 */
	public function addNote($user, $note)
	{
		if($user->getNote($this->id))
			@$this->editNote($user->id, $note);
		else {
			DB::getInstance()->real_query("INSERT INTO product_note ( id_product, id_user, note) VALUES ( {$this->id}, {$user->id}, {$note}) ;");
		}
	}
	
	/**
	 * Function editNote
	 * Enter description here ...
	 */
	public function editNote($id_user, $note)
	{
		sql_result("UPDATE product_note SET note='{$note}' WHERE id_product={$this->id} AND id_user={$id_user} ;");
	}
	
	/**
	 * Function getAverageRating
	 * return the average rating of the product
	 */
	public function getAverageRating()
	{
		$rating = sql_result("SELECT AVG(note) FROM product_note WHERE id_product={$this->id} ;");
		return ($rating) ? intval($rating) : 3;
	}
	
/*
 * COMMENTS FUNCTIONS
 */
	/**
	 * Function addComment
	 * add a comment on the product
	 * @param <int> $id_user
	 * @param <string> $comment
	 */
	public function addComment($id_user, $comment)
	{
		$comment = trim(DB::getInstance()->real_escape_string(htmlentities($comment, ENT_COMPAT, 'UTF-8')));
		
		if(!empty($comment)) {
			sql_result("INSERT INTO product_comment ( `id_product`, `id_from`, `comment` )
							VALUES (
								'{$this->id}',
								'{$id_user}',
								'{$comment}'
							)
			;");
			return true;
		} else
			return false;
	}
	
	/**
	 * Function getComments
	 * Get comments about the product
	 */
	public function getComments()
	{
		$comments = Array();
		$q = DB::getInstance()->query("SELECT * FROM product_comment WHERE id_product = '{$this->id}' ORDER BY `when` DESC ;");
		$i = 0;
		while($comment = $q->fetch_object()) {
			$i++;
			$u = new User($comment->id_from);
			$comments[] = Array (
							'id_comment'	=> intval($comment->id),
							'user'			=> $u,
							//'date'			=> date("d/m &\a\g\\r\av\e; H:m", $comment->when),
							'comment'		=> html_entity_decode($comment->comment, ENT_COMPAT, 'UTF-8')
						);
		}
		
		return $comments;
	}
	
	/**
	 * Function deleteComment
	 * delete a comment
	 * @param <int> $id_comment
	 */
	public function deleteComment($id_comment)
	{
		return sql_result("DELETE FROM product_comment WHERE id = '{$id_comment}' ;");
	}

	/**
	 * Function getPropertiesVal
	 * get extended properties values of the product
	 */
	public function getProperties()
	{
		$q_values = DB::getInstance()->query("SELECT * FROM product_type_property_val WHERE id_product={$this->id} ;");
		while($rValue = $q_values->fetch_object()) {
			$q_properties	= DB::getInstance()->query("SELECT * FROM product_type_property WHERE id='{$rValue->id_product_type_property}' ;");
			$rProperty		= $q_properties->fetch_object();
			$propertyVal[]	= Array (
								'property'		=> $rProperty->property,
								'property_val'	=> $rValue->value,
							);
		}
		
		return (isset($propertyVal)) ? $propertyVal : null;	
	}
	
}


?>