<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class tas_task_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "tas_task";
			$this->key			= "idtask";
			$this->duplicateKey = false;
			
			$this->fields 		= "tas_task.*, tas_duration, GROUP_CONCAT(lnk_task_worker.idworker) AS idworker";
			
			$this->joins 		= "
				LEFT JOIN lnk_task_worker USING(idtask)
			";
			$this->conds 		= array();
			$this->groups 		= array("idtask");
			$this->orders 		= array("tas_name");
			$this->fieldName	= "tas_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			$this->idparent		= "idjob";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idtask"=>0,
				"idjob"=>decrypt($this->idrecord)??0,
				"idtastype"=>0,
				"tas_name"=>null,
				"tas_status"=>2,
				"tas_date_begin"=>date("Y-m-d"),
				"tas_date_end"=>null,
				"tas_duration_days"=>null,
				"tas_duration"=>0,
				"tas_deadline"=>null,
				"tas_order"=>0,
				"tas_block"=>0,
				"tas_remark"=>"",
				
				);
			$this->values = $data;
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
			$this->conds = array("idtask = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			//$this->debugging = true;
			
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $dataSent)
		{
			parse_str($dataSent, $data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("idtask = ".$idrecord);
			//$this->joins = "";
			//$this->debugging = true;
			
			return $this->update($data);
		}
		
		public function m_updateOrder()
		{
			//var_dump($this->dataSent); exit;
			$sign = "-";
			if($this->dataSent['oldIndex'] > $this->dataSent['newIndex']){$sign = "+";}
			$query = "
			UPDATE tas_task SET 
			tas_order = tas_order $sign 1 
			WHERE idjob = ".decrypt($this->dataSent['idparent'])." 
			AND tas_order BETWEEN ".min($this->dataSent['newIndex'], $this->dataSent['oldIndex'])." AND ".max($this->dataSent['newIndex'], $this->dataSent['oldIndex']).";
			UPDATE tas_task SET tas_order = ".$this->dataSent['newIndex']." WHERE idtask = ".decrypt($this->idrecord)."
			";
			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function m_delete($idrecord)
		{
			//$this->debugging = true;
			$query = "
			SELECT @idjob := idjob, @tas_order := tas_order
			FROM tas_task WHERE idtask = $idrecord;
			UPDATE tas_task
			SET tas_order = tas_order - 1 WHERE idjob = @idjob AND tas_order > @tas_order;
			DELETE FROM tas_task WHERE idtask = $idrecord;
			";
			
			return $this->executeQuery($query);
		}
		
		public function m_createTimesheet($idworker, $idtask)
		{
			parse_str($this->dataSent, $data);
			$query = "
			INSERT INTO tim_timesheet (idworker, idtask, tim_date, tim_duration)
			SELECT $idworker, $idtask, CURRENT_DATE(), '".$data['tim_duration']."'
			";
			//$this->debugging = true;
			
			return $this->executeQuery($query);
		}
		
		public function m_getList($id, $conds=array())
		{
			$this->conds = $conds;
			switch($id){
				case 1 : $this->fields = "idtask AS id, tas_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}