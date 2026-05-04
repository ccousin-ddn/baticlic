<?php
/**
*** Juillet 2022@SoluFile 
**/
	require_once 'model_crud.php';
	
	class veh_full_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "veh_full";
			$this->key			= "idfull";
			$this->duplicateKey = false;
			
			$this->fields 		= "veh_full.*, CONCAT_WS(' ',veh_model,veh_numberplate) AS veh_name, CONCAT(usr_firstname,' ',usr_lastname) AS usr_name, ROUND(ful_liter/(ful_mileage - ful_old_mileage)*100, 0) AS ful_consum";
			
			$this->joins 		= "
				LEFT JOIN veh_vehicle USING (idvehicle)
				LEFT JOIN usr_user USING (iduser)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("ful_date DESC");
			
			$this->unset		= array("idparent","idShop","idArticle","ful_consum");
			$this->unclean		= array();
			
			/*** new from ***/
			$this->fieldName	= "ful_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idfull"=>0,
				"idvehicle"=>0,
				"iduser"=>$_SESSION['iduser'],
				"ful_date"=>date("Y-m-d"),
				"ful_mileage"=>0,
				"ful_liter"=>0,
				"ful_amount"=>0,
				
				
			);
			$this->values = (object) $data;
			if($insert){
				return $this->insert($data);
			}else{
				return true;
			}
		}
		
		public function m_getAll($withFilter=true)
		{
			if($withFilter){
				$filters = $this->dataSent;
				if(!empty($filters)){
					foreach($filters as $key=>$val){
						$this->conds[] = $key." IN(".implode(",",$val).")";
					}
				}else{
					if(!empty($this->filters)){
						foreach($this->filters as $column=>$val){
							if(!empty($val['in'])){
								$this->conds[] = $val['id']." IN(".implode(",",$val['in']).")";
							}
						}
					}
				}
			}else{
				foreach($this->filters as $filter){
					$filter['in'] = array();
				}
			}
			//$this->conds = array();
			//$this->orders = array();
			return $this->select();
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("idfull = ".$idrecord);
			//$this->orders = array();
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $data)
		{
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("idfull = ".$idrecord);
			//$this->joins = "";
			
			return $this->update($data);
		}
		
		public function m_delete($idrecord)
		{
			return $this->delete($idrecord);
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : $this->fields = "idfull AS id, ful_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_function()
		{
			$query = "
			SELECT 
			FROM veh_full
			WHERE 
			GROUP BY 
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function __destruct()
		{
		}
	}