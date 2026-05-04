<?php
/**
*** Août 2021@SoluFile 
**/
	require_once 'model_crud.php';
	
	class veh_vehicle_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "veh_vehicle";
			$this->key			= "idvehicle";
			$this->duplicateKey = false;
			
			$this->fields 		= "veh_vehicle.*, veh_brand.*, COALESCE(COUNT(idarticle),0) AS tot_qty";
			
			$this->joins 		= "
				LEFT JOIN veh_brand USING (idvehbrand) 
				LEFT JOIN (SELECT idarticle, idvehicle FROM lnk_vehicle_article WHERE art_qty > 0) AS stock ON stock.idvehicle = veh_vehicle.idvehicle 
			";
			$this->conds 		= array();
			$this->groups 		= array("veh_vehicle.idvehicle");
			$this->orders 		= array("veh_num");
			
			$this->unset		= array("idparent");
			$this->unclean		= array();
			
			/*** new from ***/
			$this->fieldName	= "veh_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idvehicle"=>0,
				"veh_type"=>0,
				"veh_state"=>1,
				"veh_brand"=>null,
				"veh_model"=>null,
				"veh_numberplate"=>null,
				"veh_hourly_rate"=>0,
				
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
			$this->conds = array("veh_vehicle.idvehicle = ".$idrecord);
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
			$this->conds = array("idvehicle = ".$idrecord);
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
				case 1 : $this->fields = "idvehicle AS id, veh_name AS val, '' AS tokens"; $this->select(); break;
				case 2 : $this->fields = "veh_vehicle.idvehicle AS id, CONCAT_WS(' ',veh_model,veh_numberplate) AS val, '' AS tokens"; $this->select(); break;
				case 3 : $this->fields = "veh_vehicle.idvehicle AS id, CONCAT_WS(' | ',veh_num,veh_numberplate) AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getByPlanning()
		{
			$query = "
			SELECT idvehicle, veh_type, veh_num, veh_model, veh_numberplate, bra_logo
			FROM veh_vehicle
			LEFT JOIN veh_brand USING(idvehbrand)
			LEFT JOIN pla_planning USING (idvehicle)
			WHERE idworker = ".$_SESSION['iduser']." AND CURDATE() BETWEEN pla_date_begin AND pla_date_end
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