<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class sup_supplier_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "sup_supplier";
			$this->key			= "idsupplier";
			$this->duplicateKey = false;
			
			$this->fields 		= "sup_supplier.*";
			
			$this->joins 		= "
				
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("sup_name");
			$this->fieldName	= "sup_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['met_metier'] = array("id"=>"idmetier","in"=>array(),"label"=>"Métiers");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idsupplier"=>0,
				"sup_name"=>null,
				"sup_short_name"=>null,
				"sup_contact"=>null,
				"sup_address_1"=>null,
				"sup_address_2"=>null,
				"sup_cp"=>null,
				"sup_city"=>null,
				"sup_siret"=>null,
				"sup_tva"=>null,
				"sup_phone"=>null,
				"sup_fax"=>null,
				"sup_email"=>null,
				"sup_mobile"=>null,
				"sup_url"=>null,
				"sup_skills"=>null,
				"sup_login"=>null,
				"sup_password"=>null,
				"sup_rate_file"=>null,
				"sup_remark"=>"",
				
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
						//dump($key,$val);
						if($key == "idmetier"){
							foreach($val as $v){
								$this->conds[] = "FIND_IN_SET('".$v."',idmetier)";
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
			$this->conds = array("idsupplier = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($data)
		{
			$data['idmetier'] = implode(",", $data['idmetier']??[]);
			//$this->debugging = true;
			
			return $this->insert($data);
		}
		
		public function m_update($idrecord, $data)
		{
			//$this->joins = "";
			$this->conds = array("idsupplier = ".$idrecord);
			$data['idmetier'] = implode(",", $data['idmetier']??[]);
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
				case 1 : $this->fields = "idsupplier AS id, COALESCE(sup_short_name,sup_name) AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}