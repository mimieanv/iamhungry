<?php

/**
 * @version 2013 April, 24
 */

class MealPlanning implements IModule
{
	private $user;
	
    function __construct()
    {
    	$this->user = IAMHUNGRY::getInstance()->user;
    }
    
    function preProcess($construct)
    {
	}

    function display()
    {
    	$planning = IAMHUNGRY::getInstance()->user->getWeekPlanning();
    	
		echo "What am I gonna eat?<br /><br />";
		
//		var_dump($planning);
		
		foreach($planning as $meal) {
			if(is_array($meal['recipes'])) {
				echo strtoupper($meal['day']) .': '. $meal['meal'] .' -> ';
				foreach($meal['recipes'] as $rec)
					echo "<a href=\"index.php?page=recipe&id_recipe={$rec->id}\" >{$rec->name}</a>   ";
//				var_dump($rec);
				echo '<br />';
			} else
				echo strtoupper($meal['day']) .': '. $meal['meal'] .' -> '. $meal['recipes'] .'<br />';
		}
    }

}

?>