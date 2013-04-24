<?php

/**
 * Class Ingredient
 * Writed to deal with IamHungry's meal plan System
 * @author Jeremy Choron
 */
class Recipe extends sqlRow
{
	// Instanciation and construction of the object with data from the database
	public function __construct($id)
	{
		parent::__construct('ingredient', $id);
	}
	
	public function getFullContentInArray()
	{
		return Array(
						'id'				=> $this->id,
						'name'				=> $this->name,
						'description'		=> $this->description,
						'serving_unit'		=> $this->serving_unit,
						'category'			=> $this->getCategory()					
					);
	}
	
    /**
     * Function getCategory
     * Return the category of the ingredient
     */
    public function getCategory()
    {
        return new Category($this->id_category);
    }
	
	
	/**
	 * Function getPictures
	 * Get pictures about the product
	 */
	public function getPictures($max = 0)
	{
		$picturesList = Array();
		$maxReq = ($max) ? "LIMIT 0, {$max}" : '';
		$q_Pictures = DB::getInstance()->query("SELECT id_picture FROM picture WHERE id_entity={$this->id} and type='ingredient' {$maxReq};");
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