<?php
/**
*** Novembre 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class bil_biller_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "bil_biller";
			$this->key			= "idbiller";
			$this->duplicateKey = false;
			
			$this->fields 		= "bil_biller.*, sup_name, CONCAT(val1.usr_firstname,' ',val1.usr_lastname) AS val1_name, CONCAT(val2.usr_firstname,' ',val2.usr_lastname) AS val2_name, CONCAT(val3.usr_firstname,' ',val3.usr_lastname) AS val3_name";
			
			$this->joins 		= "
				LEFT JOIN sup_supplier USING(idsupplier)
				LEFT JOIN usr_user AS val1 ON val1.iduser = bil_validation_1
				LEFT JOIN usr_user AS val2 ON val2.iduser = bil_validation_2
				LEFT JOIN usr_user AS val3 ON val3.iduser = bil_validation_3
			";
			$this->conds 		= array($_SESSION['usr_level']<2?"bil_visible = 1":"");
			$this->groups 		= array();
			$this->orders 		= array("bil_date DESC");
			$this->unset		= array("bre_tot","val1_name");
			
			$this->fieldName	= "bil_name";
			
			/*** Table filtering ***/
			$this->filters['bil_status'] = array("id"=>"bil_status","in"=>array(1),"label"=>"Statut");
			$this->filters['bil_paid'] = array("id"=>"bil_date_payment","in"=>array(),"label"=>"Etat");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idbiller"=>0,
				"idsupplier"=>0,
				"bil_date"=>date("Y-m-d"),
				"bil_ref"=>null,
				"bil_description"=>null,
				"bil_amount"=>0,
				"bil_expenses"=>0,
				"bil_ecotax"=>0,
				"bil_total"=>0,
				"bil_total_to_pay"=>0,
				"bil_deadline"=>null,
				"bil_date_payment"=>null,
				"bil_status"=>1,
				"bil_visible"=>1,
				"idtypepayment"=>0,
				"bil_info_payment"=>null,
				"bil_account_number"=>null,
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
						//dump($key,$val[]);
						if($key == "bil_date_payment"){
							if(count($val) == 1){
								if(in_array("1", $val)){
									$this->conds[] = "bil_date_payment IS NOT NULL";
								}
								if(in_array("2", $val)){
									$this->conds[] = "bil_date_payment IS NULL";
								}
							}
						}else{
							$this->conds[] = $key." IN(".implode(",",$val).")";
						}
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
			$this->conds = array("idbiller = ".$idrecord);
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
			$this->conds = array("idbiller = ".$idrecord);
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
				case 1 : $this->fields = "idbiller AS id, bil_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function m_duplicate($idrecord)
		{
			// bil_biller CURRENT_DATE()
			$query = "
			INSERT INTO bil_biller (idsupplier, bil_date, bil_ref, bil_description, bil_amount, bil_expenses, bil_ecotax, bil_total, bil_total_to_pay, bil_deadline, bil_date_payment, bil_status, bil_visible, idtypepayment, bil_info_payment)
			SELECT idsupplier, DATE_ADD(bil_date, INTERVAL 1 MONTH), bil_ref, bil_description, bil_amount, bil_expenses, bil_ecotax, bil_total, bil_total_to_pay, DATE_ADD(bil_deadline, INTERVAL 1 MONTH), DATE_ADD(bil_date_payment, INTERVAL 1 MONTH), bil_status, bil_visible, idtypepayment, bil_info_payment
			FROM bil_biller
			WHERE idbiller = $idrecord
			";
			$this->executeQuery($query);
			$this->result['newId'] = $this->result['idparent'];
			
			// bil_breakdown
			$query = "
			INSERT INTO bil_breakdown (idbiller, bre_amount, idsuporder, idaccountlev1, idaccountlev2, idaccountlev3)
			SELECT ".$this->result['newId'].", bre_amount, idsuporder, idaccountlev1, idaccountlev2, idaccountlev3
			FROM bil_breakdown
			WHERE idbiller = $idrecord
			";
			$this->executeQuery($query);
			
			return true;
		}
		
		public function __destruct()
		{
		}
	}