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
						'description'		=> $this->description,
						'instructions'		=> $this->instructions,
						'nb_servings'		=> $this->nb_servings,
						'preparation_time'	=> $this->preparation_time,
						'category'			=> $this->getCategory(),
						'ingredients'		=> $this->getIngredients()					
					);
	}
	
	/**
	 * Function getIngredients()
	 * Get a list of all ingredients and associated quantity of the recipe
	 */
	public function getIngredients()
	{
		$q_Ingredients = DB::getInstance()->query("SELECT id_ingredient, quantity FROM as_recipe_ingredient WHERE id_entity={$this->id} and type='recipe' {$maxReq} ;");
		while($qId = $q_Ingredients->fetch_object())
			$ingredientsList[] = array(new Ingredient($qId->id_ingredient), $qId->quantity);
			
		return $ingredientsList;
	}
	
    public function getCategory()
    {
    	return sql_query("select name from recipe-ingredient_category where id= {$this->id} ;");
    }
	
	
	/**
	 * Function getPictures
	 * Get pictures about the product
	 */
	public function getPictures($max = 0)
	{
		$picturesList = Array();
		$maxReq = ($max) ? "LIMIT 0, {$max}" : '';
		$q_Pictures = DB::getInstance()->query("SELECT id_picture FROM picture WHERE id_entity={$this->id} and type='recipe' {$maxReq};");
		while($qId = $q_Pictures->fetch_object())
			$picturesList[] = new Picture($qId->id_picture);
        
		if(empty($picturesList))
			$picturesList[] = new Picture('no_picture.jpg');
		
		return $picturesList;
	}
	
	/**
	 * Function getUrl
	 * Return the url address of the recipe page
	 */
	public function getUrl()
	{
		return URL_BASE_SITE.'/index.php?page=recipe&id_recipe='.$this->id;
	}
	
}


?>