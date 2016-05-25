<?php
class MysqlExt extends Mysql
{
	function getCreateTableSql($_table_name)
	{
		$sql = "SHOW CREATE TABLE `$_table_name`";
		return $this->getFirstRowColumn($sql,"Create Table");
	}
	
	function moveTable($_table_name_from,$_table_name_to,$_table_name_backup="")
	{
		$arr_sql_rename = array();
		if($this->isTableExisting($_table_name_to))
		{
			if($_table_name_backup)
			{
				$this->dropTable($_table_name_backup);
				$arr_sql_rename[] = "`$_table_name_to` to `$_table_name_backup`";
			}
			else
			{
				$this->dropTable($_table_name_to);
			}
		}
		$arr_sql_rename[] = "`$_table_name_from` to `$_table_name_to`";
		$sql = "rename table " . implode(",",$arr_sql_rename);
		return $this->query($sql);
	}
	
	function swapTable($_table_name_1,$_table_name_2,$_table_name_swap="")
	{
		//the max length of mysql table name is 64
		if(!$_table_name_swap)
		{
			$_table_name_swap = "swap_" . $_table_name_1 . "_". $_table_name_2;
			if(strlen($_table_name_swap) > 50) $_table_name_swap = substr($_table_name_swap,0,50);
			$_table_name_swap .= "_" . time();
		}
		
		$this->dropTable($_table_name_swap);
		
		$arr_sql_rename = array();
		$arr_sql_rename[] = "`$_table_name_1` to `$_table_name_swap`";
		$arr_sql_rename[] = "`$_table_name_2` to `$_table_name_1`";
		$arr_sql_rename[] = "`$_table_name_swap` to `$_table_name_2`";
		$sql = "rename table " . implode(",",$arr_sql_rename);
		return $this->query($sql);
	}
	
	function isTableExisting($_table_name)
	{
		$sql = "SHOW TABLES LIKE '$_table_name'";
		if($this->getFirstRowColumn($sql)) return true;
		return false;
	}
	
	function showTables($_table_names)
	{
		$arr_return = array();
		$sql = "SHOW TABLES LIKE '$_table_names'";
		$arr = $this->getRows($sql);
		foreach($arr as $row)
		{
			$arr_return[] = current($row);
		}
		return $arr_return;
	}
	
	function dropTables($_table_names)
	{
		//support % in table name
		$tables = $this->showTables($_table_names);
		$this->dropTable($tables);
	}
	
	function getTableIndex($_table_name)
	{
		//Table,Non_unique,Key_name,Seq_in_index,Column_name,Index_type,Sub_part
		$arr_return = array();
		$sql = "SHOW INDEX FROM `$_table_name`";
		$arrRow = $this->getRows($sql);
		foreach($arrRow as $row)
		{
			$index_name = $row["Key_name"];//PRIMARY,index1,index2...
			$seq_in_index = $row["Seq_in_index"];//1,2,3,4,...
			$arr_return[$index_name]["details"][$seq_in_index - 1] = $row;
			$arr_return[$index_name]["Index_type"] = $row["Index_type"];//BTREE,FULLTEXT
			$arr_return[$index_name]["Non_unique"] = $row["Non_unique"];//0:unique index,1:normal index
			
			$column_with_sub_part = "`" . $row["Column_name"] . "`";
			if(is_numeric($row["Sub_part"])) $column_with_sub_part .= "(" . $row["Sub_part"] . ")";
			
			$arr_return[$index_name]["arr_column"][] = $row["Column_name"];
			$arr_return[$index_name]["arr_column_with_sub_part"][] = $column_with_sub_part;
		}
		
		foreach($arr_return as $index_name => $col_index)
		{
			$columns = implode(",",$col_index["arr_column_with_sub_part"]);
			if($index_name == "PRIMARY")
			{
				$arr_return[$index_name]["dropsql"] = "DROP PRIMARY KEY";
				$arr_return[$index_name]["addsql"] = "ADD PRIMARY KEY ($columns)";
			}
			elseif($col_index["Index_type"] == "FULLTEXT")
			{
				$arr_return[$index_name]["dropsql"] = "DROP KEY `$index_name`";
				$arr_return[$index_name]["addsql"] = "ADD FULLTEXT `$index_name` ($columns)";
			}
			elseif($col_index["Non_unique"] == 1)
			{
				$arr_return[$index_name]["dropsql"] = "DROP KEY `$index_name`";
				$arr_return[$index_name]["addsql"] = "ADD INDEX `$index_name` ($columns)";
			}
			else
			{
				$arr_return[$index_name]["dropsql"] = "DROP KEY `$index_name`";
				$arr_return[$index_name]["addsql"] = "ADD UNIQUE `$index_name` ($columns)";
			}
		}
		
		if(!empty($arr_return)) $this->lastTableIndexInfo[$_table_name] = $arr_return;
		return $arr_return;
	}
	
	function dropAllIndex($_table_name,$_index_info="")
	{
		return $this->dropIndex($_table_name,$_index_info);
	}
	
	function addAllIndex($_table_name,$_index_info="")
	{
		return $this->addIndex($_table_name,$_index_info);
	}
	
	function dropIndex($_table_name,$_index_info="",$_arr_index_name=array())
	{
		return $this->dropOrAddIndex("drop",$_table_name,$_index_info,$_arr_index_name);
	}

	function addIndex($_table_name,$_index_info="",$_arr_index_name=array())
	{
		return $this->dropOrAddIndex("add",$_table_name,$_index_info,$_arr_index_name);
	}
	
	function dropOrAddIndex($_act,$_table_name,$_index_info="",$_arr_index_name=array())
	{
		if($_index_info === "") $_index_info = $this->getTableIndex($_table_name);
		if(empty($_index_info)) return true;
		$arr_drop_sql = array();
		foreach($_index_info as $index_name => $index)
		{
			if(empty($_arr_index_name))
			{
				$arr_drop_sql[] = $index[$_act . "sql"];
			}
			else
			{
				if(in_array($index_name,$_arr_index_name))
				{
					$arr_drop_sql[] = $index[$_act . "sql"];
				}
			}
			
		}
		$sql = "ALTER TABLE `$_table_name` " . implode(",",$arr_drop_sql);
		$this->query($sql);
	}
	
	function duplicateTable($_table_name_1,$_table_name_2)
	{
		$sql = $this->getCreateTableSql($_table_name_1);
		$search_text = "CREATE TABLE `$_table_name_1`";
		$pos = strpos($sql, $search_text);
		if($pos === false || $pos > 0) die("something not right here:$sql");
		$sql = "CREATE TABLE `$_table_name_2`" . substr($sql,strlen($search_text));
		$this->query($sql);
	}
	
	function dropTable($_table_name,$_is_temp_table=false)
	{
		if(is_array($_table_name)) $_table_name = implode(",",$_table_name);
		$str_temp_table = $_is_temp_table ? "TEMPORARY" : "";
		$sql = "drop $str_temp_table table if exists $_table_name";
		$this->query($sql);
	}
	
}
?>