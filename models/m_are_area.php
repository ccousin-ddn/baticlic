<?php
/**
*** Juillet 2021@SoluFile 
**/
	require_once 'model_crud.php';
	
	class are_area_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "are_area";
			$this->key			= "idarea";
			$this->duplicateKey = false;
			
			$this->fields 		= "are_area.*";
			
			$this->joins 		= "
				
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array();
			$this->unset		= array();
			$this->unclean		= array();
			
			/*** new from ***/
			$this->fieldName	= "are_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idarea"=>0,
				"are_name"=>null,
				"are_amount"=>0,
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
			//$this->debugging = true;

			return $this->select();
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("idarea = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			//$this->debugging = true;
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
			$this->conds = array("idarea = ".$idrecord);
			//$this->joins = "";
			//$this->debugging = true;
			
			return $this->update($data);
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
				case 1 : $this->fields = "idarea AS id, are_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getByGeo($lat,$lng)
		{
			$cfg = new cfg_config();
			$cfg_lat = $cfg->getInfo("lat");
			$cfg_lng = $cfg->getInfo("lng");
			
			$query = "
			SELECT idarea 
			FROM are_area 
			WHERE ROUND(6371*ACOS(COS(RADIANS(".$cfg_lat."))*COS(RADIANS(".$lat."))*COS(RADIANS(".$lng.")-RADIANS(".$cfg_lng."))+SIN(RADIANS(".$cfg_lat."))* SIN(RADIANS(".$lat."))),2)  
			BETWEEN are_min AND are_max
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