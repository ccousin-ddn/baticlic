<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class mov_movement_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "mov_movement";
			$this->key			= "idmovement";
			$this->duplicateKey = false;
			
			$this->fields 		= "mov_movement.*, idjob, usr_firstname, sho_name, art_code, art_description";
			
			$this->joins 		= "
				LEFT JOIN usr_user USING(iduser) 
				LEFT JOIN sho_shop USING(idshop) 
				LEFT JOIN veh_vehicle ON idshop = idvehicle 
				LEFT JOIN art_article USING(idarticle) 
				LEFT JOIN lnk_job_article USING (idmovement) 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("idmovement DESC");
			$this->fieldName	= "";
			
			/*** Table filtering ***/
			$this->filters['sho_type'] = array("id"=>"sho_type","in"=>array(2),"label"=>"Magasin");
			$this->filters['mov_year'] = array("id"=>"idyear","in"=>array(date("Y")),"label"=>"Année");
			$this->filters['mov_month'] = array("id"=>"idmonth","in"=>array(date("m")),"label"=>"Mois");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idmovement"=>0,
				"iduser"=>$_SESSION['iduser'],
				"idshop"=>$this->dataSent['idshop']??0,
				"idarticle"=>0,
				"mov_quantity"=>1,
				"mov_date"=>date("Y-m-d"),
				"mov_type"=>$this->dataSent['mov_type']??1,
				"mov_place"=>5,
				"mov_idplace"=>0,
				
				);
			if($insert){
				return $this->insert($this->values);
			}else{
				return true;
			}
		}
		
		public function m_getAll($withFilter=true)
		{
			if($withFilter){
				$filters = $this->dataSent;
				if(!empty($filters)){
					//var_dump($filters);
					foreach($filters as $key=>$val){
						if($key == "idyear"){
							if(is_array($val)){
								$this->conds[] = "YEAR(mov_date) IN (".implode(",",$val).")";
							}else{
								$this->conds[] = "YEAR(mov_date) = ".$val;
							}
							//$this->dataSent["idyear"][0] = $val;
						}elseif($key == "idmonth"){
							if(is_array($val)){
								$this->conds[] = "MONTH(mov_date) IN (".implode(",",$val).")";
							}else{
								$this->conds[] = "MONTH(mov_date) = ".$val;
							}
						}else{
							$this->conds[] = $key." IN(".implode(",",$val).")";
						}
					}
				}else{
					if(!empty($this->filters)){
						$this->conds[] = "YEAR(mov_date) = ".date("Y");
						$this->conds[] = "MONTH(mov_date) = ".date("m");
					}
				}
			}else{
				foreach($this->filters as $filter){
					$filter['in'] = array();
				}
			}
			//$this->conds = array();
			//$this->orders = array();
			//$this->debugging = true;

			return $this->select();
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("idmovement = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			/*** 
			idshop = idSource | mov_place = destinationType | mov_idplace = idDestination 
			mov_place : 1=>shop, 3=>job, 4=>vehicle, 14=>epi
			***/
			parse_str($dataSent, $data);
			//$this->debugging = true;
			//var_dump($data);exit;
			
			// new movement
			$this->insert($data);
			$newIdmovement = $this->result['idparent'];
			
			if($data['mov_type'] == 1){ // In
				$_query_insert = "
				INSERT INTO sto_stock (idshop, idarticle, sto_quantity)
				SELECT idshop, idarticle, mov_quantity
				FROM mov_movement
				WHERE idmovement = $newIdmovement
				ON DUPLICATE KEY UPDATE
				sto_quantity = sto_quantity + mov_quantity";
				//echo $_query_insert;exit;
				$mysql = new mysql();
				$mysql->query($_query_insert);
			}
			if($data['mov_type'] == 2){ // Out
				$_query_insert = "
				INSERT INTO sto_stock (idshop, idarticle, sto_quantity)
				SELECT idshop, idarticle, mov_quantity
				FROM mov_movement
				WHERE idmovement = $newIdmovement
				ON DUPLICATE KEY UPDATE
				sto_quantity = IF(sto_quantity < 1, 0, sto_quantity - mov_quantity)";
				//echo $_query_insert;exit;
				$mysql = new mysql();
				$mysql->query($_query_insert);
			}
			
			// add movement if in/out shop
			if($data['mov_place'] == 1){
				if($data['mov_type'] == 1){
					$mov_type = 2;
				}else{
					$mov_type = 1;
				}
				$this->m_insertReverse($newIdmovement, $mov_type);
			}
			
			// if mvt into job
			if($data['mov_place'] == 3){
				// Out from vehicle
				/*
				$_query_insert = "
				UPDATE lnk_vehicle_article 
				SET mvt_date_out = '".$this->clean($data['mov_date'])."', art_qty = IF(art_qty < 1, 0, art_qty - ".$data['mov_quantity'].")
				WHERE idvehicle = ".$data['idshop']." AND idarticle = ".$data['idarticle']." AND mvt_date_in = '".$this->clean($data['mov_date'])."'
				";
				//echo $_query_insert;exit;
				$mysql = new mysql();
				$mysql->query($_query_insert);
				*/
				
				// into job_article
				$_query_insert = "
				INSERT INTO lnk_job_article (idjob, idarticle, idmovement, lnk_qty, lnk_date_in)
				VALUES (".$data['mov_idplace'].", ".$data['idarticle'].", ".$newIdmovement.", ".$data['mov_quantity'].", '".$this->clean($data['mov_date'])."')
				";
				//echo $_query_insert;exit;
				$mysql = new mysql();
				$mysql->query($_query_insert);
			}
			
			// if mvt vehicle (from/to shop)
			if($data['mov_place'] == 4){
				if($data['mov_type'] == 2){ // Out from shop to vehicle
					$_query_insert = "
					INSERT IGNORE INTO lnk_vehicle_article (idvehicle, idarticle, art_qty, mvt_date_in) 
					VALUES (".$data['mov_idplace'].", ".$data['idarticle'].", ".$data['mov_quantity'].", '".$this->clean($data['mov_date'])."')
					ON DUPLICATE KEY UPDATE art_qty = IF(art_qty < 1, 0, art_qty + ".$data['mov_quantity'].")
					";
				}else{ // Out from vehicle
					$_query_insert = "
					UPDATE lnk_vehicle_article 
					SET mvt_date_out = '".$this->clean($data['mov_date'])."', art_qty = IF(art_qty < 1, 0, art_qty - ".$data['mov_quantity'].")
					WHERE idvehicle = ".$data['mov_idplace']." AND idarticle = ".$data['idarticle']."
					";
				}
				//echo $_query_insert;exit;
				$mysql = new mysql();
				$mysql->query($_query_insert);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("idmovement = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			return $this->update($data);
		}
		
		public function m_insertReverse($idmovement, $mov_type)
		{
			$_query_insert = "
			INSERT IGNORE INTO mov_movement (iduser, idshop, idarticle, mov_quantity, mov_date, mov_type, mov_place, mov_idplace)
			SELECT iduser, mov_idplace, idarticle, mov_quantity, mov_date, $mov_type, 1, idshop
			FROM mov_movement
			WHERE idmovement = $idmovement";
			//echo $_query_insert;exit;
			$mysql = new mysql();
			$mysql->query($_query_insert);
			
			//update stock
			$qty = ($mov_type == 1 ? "sto_quantity + mov_quantity" : "IF(sto_quantity < 1, 0, sto_quantity - mov_quantity)");
			$_query_update = "
			INSERT INTO sto_stock (idshop, idarticle, sto_quantity)
			SELECT mov_idplace, idarticle, mov_quantity
			FROM mov_movement
			WHERE idmovement = $idmovement
			ON DUPLICATE KEY UPDATE
			sto_quantity = ".$qty;
			//echo $_query_insert;exit;
			$mysql = new mysql();
			$mysql->query($_query_update);
			
			return true;
		}
		
		public function m_delete($idrecord)
		{
			//$this->debugging = true;
			
			return $this->delete($idrecord);
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : $this->fields = "idmovement AS id, mov_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}