<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class cli_contact_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "cli_contact";
			$this->key			= "idclicontact";
			$this->duplicateKey = false;
			
			$this->fields 		= "cli_contact.*, typ_name, age_name";
			
			$this->joins 		= "
				LEFT JOIN con_type USING(idcontype)
				LEFT JOIN cli_agency USING(idcliagency)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("con_name");
			$this->fieldName	= "con_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			$this->idparent		= "idclient";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idclicontact"=>0,
				"idclient"=>0,
				"idcontype"=>0,
				"con_name"=>null,
				"con_email"=>null,
				"con_mobile"=>null,
				"con_phone"=>null,
				"con_phone_perso"=>null,
				"con_remark"=>null,
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
			$this->conds = array("idclicontact = ".$idrecord);
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
			$this->conds = array("idclicontact = ".$idrecord);
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
				case 1 : $this->fields = "idclicontact AS id, con_name AS val, '' AS tokens"; $this->select(); break;
				case 2 : $this->fields = "idclicontact AS id, CONCAT_WS(' - ',con_name,typ_name,age_name) AS val, '' AS tokens"; 
					$this->joins = "LEFT JOIN con_type USING(idcontype) LEFT JOIN (SELECT idcliagency, age_name FROM cli_agency) AS age USING (idcliagency)";
					$this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}