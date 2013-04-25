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
						'category'			=> 1, //TODO $this->getCategory(),
						'ingredients'		=> $this->getIngredients()					
					);
	}
	
	/**
	 * Function getIngredients()
	 * Get a list of all ingredients and associated quantity of the recipe
	 */
	public function getIngredients()
	{
		$ingredientsList = Array();
		$q_Ingredients = DB::getInstance()->query("SELECT id_ingredient, quantity FROM ass_recipe_ingredient WHERE id_recipe={$this->id} ;");

		while($qId = $q_Ingredients->fetch_object()) {
			$ing = new Ingredient($qId->id_ingredient);
			$ingredientsList[] = array('id' => $ing->id, 'name' => $ing->name, 'quantity' => $qId->quantity, 'serving_unit' => $ing->serving_unit);
		}
			
		return (($ingredientsList != null) ? $ingredientsList : null);
	}
	
	//TODO check
    public function getCategory()
    {
    	return sql_query("select name from recipe-ingredient_category where id= {$this->id} ;");
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