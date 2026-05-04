<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once 'model_crud.php';
	
	class lib_work_model extends crud {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->table 		= "lib_work";
			$this->key			= "idwork";
			$this->duplicateKey = false;
			
			$this->fields 		= "lib_work.*, lib_name, CONCAT(cat_code,' ',cat_name) AS cat_name, CONCAT(subcat_code,' ',subcat_name) AS subcat_name";
			
			$this->joins 		= "
				LEFT JOIN lib_library USING(idlibrary) 
				LEFT JOIN wor_category USING(idworcategory) 
				LEFT JOIN wor_subcategory USING(idworsubcategory)
			";
			$this->conds 		= array();
			$this->groups 		= array();
			$this->orders 		= array("cat_code, subcat_code, wor_code");
			$this->fieldName	= "wor_name";
			
			/*** Table filtering ***/
			$this->filters		= array();
			//ex : $this->filters['arrayName'] = array("id"=>"idColumn",in"=>array(0),"label"=>"label");
			$this->filters['wor_category'] = array("id"=>"lib_work.idworcategory","in"=>array(),"label"=>"Catégorie");
			$this->filters['lib_library'] = array("id"=>"lib_work.idlibrary","in"=>array(0),"label"=>"Bibliothèque");
			
			/*** Select filtering ***/
			//$this->idparent		= "";
		}

		public function m_newRecord($insert=false)
		{
			$data = array(
				"idwork"=>0,
				"idlibrary"=>0,
				"idworcategory"=>0,
				"idworsubcategory"=>0,
				"wor_code"=>null,
				"wor_name"=>null,
				"wor_description"=>null,
				"wor_unit"=>null,
				"wor_rate_small"=>0,
				"wor_rate_medium"=>0,
				"wor_rate_large"=>0,
				"wor_remark"=>"",
				
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
			$this->conds = array("idwork = ".$idrecord);
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
			$this->conds = array("idwork = ".$idrecord);
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
				case 1 : $this->fields = "idwork AS id, wor_name AS val, '' AS tokens"; $this->select(); break;
				case 2 : 
					$query = "
					SELECT (SELECT cli_rate_level FROM cli_client WHERE idclient = ".IDCLIENT.") AS cli_rate_level, idwork AS id, 
					CONCAT(cat_code,' ',cat_name) AS level1, CONCAT(subcat_code,' ',subcat_name) AS level2, CONCAT_WS(' ',wor_code,wor_name) AS level3, wor_name AS val, 
					COALESCE(wor_description, wor_rate_medium) AS subtext, CONCAT_WS('', cat_code, subcat_code) AS lev_name, 
					IF((SELECT cli_rate_level FROM cli_client WHERE idclient = ".IDCLIENT.") = 1, wor_rate_small, IF((SELECT cli_rate_level FROM cli_client WHERE idclient = ".IDCLIENT.") = 2, wor_rate_medium, wor_rate_large)) AS wor_rate, wor_unit 
					FROM lib_work 
					LEFT JOIN wor_category USING(idworcategory) 
					LEFT JOIN wor_subcategory USING(idworsubcategory) 
					WHERE idlibrary = ".idlibrary." 
					ORDER BY cat_code, subcat_code, wor_code, wor_name
					";
					$this->executeQuery($query);
					break;
				case 3 : 
					$query = "
					SELECT (SELECT cli_rate_level FROM cli_client WHERE idclient = ".IDCLIENT.") AS cli_rate_level, idwork AS id, 
					CONCAT(cat_code,' ',cat_name) AS level1, CONCAT(subcat_code,' ',subcat_name) AS level2, CONCAT_WS(' ',wor_code,wor_name) AS level3, wor_name AS val, 
					COALESCE(wor_name, wor_rate_medium) AS subtext, CONCAT_WS('', cat_code, subcat_code) AS lev_name, 
					IF((SELECT cli_rate_level FROM cli_client WHERE idclient = ".IDCLIENT.") = 1, wor_rate_small, IF((SELECT cli_rate_level FROM cli_client WHERE idclient = ".IDCLIENT.") = 2, wor_rate_medium, wor_rate_large)) AS wor_rate, wor_unit 
					FROM lib_work 
					LEFT JOIN wor_category USING(idworcategory) 
					LEFT JOIN wor_subcategory USING(idworsubcategory) 
					WHERE idcontract = (SELECT idcontract FROM job_job WHERE idjob = ".IDJOB.")
					ORDER BY cat_code, subcat_code, wor_code, wor_name
					";
					//$this->debugging = true;
					$this->executeQuery($query);
					break;
			}
			return true;
		}
		
		public function __destruct()
		{
		}
	}