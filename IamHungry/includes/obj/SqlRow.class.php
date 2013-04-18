<?php

/**
 * Class SqlRow
 * @author Johan Massin
 * @copyright SoWeTrip, 2011
 */

class sqlRow
{
	protected	$table;
	protected	$id;
	protected	$id_name;
	protected	$data	= Array();
	public 		$error	= '';

	public function __construct($table, $id, $idname = 'id')
	{
		$this->table	= $table;
		$this->id		= $id;
		$this->id_name	= $idname;
		if(!$id)
			$this->error = 'id incorrect';
		$query = DB::getInstance()->query("SELECT * FROM {$this->table} WHERE {$this->id_name}='{$this->id}';");
		if(!($this->data = $query->fetch_assoc()))
			$this->error = 'id incorrect';
		$query->free();
	}

	public function __toString()
	{
		return $this->data;
	}

	public function __set($key, $value)
	{
		if($this->id == null)
			return null;
		if(!array_key_exists($key, $this->data))
			return false;
		else {
			$this->data[$key] = $value;
			$this->commit();
		}
		return true;
	}

	public function __get($key)
	{
		if($this->id != null)
			return (isset($this->data[$key])) ? $this->data[$key] : false;
	}

	public function __isset($key)
	{
		return array_key_exists($key, $this->data);
	}

	public function __unset($key)
	{
		unset($this->data[$key]);
	}

	public function update($row)
	{
		$this->data = $row;
		$this->commit();
	}

	public function delete()
	{
		DB::getInstance()->real_query("DELETE FROM $this->table WHERE {$this->id_name}='{$this->id}';");
	}

}

?>