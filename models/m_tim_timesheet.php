<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class tim_timesheet_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "tim_timesheet";
			$this->key			= "idtimesheet";
			$this->duplicateKey = false;
			
			$this->fields 		= "tim_timesheet.*, wor_name, wor_picture, tas_name, job_job.idjob, COALESCE(job_surname, job_name) AS job_name, sit_site.idsite, sit_name, cli_client.idclient, cli_name";
			
			$this->joins 		= "
				LEFT JOIN wor_worker USING (idworker) 
				LEFT JOIN tas_task USING (idtask) 
				LEFT JOIN job_job USING (idjob) 
				LEFT JOIN cli_client USING (idclient) 
				LEFT JOIN sit_site USING (idsite) 
				";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("tim_date DESC, wor_name, tas_name");
			$this->unset		= array("idclient","idsite","idjob");
			$this->fieldName	= "";
			
			/*** Table filtering ***/
			/*** Table filtering ***/
			$this->filters['wor_worker'] = array("id"=>"tim_timesheet.idworker","in"=>array(),"label"=>"Compagnon");
			$this->filters['tim_year'] = array("id"=>"idyear","in"=>array(date("Y")),"label"=>"Année");
			$this->filters['tim_week'] = array("id"=>"idweek","in"=>array(date("W")),"label"=>"Semaine");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				//"idtimesheet"=>0,
				"idworker"=>0,
				"idtask"=>0,
				"tim_date"=>date("Y-m-d"),
				"tim_duration"=>"04:00:00",
				"tim_validated_by"=>null,
				"tim_validated_date"=>"",
				"tim_edited_by"=>null,
				"tim_edited_date"=>"",
				"tim_remark"=>"",
				
				);
			if($insert){
				return $this->insert((array) $this->values);
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
								$this->conds[] = "YEAR(tim_date) IN (".implode(",",$val).")";
							}else{
								$this->conds[] = "YEAR(tim_date) = ".$val;
							}
							//$this->dataSent["idyear"][0] = $val;
						}elseif($key == "idweek"){
							if(is_array($val)){
								$this->conds[] = "WEEK(tim_date, 3) IN (".implode(",",$val).")";
							}else{
								$this->conds[] = "WEEK(tim_date, 3) = ".$val;
							}
						}else{
							$this->conds[] = $key." IN(".implode(",",$val).")";
						}
					}
				}else{
					if(!empty($this->filters)){
						foreach($this->filters as $column=>$val){
							if(!empty($val['in'])){
								if($val['id'] == "idyear"){
									$val['id'] = "YEAR(tim_date)";
								}
								if($val['id'] == "idweek"){
									$val['id'] = "WEEK(tim_date, 3)";
								}
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
			$this->conds = array("idtimesheet = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			return $this->insert($data);
		}
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("idtimesheet = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			unset($data["idclient"]);
			unset($data["idsite"]);
			unset($data["idjob"]);
			return $this->update($data);
		}
		
		public function m_duplicate($idrecord)
		{
			$query = "
			INSERT INTO tim_timesheet (idworker, idtask, tim_date, tim_duration)
			SELECT idworker, idtask, tim_date, tim_duration
			FROM tim_timesheet
			WHERE idtimesheet = $idrecord
			";
			$this->executeQuery($query);
			$this->result['newId'] = $this->result['idparent'];
			
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
				case 1 : $this->fields = "idtimesheet AS id, tim_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}