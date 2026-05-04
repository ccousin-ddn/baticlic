<?php
/**
*** Octobre 2021@SoluFile 
**/
	require_once 'model_crud.php';
	
	class pla_planning_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "pla_planning";
			$this->key			= "idplanning";
			$this->duplicateKey = false;
			
			$this->fields 		= "pla_planning.*, wor_name, veh_num";
			
			$this->joins 		= "
				LEFT JOIN wor_worker USING (idworker)
				LEFT JOIN veh_vehicle USING (idvehicle)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array();
			
			$this->unset		= array("idparent", "wor_name");
			$this->unclean		= array();
			
			/*** new from ***/
			$this->fieldName	= "pla_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idplanning"=>0,
				"idworker"=>$this->dataSent['idworker']??0,
				"idjob"=>0,
				"pla_date_begin"=>$this->dataSent['date']??date("Y-m-d"),
				"pla_date_end"=>$this->dataSent['date']??date("Y-m-d"),
				
			);
			$this->values = (object) $data;
			if($insert){
				return $this->insert($data);
			}else{
				return true;
			}
		}
		
		public function m_getById($idrecord)
		{
			$this->conds = array("idplanning = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			//$this->debugging = true;
			
			$begin = new DateTime($data['pla_date_begin']);
			$end = new DateTime($data['pla_date_end']);
			$end->modify('+1 day');
			
			$query = "
			INSERT INTO pla_planning (idworker, idjob, idvehicle, idabstype, pla_date_begin, pla_date_end)
			VALUES
			";
			
			$diff = date_diff($begin,$end);
			//var_dump($begin, $end, $diff->format("%a"), $begin->format('N'));
			while ($diff->format("%a") > 0){
				if($begin->format('N')<6){ // not during week-end
					$query .= "(".$data['idworker'].", ".($data['idjob']>0?$data['idjob']:"NULL").", ".($data['idvehicle']>0?$data['idvehicle']:"NULL").", ".($data['idabstype']>0?$data['idabstype']:"NULL").", ".$begin->format("Ymd").", ".$begin->format("Ymd")."),";
				}else{
					if($diff->format("%a") == 1){
						$query .= "(".$data['idworker'].", ".($data['idjob']>0?$data['idjob']:"NULL").", ".($data['idvehicle']>0?$data['idvehicle']:"NULL").", ".($data['idabstype']>0?$data['idabstype']:"NULL").", ".$begin->format("Ymd").", ".$begin->format("Ymd")."),";
					}
				}
				$begin->modify('+1 day');
				$diff = date_diff($begin,$end);
			}
			$query = substr($query, 0, strlen($query)-1);
			//dump($query);
			
			if($this->executeQuery($query)){
				return true;
			}else{
				return false;
			}
		}
		
		public function m_update($idrecord, $data)
		{
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("idplanning = ".$idrecord);
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
				case 1 : $this->fields = "idplanning AS id, pla_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_function()
		{
			$query = "
			SELECT 
			FROM pla_planning
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