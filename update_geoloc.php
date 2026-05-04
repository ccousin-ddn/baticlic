<?php
	include_once ("libraries/lib_include.php");
	
	$table = "sit_site";
	$id = "idsite";
	$lat = "sit_lat";
	$lng = "sit_lng";
	$address = array("sit_address_1");
	$city =  array("sit_pc","sit_city");
	
	$table_object = new $table();
	$table_object->cryptfields = array();
	$table_object->fields = $id.", ".$lat.", ".$lng.", ".implode(", ", $address).", ".implode(", ", $city);
	$table_object->joins = "";
	$table_object->orders = array();
	$table_object->conds = array("idsite IN (17,18,19,20,21)");
	//$table_object->debugging = true;
	$table_object->m_getAll();
	
	foreach($table_object->values as $loc){
		if(empty($loc[$lat]) || empty($loc[$lng])){
			$geocode = new geocode();
			$tmp = "";
			foreach($address as $key=>$add){
				if($key > 0){$tmp .= " ";}
				$tmp .= $loc[$add];
			}
			$tmp .= ", ";
			foreach($city as $key=>$add){
				if($key > 0){$tmp .= " ";}
				$tmp .= $loc[$add];
			}
			$searchAddress = urlencode($tmp);
			$res_geocode = $geocode->getGeoloc($searchAddress);

			if($res_geocode['status'] == "ok"){
				$data[$lat] = $res_geocode['lat'];
				$data[$lng] = $res_geocode['lng'];
				$table_object->conds = array($id." = ".$loc[$id]);
				$table_object->update($data);
				echo $loc[$id]." mis à jour<br>";
			}else{
				echo $loc[$id]." ".$res_geocode['info'].$searchAddress."<br>";
			}
		}
	}