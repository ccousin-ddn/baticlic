<?php
/**
*** 08-2025@SOLUfile SRL 
**/
	require_once 'model_crud.php';
	
	class inv_line_retention_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "inv_line_retention";
			$this->key			= "idline";
			$this->duplicateKey = false;
			
			$this->fields 		= "inv_line_retention.*, ret_name";
			
			$this->joins 		= "
				LEFT JOIN ret_retention USING (idretention)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array();
			
			$this->unset		= array("idparent");
			$this->unclean		= array();
			$this->linkTable	= "";
			
			/*** new from ***/
			$this->fieldName	= "lin_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idline"=>0,
				"idinvoice"=>0,
				"idretention"=>0,
				"ret_percent"=>0,
				"ret_total"=>0,
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
			$this->conds = array("inv_line_retention.idline = ".$idrecord);
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
			$this->conds = array("inv_line_retention.idline = ".$idrecord);
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
				case 1 : $this->fields = "inv_line_retention.idline AS id, lin_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_getTotalByInvoice($idinvoice)
		{
			$query = "
			SELECT SUM(ret_total) as tot_ret
			FROM inv_line_retention
			WHERE idinvoice = $idinvoice
			GROUP BY idinvoice
			";
			//$this->debugging = true;
			if($this->executeQuery($query)){
				return $this->values[0]['tot_ret']??0;
			}else{
				return false;
			}
		}
		
		public function __destruct()
		{
		}
	}