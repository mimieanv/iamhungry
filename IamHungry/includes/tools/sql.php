<?php

/*
 *	Definitons of sql functions here !
 *	Author : Huitre
 *	Version : 0.3
 */

// TODO : replace all error checks by exception !
/**
 * @method fetchQuery
 * @param 
 */
function fetchQuery($query, $bool = false)
{
	return (sql_query($query, $bool));
}

/**
 * fetchQueryId : return an array from a query with a specified key
 * @param $query : resource, $id: string, $bool : boolean
 */
function fetchQueryId($query, $id = 'id_user', $bool = false)
{
	return (sql_query_id($query, $id, $bool));
}

/**
 *
 * @param <string> $query
 * @param <string> $id
 * @param <boolean> $bool
 * @return <type>
 */
function sql_query_id_a($query, $id = 'id_user', $bool = false)
{
	$array = array();
	$data = array();
	if ($bool)
		$query = @DB::getInstance()->query($query);
	else
		$query = DB::getInstance()->query($query) or die(DB::getInstance()->error);
	while ($data = $query->fetch_assoc())
		$array[$data[$id]][] = $data;
    if (!empty($array))
        return ($array);
    return (-1);
}

/**
 *
 * @param <string> $query
 * @param <string> $id
 * @param <boolean> $bool
 * @return <type>
 */
function sql_query_id($query, $id = 'id_user', $bool = false)
{
	$array = array();
	$data = array();
	if ($bool)
		$query = @DB::getInstance()->query($query);
	else
		$query = DB::getInstance()->query($query) or die(DB::getInstance()->error);
	while ($data = $query->fetch_assoc())
		$array[$data[$id]] = $data;
    if (!empty($array))
        return ($array);
    return (-1);	
}

/**
 *
 * @param <type> $query
 * @param <type> $id
 * @param <type> $data
 * @return <type> 
 */
function sql_query_key($query, $id = 'id_user', $value = 'id', $bool = false)
{
	$array = array();
	$data = array();
	if ($bool)
		$query = @DB::getInstance()->query($query);
	else
		$query = DB::getInstance()->query($query) or die(DB::getInstance()->error);
	while ($data = $query->fetch_assoc())
		$array[$data[$id]] = $data[$value];
    if (!empty($array))
        return ($array);
    return (-1);
}

/**
 * @method sql_query_a
 * @param <string> $query
 * @param <string> $id
 * @param <boolean> $bool
 * @return <array> 
 */
function sql_query_a($query, $id = 'id', $bool = false)
{
	$array = array();
	if ($bool)
		$query = @DB::getInstance()->query($query);
	else
		$query = DB::getInstance()->query($query);
	if ($query)
	while ($data = $query->fetch_assoc())
		$array[] = $data[$id];
    if (!empty($array))
        return ($array);
    return (-1);
}

/**
 * @method sql_query
 * @param <string> $query
 * @param <type> $bool
 * @return <array> If only 1 entry, return array[0], <int> 0 if no entries found
 */
function sql_query($query)
{
	$array = array();
	$query = DB::getInstance()->query($query);
	if ($query)
	{
		while ($data = $query->fetch_assoc())
			$array[] = $data;
    	return ($array);
	}
	return (-1);
}

/**
 * @method sql_select
 * @param <string> $tables
 * @param <string> $fields
 * @param <string> or <array> $wheres
 * @param <bool> $bool true : stop execution on error
 * @return <array> If only 1 entry, return array[0]
 */
function sql_select($tables, $fields = NULL, $wheres = NULL, $bool = false)
{
	$query = "SELECT ";
	if ($fields)
		$query .= $fields;
	else
		$query .= "*";
	$query .=  " FROM ".$tables;
	$query .= sql_set_where($wheres).';';
	gdebug($query, 'sql_select');
	return (sql_query($query, $bool));
}

function sql_x_delete($tables, $wheres)
{
	foreach($tables as $t)
	{
		if (!sql_delete($t, $where))
			throw new myException(DB::getInstance()->error);
	}
}

function sql_delete($table, $wheres)
{
	$query = "DELETE FROM $table ";
	$query .= sql_set_where($wheres).';';
	$query = DB::getInstance()->query($query) or die (DB::getInstance()->error);
	return($query);
}

function sql_x_update($tables, $datas, $wheres)
{
	$ret = 0;
	$i = 0;
	foreach ($tables as $k => $table)
	{
		$ret = sql_update($table, $datas[$i], $wheres[$i]);
		$i++;
	}
	return ($ret);
}

/**
 * @method sql_update
 * @param Array or String $tables Nom de la table
 * @param Array or String $datas Tableau de donnees
 * @param Array or String $wheres Condition where rajoute a la fin de la query
 * @return ressources sql
 */
function sql_update($tables, $datas, $wheres = '')
{
	$query = "UPDATE $tables ";
	if (is_array($datas))
	{
		$qData = "";
		$i = 0;
		foreach ($datas as $kdata => $data)
		{
			if ($kdata == 'id')
				continue;
			if ($i)
				$qData .= ", $kdata = '$data' ";
			else
				$qData .= "SET $kdata = '$data'";
			$i++;
		}
	}
	else
		$qData = "SET ".$datas;
	$query .= $qData;
	$query .= sql_set_where($wheres).';';
	gdebug($query, 'sql_update');
	$query = DB::getInstance()->query($query);
	return ($query);
}

function sql_set_where($wheres = '')
{
	if (empty($wheres))
		return "";
	$qWhere = " WHERE ";
	if (is_array($wheres))
		foreach ($wheres as $where)
			$qWhere .= $where.' ';
	else
		return($qWhere.$wheres);
	return ($qWhere);
}

function sql_x_insert($tables, $datas, $last_id = 0)
{
	foreach ($tables as $tk => $tv)
		return (sql_insert($tv, $datas, $last_id));
}

/**
 *
 * @param <string> $tables
 * @param <array> or <string> $datas
 * @param <type> $last_id
 * @return <int> last inserted id
 */
function sql_insert($tables, $datas, $last_id = 0)
{
	$query = "INSERT INTO `$tables` ";
  $i = 0;
  if (is_array($datas))
	{
    $query .= " (`id`,";
		foreach ($datas as $k => $v)
		{
			if ($i)
				$query .= ",`{$k}`";
			else
				$query .= "`{$k}`";
			$i++;
		}
    $query .= ") ";
	}

  $query .= "VALUES ('',";
	$i = 0;
	if (is_array($datas))
	{
		foreach ($datas as $k => $v)
		{
			if ($i)
				$query .= " , '".$v."'";
			else
				$query .= "'".$v."'";
			$i++;
		}
	}
	else
		$query .= $datas;
	$query .=");";
	gdebug($query, 'sql_insert');
	DB::getInstance()->query ($query) or die (DB::getInstance()->error);
	return (DB::getInstance()->insert_id);
}

function sql_result($query)
{
    $res = DB::getInstance()->query($query);
    if(!($res && ($res->num_rows > 0)))
        return -1;
	$r = $res->fetch_array(MYSQLI_NUM);
    $result = $r[0];
    return $result;
}

function sql_num($query)
{
    $res = DB::getInstance()->query($query);
    return $res->num_rows;
}

function sql2csv($query)
{
    // Execute the base query
    $res = DB::getInstance()->query($query) or die("Unable to execute requested query for csv output");
    
    // Initialization
    $headers = Array(); // header's fields
    $nf = $res->field_count; // number of fields
    $i = 0;
    
    // Fill header's info
    while ($i < $nf) {
        $meta = $res->fetch_field_direct($i);
        array_push($headers, $meta->name);
        $i++;
    }
    $csv = implode(";", $headers)."\r\n";
    
    // Adding the request's content
    while($line = $res->fetch_array(MYSQLI_NUM))
    {
        for($i = 0; $i < $nf;$i++)
        {
            // Cleaning datas
            $line[$i] = html_entity_decode($line[$i]); // Remove htmlencoded datas
            $line[$i] = str_replace(";", "_", $line[$i]); // Remove comas avoinding csv corruption
            if(strpos($line[$i], "\n") !== false) // Check for linebreaks and add doublequotes if needed
                $line[$i] = "\"$line[$i]\"";      
        }
        $csv .= implode(";", $line)."\r\n";
    }
    echo $csv;
}

?>