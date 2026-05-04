<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class sho_shop_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "sho_shop";
			$this->key			= "idshop";
			$this->duplicateKey = false;
			
			$this->fields 		= "sho_shop.*, CONCAT(usr_firstname,' ',usr_lastname) AS wor_name, COALESCE(SUM(sto_quantity),0) AS tot_qty, COALESCE(SUM(sto_quantity * art_price),0) AS tot_amount";
			
			$this->joins 		= "
				LEFT JOIN usr_user ON sho_shop.idworker = usr_user.iduser 
				LEFT JOIN sto_stock USING (idshop) 
				LEFT JOIN art_article USING (idarticle) 
			";
			$this->conds 		= array();
			$this->groups 		= array("idshop");
			$this->orders 		= array("sho_name");
			$this->fieldName	= "sho_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['sho_type'] = array("id"=>"sho_type","in"=>array(),"label"=>"Type");
			$this->filters['wor_worker'] = array("id"=>"idworker","in"=>array(),"label"=>"Responsable");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idshop"=>0,
				"idworker"=>0,
				"sho_type"=>0,
				"sho_name"=>null,
				"sho_remark"=>"",
				
				);
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
			$this->conds = array("idshop = ".$idrecord);
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
			$this->conds = array("idshop = ".$idrecord);
			parse_str($dataSent, $data);
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
				case 1 : $this->fields = "idshop AS id, sho_name AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}