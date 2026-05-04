<?php
/**
*** Juillet 2021@SoluFile 
**/
	require_once 'model_crud.php';
	
	class pay_payment_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "pay_payment";
			$this->key			= "idpayment";
			$this->duplicateKey = false;
			
			$this->fields 		= "pay_payment.*";
			
			$this->joins 		= "
				
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array();
			$this->unset		= array("idparent");
			$this->unclean		= array();
			
			/*** new from ***/
			$this->fieldName	= "pay_name";
			
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
				"pay_date"=>date("Y-m-d"),
				"pay_amount"=>0,
				"pay_type"=>1,
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
			$this->conds = array("idpayment = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			//$this->debugging = true;
			$data['pay_amount'] = floatval(str_replace(",",".",$data['pay_amount']));
			$this->insert($data);
			
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($this->result['idparent'], $data);
			}
			
			return true;
		}
		
		public function m_update($idrecord, $data)
		{
			$data['pay_amount'] = floatval(str_replace(",",".",$data['pay_amount']));
			// *** lnk ***
			if(!empty($this->lnk)){
				$this->lnkProcess($idrecord, $data);
			}
			
			// *** update table ***
			$this->conds = array("idpayment = ".$idrecord);
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
				case 1 : $this->fields = "idpayment AS id, pay_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_function()
		{
			$query = "
			SELECT 
			FROM pay_payment
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