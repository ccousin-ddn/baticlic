<?php
	include_once ("libraries/lib_include.php");
	
	$table = "cli_contact";
	$id = "idclicontact";
	$cryptfields = array("con_mobile","con_phone","con_phone_perso","con_email");
	
	$table_object = new $table();
	$table_object->cryptfields = array();
	$table_object->fields = $id.", ".implode(", ", $cryptfields);
	$table_object->joins = "";
	$table_object->orders = array();
	//$table_object->debugging = true;
	$table_object->m_getAll();
	
	$newdata = array();
	
	foreach($table_object->values as $row){
		foreach($cryptfields as $column){
			$newdata[$column] = strongEncrypt($row[$column]);
		}
		$table_object->conds = array($id." = ".$row[$id]);
		//$table_object->debugging = true;
		$table_object->update($newdata);
		echo $row[$id]." updated<br>";
	}
	echo "The end";