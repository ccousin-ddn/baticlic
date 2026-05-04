<?php
/**
*** Juillet 2022@SoluFile 
**/
	require_once 'model_crud.php';
	
	class veh_maintenance_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "veh_maintenance";
			$this->key			= "idmaintenance";
			$this->duplicateKey = false;
			
			$this->fields 		= "veh_maintenance.*, CONCAT_WS(' ',veh_model,veh_numberplate) AS veh_name";
			
			$this->joins 		= "
				LEFT JOIN veh_vehicle USING (idvehicle)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("mai_date DESC");
			
			$this->unset		= array("idparent");
			$this->unclean		= array();
			
			/*** new from ***/
			$this->fieldName	= "mai_name";
			
			/*** Table filtering ***/
			$this->filters['mai_done'] = array("id"=>"mai_done","in"=>array(0),"label"=>"Fait");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idmaintenance"=>0,
				"idvehicle"=>0,
				"mai_date"=>date("Y-m-d"),
				"mai_mileage"=>0,
				"mai_done"=>0,
				"mai_date_done"=>"",
				"mai_actual_mileage"=>0,
				"mai_remark"=>"",
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
			$this->conds = array("idmaintenance = ".$idrecord);
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
			$this->conds = array("idmaintenance = ".$idrecord);
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
				case 1 : $this->fields = "idmaintenance AS id, mai_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_createNext($idrecord)
		{
			$query = "
			INSERT INTO veh_maintenance (idvehicle,mai_date,mai_mileage,mai_done) 
			SELECT idvehicle, DATE_ADD(mai_date_done, INTERVAL 6 MONTH), (mai_mileage+7500), 0 
			FROM veh_maintenance 
			WHERE idmaintenance = $idrecord
			";
			$this->executeQuery($query);
			$this->result['newId'] = $this->result['idparent'];
			
			return true;
		}
		
		public function m_function()
		{
			$query = "
			SELECT 
			FROM veh_maintenance
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