<?php
/**
*** Novembre 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class bil_breakdown_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "bil_breakdown";
			$this->key			= "idbreakdown";
			$this->duplicateKey = false;
			
			$this->fields 		= "bil_breakdown.*, ord_ref, ord_sup_ref, ord_title, CONCAT_WS(' ',ord_ref, ord_title) AS ord_name, bil_account_lev1.acc_name AS lev1_name, bil_account_lev2.acc_name AS lev2_name, bil_account_lev3.acc_name AS lev3_name";
			
			$this->joins 		= "
				LEFT JOIN sup_order USING(idsuporder) 
				LEFT JOIN bil_account_lev1 USING(idaccountlev1) 
				LEFT JOIN bil_account_lev2 USING(idaccountlev2) 
				LEFT JOIN bil_account_lev3 USING(idaccountlev3) 
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("idbreakdown");
			$this->unset		= array("ord_name");
			
			$this->fieldName	= "bre_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn","in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idbreakdown"=>0,
				"idbiller"=>0,
				"bre_amount"=>0,
				"idsuporder"=>0,
				"idaccountlev1"=>0,
				"idaccountlev2"=>0,
				"idaccountlev3"=>0,
				"ord_name"=>""
			);
			$this->values = (object) $data;
			if($insert){
				return $this->insert($this->values);
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
			$this->conds = array("idbreakdown = ".$idrecord);
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
			$this->conds = array("idbreakdown = ".$idrecord);
			parse_str($dataSent, $data);
			//$this->debugging = true;
			unset($data['idparent']);
			
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
				case 1 : $this->fields = "idbreakdown AS id, bre_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}