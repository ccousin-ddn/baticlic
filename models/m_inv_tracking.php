<?php
/**
*** 07-2025@SOLUfile SRL 
**/
	require_once 'model_crud.php';
	
	class inv_tracking_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "inv_tracking";
			$this->key			= "idtracking";
			$this->duplicateKey = false;
			
			$this->fields 		= "inv_tracking.*, usr_firstname";
			
			$this->joins 		= "
				LEFT JOIN usr_user USING (iduser)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("tra_date DESC");
			
			$this->unset		= array("idparent","usr_firstname");
			$this->unclean		= array();
			$this->linkTable	= "";
			
			/*** new from ***/
			$this->fieldName	= "tra_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				
				"idinvoice"=>0,
				"iduser"=>$_SESSION['iduser'],
				"tra_action"=>"",
				"tra_date"=>date("Y-m-d"),
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
			$this->conds = array("inv_tracking.idtracking = ".$idrecord);
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
			$this->conds = array("inv_tracking.idtracking = ".$idrecord);
			//$this->joins = "";
			$data['iduser'] = $_SESSION['iduser'];
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
				case 1 : $this->fields = "inv_tracking.idtracking AS id, tra_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_function()
		{
			$query = "
			SELECT 
			FROM inv_tracking
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