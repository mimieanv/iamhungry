<?php

/**
 * Class Category
 * Writed to deal with IamHungry's meal plan System
 * @author Jeremy Choron
 */
class Category extends sqlRow
{
	
	// Instanciation and construction of the object with data from the database
	public function __construct($id)
	{
		parent::__construct('recipe-ingredient_category', $id);
	}
	
	public function getFullContentInArray()
	{
		return Array(
						'id'					=> $this->id,
						'name'					=> $this->name,
						'description'			=> $this->description,
						'id_parent_category'	=> $this->id_parent_category,
						'type'					=> $this->type					
					);
	}
	
}


?>