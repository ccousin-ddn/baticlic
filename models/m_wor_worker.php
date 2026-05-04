<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class wor_worker_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "wor_worker";
			$this->key			= "idworker";
			$this->duplicateKey = false;
			
			$this->fields 		= "wor_worker.*, idshop, age_name";
			
			$this->joins 		= "
				LEFT JOIN sho_shop ON sho_shop.idworker = wor_worker.idworker AND sho_type = 4
				LEFT JOIN age_agency USING (idagency)
			";
			$this->conds 		= array();
			$this->groups 		= array("wor_worker.idworker","idshop");
			$this->orders 		= array("wor_name");
			$this->unset		= array("wor_new_password");
			$this->fieldName	= "wor_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			$this->filters['wor_type'] = array("id"=>"wor_type","in"=>array(1,3),"label"=>"Type");
			$this->filters['wor_state'] = array("id"=>"wor_state","in"=>array(1),"label"=>"Actif");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idworker"=>0,
				"wor_name"=>null,
				"wor_state"=>1,
				"wor_type"=>1,
				"wor_rate"=>0,
				"wor_login"=>null,
				"wor_color"=>"rgba(0, 0, 0, 0.5)",
				"wor_password"=>null,
				"wor_remark"=>"",
				"idshop"=>0
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
			$this->conds = array("wor_worker.idworker = ".$idrecord);
			//$this->orders = array();
			//$this->debugging = true;
			
			return $this->select();
		}
		
		public function m_insert($dataSent)
		{
			parse_str($dataSent, $data);
			if(!empty($data['wor_new_password'])){
				$data['wor_password'] = password_hash($data['wor_new_password'], PASSWORD_DEFAULT);
			}
			//$this->debugging = true;
			
			return $this->insert($data);
		}
		
		public function m_update($idrecord, $dataSent)
		{
			//$this->joins = "";
			$this->conds = array("wor_worker.idworker = ".$idrecord);
			parse_str($dataSent, $data);
			if(!empty($data['wor_new_password'])){
				$data['wor_password'] = password_hash($data['wor_new_password'], PASSWORD_DEFAULT);
			}
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
				case 1 : $this->fields = "wor_worker.idworker AS id, wor_name AS val, wor_login AS tokens"; $this->conds = array("wor_type IN (1,2,3)","wor_state=1"); $this->select(); break;
				case 2 : $this->fields = "wor_worker.idworker AS id, wor_name AS val, wor_login AS tokens"; $this->conds = array("wor_type IN (1,3)","wor_state=1"); $this->select(); break;
				case 3 : $this->fields = "wor_worker.idworker AS id, wor_name AS val, wor_login AS tokens"; $this->conds = array("wor_type=1"); $this->select(); break;
				case 4 : $this->fields = "wor_worker.idworker AS id, wor_name AS val, wor_login AS tokens"; $this->conds = array("wor_type IN (1,2,3)"); $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}