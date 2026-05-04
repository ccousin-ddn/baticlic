<?php
/**
*** Juillet 2022@SoluFile 
**/
	require_once 'model_crud.php';
	
	class veh_control_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "veh_control";
			$this->key			= "idcontrol";
			$this->duplicateKey = false;
			
			$this->fields 		= "veh_control.*, CONCAT_WS(' ',veh_model,veh_numberplate) AS veh_name";
			
			$this->joins 		= "
				LEFT JOIN veh_vehicle USING (idvehicle)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("con_date DESC");
			
			$this->unset		= array("idparent");
			$this->unclean		= array();
			
			/*** new from ***/
			$this->fieldName	= "con_name";
			
			/*** Table filtering ***/
			$this->filters['con_type'] = array("id"=>"con_type","in"=>array(1,2),"label"=>"Type");
			$this->filters['con_done'] = array("id"=>"con_done","in"=>array(0),"label"=>"Fait");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idcontrol"=>0,
				"idvehicle"=>0,
				"con_date"=>date("Y-m-d"),
				"con_mileage"=>0,
				"con_type"=>0,
				"con_date_done"=>null,
				"con_remark"=>"",
				
				
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
			$this->conds = array("idcontrol = ".$idrecord);
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
			$this->conds = array("idcontrol = ".$idrecord);
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
				case 1 : $this->fields = "idcontrol AS id, con_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_createNext($idrecord)
		{
			$query = "
			INSERT INTO veh_control (idvehicle,con_date,con_type,con_done) 
			SELECT idvehicle, DATE_ADD(con_date_done, INTERVAL (IF (con_type = 1, 2, 1)) YEAR), con_type, 0 
			FROM veh_control 
			WHERE idcontrol = $idrecord
			";
			$this->executeQuery($query);
			$this->result['newId'] = $this->result['idparent'];
			
			return true;
		}
		
		public function m_function()
		{
			$query = "
			SELECT 
			FROM veh_control
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