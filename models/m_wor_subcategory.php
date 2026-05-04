<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class wor_subcategory_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "wor_subcategory";
			$this->key			= "idworsubcategory";
			$this->duplicateKey = false;
			
			$this->fields 		= "wor_subcategory.*, CONCAT(cat_code,' ',cat_name) AS cat_name, CONCAT(cat_name,' ',subcat_name) AS sub_name";
			
			$this->joins 		= "
				LEFT JOIN wor_category USING(idworcategory)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("cat_code, subcat_code");
			$this->fieldName	= "subcat_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			
			/*** Select filtering ***/
			$this->idparent		= "idworcategory";
		}

		public function m_newRecord($insert=false)
		{
			$this->values = (object) array(
				"idworsubcategory"=>0,
				"idworcategory"=>0,
				"subcat_name"=>null,
				
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
			$this->conds = array("idworsubcategory = ".$idrecord);
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
			$this->conds = array("idworsubcategory = ".$idrecord);
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
				case 1 : $this->fields = "idworsubcategory AS id, CONCAT(subcat_code,' ',subcat_name) AS val, '' AS tokens"; $this->select(); break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}